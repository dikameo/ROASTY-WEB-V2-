<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Products Table Columns ===\n";
$result = DB::select("
    SELECT column_name, data_type, is_nullable
    FROM information_schema.columns
    WHERE table_name = 'products'
    ORDER BY ordinal_position
");
foreach ($result as $row) {
    echo "{$row->column_name}: {$row->data_type} (nullable: {$row->is_nullable})\n";
}

echo "\n=== Sample Products with created_by ===\n";
$products = DB::select("
    SELECT id, name, created_by FROM products LIMIT 5
");
foreach ($products as $product) {
    echo "ID: {$product->id}, Name: {$product->name}, created_by: {$product->created_by}\n";
}
