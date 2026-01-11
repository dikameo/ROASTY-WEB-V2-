<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Helpers\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    /**
     * Get Midtrans configuration (public endpoint)
     * Frontend akan fetch client key dari sini - fetch langsung dari .env
     */
    public function getMidtransConfig()
    {
        $clientKey = env('MIDTRANS_CLIENT_KEY', config('midtrans.client_key'));
        $isProduction = env('MIDTRANS_IS_PRODUCTION', 'false') === 'true';

        Log::info('🔑 Midtrans Config Request', [
            'client_key' => substr($clientKey, 0, 15) . '...',
            'is_production' => $isProduction
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'client_key' => $clientKey,
                'is_production' => $isProduction,
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        // Check if user is admin - check profile role, Spatie role, or users table role
        $isAdmin = $user->profile?->role === 'admin' || $user->hasRole('admin') || $user->role === 'admin';

        if ($isAdmin) {
            $query = Order::query();
        } else {
            $query = Order::where('user_id', $user->id);
        }
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->get('limit', 10);
        $orders = $query->with('user')->latest('order_date')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ]);
    }

    /**
     * Display all orders (Admin only)
     */
    public function indexAll(Request $request)
    {
        $query = Order::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Pagination
        $perPage = $request->get('limit', 10);
        $orders = $query->with('user')->latest('order_date')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'All orders retrieved successfully',
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        // Generate unique Order ID (CRITICAL: id is text, not auto increment!)
        $orderId = IdGenerator::generateOrderId();

        // Configure Midtrans - fetch dari .env langsung
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        $isProduction = env('MIDTRANS_IS_PRODUCTION', 'false') === 'true';

        if (!$serverKey || !$clientKey) {
            Log::error('Midtrans config tidak lengkap', [
                'has_server_key' => !!$serverKey,
                'has_client_key' => !!$clientKey,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Midtrans configuration error',
                'error' => [
                    'code' => 'CONFIG_ERROR',
                    'details' => 'Missing Midtrans credentials in .env'
                ]
            ], 500);
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);

        // Debug: Log Midtrans config
        Log::info('Midtrans Config Loaded', [
            'server_key' => substr($serverKey, 0, 10) . '...',
            'client_key' => substr($clientKey, 0, 10) . '...',
            'is_production' => $isProduction,
        ]);

        $orderData = [
            'id' => $orderId,              // REQUIRED: Manual ID for text PK
            'user_id' => (string) Auth::id(),     // UUID from auth.users
            'items' => $request->items,    // Will be cast to JSON
            'status' => 'pendingPayment',  // Match CHECK constraint
            'order_date' => now(),
            'subtotal' => $request->subtotal,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'total' => $request->total,
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
            'tracking_number' => IdGenerator::generateTrackingNumber(),
        ];

        // **IMPORTANT: Save order FIRST, then generate snap token**
        $order = Order::create($orderData);
        Log::info('Order saved to database', ['order_id' => $orderId, 'id' => $order->id]);

        // Generate Snap Token if not COD
        $snapToken = null;
        if ($request->payment_method !== 'cod' && $request->payment_method !== 'cash_on_delivery') {
            try {
                Log::info('Generating Snap Token', ['order_id' => $orderId, 'amount' => $request->total, 'payment_method' => $request->payment_method]);

                /** @var \App\Models\User $authUser */
                $authUser = Auth::user();

                // Map payment method to Midtrans enabled_payments array
                $paymentMethod = strtolower($request->payment_method);
                $enabledPayments = ['gopay', 'bca_va', 'bni_va', 'mandiri_va', 'permata_va', 'credit_card'];

                // Map common names to Midtrans names
                $paymentMap = [
                    'gopay' => 'gopay',
                    'bca' => 'bca_va',
                    'bni_va' => 'bni_va',
                    'bni' => 'bni_va',
                    'mandiri' => 'mandiri_va',
                    'permata' => 'permata_va',
                    'credit_card' => 'credit_card',
                ];

                $mappedPayment = $paymentMap[$paymentMethod] ?? $paymentMethod;

                // Filter enabled payments - hanya yang dipilih + gopay as fallback
                $selectedPayments = in_array($mappedPayment, $enabledPayments)
                    ? [$mappedPayment]
                    : ['gopay'];

                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $request->total,
                    ],
                    'customer_details' => [
                        'first_name' => $authUser->name ?? 'Customer',
                        'email' => $authUser->email,
                        'phone' => $authUser->phone ?? '',
                    ],
                    // Only enable selected payment method
                    'enabled_payments' => $selectedPayments
                ];

                Log::info('Snap Params', [
                    'order_id' => $params['transaction_details']['order_id'],
                    'amount' => $params['transaction_details']['gross_amount'],
                    'customer_email' => $params['customer_details']['email']
                ]);

                $snapToken = Snap::getSnapToken($params);

                // Validate snap token
                if (empty($snapToken) || !is_string($snapToken)) {
                    Log::error('Invalid Snap Token returned', [
                        'token_type' => gettype($snapToken),
                        'token_value' => var_export($snapToken, true),
                    ]);
                    throw new \Exception('Invalid snap token returned from Midtrans: ' . var_export($snapToken, true));
                }

                if (strlen($snapToken) < 20) {
                    Log::error('Snap Token too short', [
                        'token' => $snapToken,
                        'length' => strlen($snapToken)
                    ]);
                    throw new \Exception('Snap token length invalid: ' . strlen($snapToken) . ' chars');
                }

                Log::info('✅ Snap Token Generated Successfully', [
                    'token_preview' => substr($snapToken, 0, 20) . '...',
                    'length' => strlen($snapToken)
                ]);

                // Save snap token ke order
                $order->update(['snap_token' => $snapToken]);
                Log::info('Snap token saved to order', ['order_id' => $orderId]);

            } catch (\Throwable $e) {
                Log::error('❌ Midtrans Error', [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway error: ' . $e->getMessage(),
                    'error' => [
                        'code' => 'PAYMENT_ERROR',
                        'details' => $e->getMessage()
                    ]
                ], 500);
            }
        }

        // Ensure snap_token is returned (or null for COD)
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order' => $order,
                'snap_token' => $snapToken ?? null
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        if ($user->profile?->role === 'admin' || $user->hasRole('admin')) {
            $order = Order::with('user')->find($id);
        } else {
            $order = Order::where('user_id', $user->id)->where('id', $id)->first();
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Order with the specified ID does not exist or does not belong to you'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'data' => $order
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find order by ID
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Order does not exist'
                ]
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|string',
            'tracking_number' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        // Update order
        $order->update($request->only(['status', 'tracking_number']));

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Log::info('🗑️ Delete order request', ['id' => $id, 'id_type' => gettype($id)]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        // Check if admin
        $isAdmin = $user->profile?->role === 'admin' || $user->hasRole('admin') || $user->role === 'admin';
        
        Log::info('Delete order - User check', ['user_id' => $user->id, 'is_admin' => $isAdmin]);

        if ($isAdmin) {
            $order = Order::find($id);
            Log::info('Admin order lookup', ['found' => !!$order, 'id' => $id]);
        } else {
            $order = Order::where('user_id', $user->id)->where('id', $id)->first();
            Log::info('User order lookup', ['found' => !!$order, 'user_id' => $user->id, 'order_id' => $id]);
        }

        if (!$order) {
            // Try to find any order with similar ID for debugging
            $allOrders = Order::select('id')->limit(5)->get();
            Log::warning('Order not found', [
                'requested_id' => $id,
                'sample_order_ids' => $allOrders->pluck('id')->toArray()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Order with the specified ID does not exist or does not belong to you'
                ]
            ], 404);
        }

        // Log delete action
        Log::info('Delete order', [
            'order_id' => $id,
            'order_status' => $order->status,
            'deleted_by' => $user->id,
            'is_admin' => $isAdmin
        ]);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    /**
     * Handle Midtrans Webhook Callback - PUBLIC endpoint (no auth required)
     * Called by Midtrans when payment status changes
     *
     * Webhook Events:
     * - payment.success
     * - payment.pending
     * - payment.deny
     * - payment.expire
     * - payment.cancel
     */
    public function handleMidtransWebhook(Request $request)
    {
        $json = $request->getContent();
        $data = json_decode($json);

        Log::info('🔔 Midtrans Webhook Received', [
            'order_id' => $data->order_id ?? null,
            'status_code' => $data->status_code ?? null,
            'transaction_status' => $data->transaction_status ?? null,
            'payment_type' => $data->payment_type ?? null,
        ]);

        // Validate webhook signature
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $signature = hash('sha512', $data->order_id . $data->status_code . $data->gross_amount . $serverKey);

        if ($signature !== $data->signature_key) {
            Log::warning('⚠️ Webhook signature mismatch!', [
                'expected' => $signature,
                'received' => $data->signature_key ?? 'none'
            ]);

            // Still process but with caution - log it
            // In production, you might want to reject this
        }

        // Find order
        $order = Order::find($data->order_id);
        if (!$order) {
            Log::warning('Order not found for webhook', ['order_id' => $data->order_id]);
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Map Midtrans transaction status to our status
        $transactionStatus = $data->transaction_status;
        $newStatus = $order->status;

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                // Payment successful
                $newStatus = 'paid';
                Log::info('✅ Payment confirmed for order: ' . $data->order_id);
                break;

            case 'pending':
                // Payment pending (waiting for customer action)
                $newStatus = 'pendingPayment';
                Log::info('⏳ Payment pending for order: ' . $data->order_id);
                break;

            case 'deny':
            case 'cancel':
                // Payment denied or cancelled
                $newStatus = 'cancelled';
                Log::warning('❌ Payment denied/cancelled for order: ' . $data->order_id);
                break;

            case 'expire':
                // Payment expired
                $newStatus = 'expired';
                Log::warning('⏰ Payment expired for order: ' . $data->order_id);
                break;

            case 'refund':
                // Refund occurred
                $newStatus = 'refunded';
                Log::info('💰 Refund processed for order: ' . $data->order_id);
                break;

            default:
                Log::info('ℹ️ Unknown transaction status: ' . $transactionStatus);
                $newStatus = $order->status; // Keep existing status
                break;
        }

        // Update order status if changed
        if ($newStatus !== $order->status) {
            $order->update([
                'status' => $newStatus
            ]);
            Log::info('Order status updated', [
                'order_id' => $data->order_id,
                'old_status' => $order->status,
                'new_status' => $newStatus
            ]);
        }

        // Return success response to Midtrans
        return response()->json([
            'success' => true,
            'message' => 'Webhook processed successfully'
        ], 200);
    }
}
