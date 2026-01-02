<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$products = \App\Models\Product::all();
echo "Total Products: " . count($products) . PHP_EOL;
if (count($products) > 0) {
    $latest = $products->last();
    echo "Latest Product: " . $latest->name . PHP_EOL;
    echo "Image URLs: " . json_encode($latest->image_urls) . PHP_EOL;
    echo "Created At: " . $latest->created_at . PHP_EOL;
} else {
    echo "No products found" . PHP_EOL;
}
?>
