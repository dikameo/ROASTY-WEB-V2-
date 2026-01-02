<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Hapus produk yang created_by tidak ada di users (invalid foreign key)
$deleted = DB::delete("
    DELETE FROM products
    WHERE created_by IS NOT NULL
    AND created_by NOT IN (SELECT id FROM users)
");

echo "Deleted {$deleted} invalid products\n";

// Or set created_by to NULL for invalid ones
$updated = DB::update("
    UPDATE products
    SET created_by = NULL
    WHERE created_by IS NOT NULL
    AND created_by NOT IN (SELECT id FROM users)
");

echo "Updated {$updated} invalid products to created_by = NULL\n";

// List all products
$products = DB::select("SELECT id, name, created_by FROM products LIMIT 10");
echo "\nRemaining products:\n";
foreach ($products as $p) {
    echo "ID: {$p->id}, Name: {$p->name}, created_by: " . ($p->created_by ?? 'NULL') . "\n";
}
