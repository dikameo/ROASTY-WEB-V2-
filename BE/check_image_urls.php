<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::whereNotNull('image_urls')
    ->where('image_urls', '!=', '[]')
    ->limit(10)
    ->get();

echo "=== Checking image_urls in database ===\n";
foreach ($products as $p) {
    echo "\nProduct: " . $p->name . "\n";
    echo "image_urls: " . json_encode($p->image_urls) . "\n";
    if (is_array($p->image_urls) && count($p->image_urls) > 0) {
        echo "First URL: " . $p->image_urls[0] . "\n";
    }
}
