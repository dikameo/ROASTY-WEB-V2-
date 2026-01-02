<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

// Check database
$products = Product::limit(5)->get();
echo "Total Products: " . Product::count() . "\n\n";

foreach ($products as $product) {
    echo "Product ID: {$product->id}\n";
    echo "Name: {$product->name}\n";
    echo "Image URLs: " . json_encode($product->image_urls) . "\n";
    echo "---\n";
}
?>
