<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Simulate API response structure
$query = \App\Models\Product::query();
$perPage = 10;
$products = $query->with('creator')->paginate($perPage);

// Normalize image URLs (simulate backend)
$products->getCollection()->transform(function($product) {
    $imageUrls = [];
    if (is_array($product->image_urls)) {
        foreach ($product->image_urls as $url) {
            if (!$url) continue;
            if (strpos($url, 'data:') === 0) {
                $imageUrls[] = $url;
            } elseif (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';
                if (strpos($path, '/storage') === 0) {
                    $imageUrls[] = ltrim(str_replace('/storage', '', $path), '/');
                } else {
                    $imageUrls[] = $url;
                }
            } elseif (strpos($url, '/storage/') === 0) {
                $imageUrls[] = ltrim(str_replace('/storage', '', $url), '/');
            } elseif (strpos($url, 'storage/') === 0) {
                $imageUrls[] = str_replace('storage/', '', $url);
            } else {
                $imageUrls[] = $url;
            }
        }
    }
    $product->image_urls = $imageUrls;
    return $product;
});

// Show response structure
echo json_encode([
    'success' => true,
    'message' => 'Products retrieved successfully',
    'data' => $products
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
