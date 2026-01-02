<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Helpers\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Normalize image URLs to use relative paths instead of full URLs
     * This prevents double-prefixing issues on the frontend
     * Returns paths like: "uploads/products/filename.png" (without /storage prefix)
     */
    private function normalizeImageUrls($imageUrls)
    {
        if (!is_array($imageUrls)) {
            return [];
        }

        return array_map(function($url) {
            if (!$url) return '';

            // If it's a data URI, return as-is
            if (strpos($url, 'data:') === 0) {
                return $url;
            }

            // Extract just the path part from full URLs
            // From: https://unfollowed-corrin-unorchestrated.ngrok-free.dev/storage/uploads/products/...
            // To: uploads/products/... (without /storage prefix since CONFIG.assets already includes /storage)
            if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                // Parse URL and get the path
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';

                // If path starts with /storage, remove it and return relative path
                if (strpos($path, '/storage') === 0) {
                    return ltrim(str_replace('/storage', '', $path), '/');
                }
                // Otherwise return the full URL (external images)
                return $url;
            }

            // If it's already a relative path starting with /storage, remove the prefix
            if (strpos($url, '/storage/') === 0) {
                return ltrim(str_replace('/storage', '', $url), '/');
            }

            if (strpos($url, 'storage/') === 0) {
                return str_replace('storage/', '', $url);
            }

            // Default: return as-is
            return $url;
        }, $imageUrls);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where('name', 'LIKE', "%{$search}%");
            }

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Sort by newest first (default behavior)
            $query->orderBy('created_at', 'desc');

            // Pagination
            $perPage = $request->get('limit', 10);
            // Safely load creator - only load if created_by is not NULL
            $products = $query->with(['creator' => function($q) {
                // This will only load creator when created_by is not null
            }])->paginate($perPage);

            // Normalize image URLs for all products
            $products->getCollection()->transform(function($product) {
                $product->image_urls = $this->normalizeImageUrls($product->image_urls);
                return $product;
            });

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => [
                    'code' => 'DATABASE_ERROR',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user is admin (via profile role or spatie role)
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        // Check multiple role sources
        $isAdmin = $user->profile?->role === 'admin' || $user->hasRole('admin') || $user->role === 'admin';

        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => [
                    'code' => 'FORBIDDEN',
                    'details' => 'Only admins can create products'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'capacity' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'specifications' => 'nullable|array',
            'image_urls' => 'nullable|array',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'is_active' => 'nullable|boolean',
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

        $data = $request->only(['name', 'price', 'description', 'capacity', 'category', 'specifications', 'image_urls', 'is_active']);
        // Ensure created_by is set to the authenticated user's UUID ID (not integer)
        $data['created_by'] = $user->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads/products', $imageName, 'public');
            // image_urls is JSONB array in database
            $data['image_urls'] = [Storage::url($imagePath)];
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('creator')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Product with the specified ID does not exist'
                ]
            ], 404);
        }

        // Normalize image URLs
        $product->image_urls = $this->normalizeImageUrls($product->image_urls);

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Check if user is admin
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        // Check multiple role sources
        $isAdmin = $user->profile?->role === 'admin' || $user->hasRole('admin') || $user->role === 'admin';

        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => [
                    'code' => 'FORBIDDEN',
                    'details' => 'Only admins can update products'
                ]
            ], 403);
        }
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Product with the specified ID does not exist'
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'capacity' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'specifications' => 'nullable|array',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'is_active' => 'nullable|boolean',
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

        $data = $request->only(['name', 'price', 'capacity', 'category', 'specifications', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads/products', $imageName, 'public');
            $data['image_urls'] = [Storage::url($imagePath)];
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if user is admin
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');

        // Check multiple role sources
        $isAdmin = $user->profile?->role === 'admin' || $user->hasRole('admin') || $user->role === 'admin';

        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => [
                    'code' => 'FORBIDDEN',
                    'details' => 'Only admins can delete products'
                ]
            ], 403);
        }
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Product with the specified ID does not exist'
                ]
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
