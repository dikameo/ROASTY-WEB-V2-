<?php
require 'vendor/autoload.php';

use App\Models\Product;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check products
$count = Product::count();
$product = Product::first();

echo "=== DATABASE CHECK ===\n";
echo "Total products: " . $count . "\n";

if ($product) {
    echo "\nFirst product:\n";
    echo "  ID: " . $product->id . "\n";
    echo "  Name: " . $product->name . "\n";
    echo "  Price: " . $product->price . "\n";
    echo "  Stock: " . $product->stock . "\n";
    echo "  Rating: " . $product->rating . "\n";
    echo "  Review Count: " . $product->review_count . "\n";
    echo "  Sold Count: " . $product->sold_count . "\n";
    echo "  Discussion Count: " . $product->discussion_count . "\n";
} else {
    echo "No products found in database!\n";
}
