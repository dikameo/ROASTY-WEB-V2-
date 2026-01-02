<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            Log::debug('Fetching addresses for user: ' . $userId);

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $addresses = UserAddress::where('user_id', $userId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::debug('Found ' . count($addresses) . ' addresses');

            return response()->json([
                'success' => true,
                'message' => 'Addresses retrieved successfully',
                'data' => $addresses
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching addresses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching addresses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'is_primary' => 'nullable|boolean',
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

        try {
            // If this is primary, unset other primary addresses
            if ($request->get('is_primary')) {
                UserAddress::where('user_id', Auth::id())->update(['is_primary' => false]);
            }

            $address = UserAddress::create([
                'user_id' => Auth::id(),
                'label' => $request->label,
                'recipient_name' => $request->recipient_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'is_primary' => $request->get('is_primary', false),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $address
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $address = UserAddress::where('user_id', Auth::id())->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Address with the specified ID does not exist or does not belong to you'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Address retrieved successfully',
            'data' => $address
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $address = UserAddress::where('user_id', Auth::id())->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Address with the specified ID does not exist or does not belong to you'
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'sometimes|required|string',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'is_primary' => 'nullable|boolean',
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

        try {
            // If this is primary, unset other primary addresses
            if ($request->get('is_primary')) {
                UserAddress::where('user_id', Auth::id())->where('id', '!=', $id)->update(['is_primary' => false]);
            }

            $updateData = [];
            if ($request->has('label')) $updateData['label'] = $request->label;
            if ($request->has('recipient_name')) $updateData['recipient_name'] = $request->recipient_name;
            if ($request->has('phone')) $updateData['phone'] = $request->phone;
            if ($request->has('address')) $updateData['address'] = $request->address;
            if ($request->has('province')) $updateData['province'] = $request->province;
            if ($request->has('city')) $updateData['city'] = $request->city;
            if ($request->has('postal_code')) $updateData['postal_code'] = $request->postal_code;
            if ($request->has('latitude')) $updateData['latitude'] = $request->latitude;
            if ($request->has('longitude')) $updateData['longitude'] = $request->longitude;
            if ($request->has('accuracy')) $updateData['accuracy'] = $request->accuracy;
            if ($request->has('is_primary')) $updateData['is_primary'] = $request->is_primary;

            $address->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $address = UserAddress::where('user_id', Auth::id())->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Address with the specified ID does not exist or does not belong to you'
                ]
            ], 404);
        }

        try {
            $address->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting address',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
