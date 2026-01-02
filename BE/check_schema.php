<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Model Has Roles Column Type ===\n";
$result = DB::select("
    SELECT column_name, data_type, is_nullable
    FROM information_schema.columns
    WHERE table_name = 'model_has_roles'
    ORDER BY column_name
");
foreach ($result as $row) {
    echo "{$row->column_name}: {$row->data_type} (nullable: {$row->is_nullable})\n";
}

echo "\n=== Model Has Permissions Column Type ===\n";
$result = DB::select("
    SELECT column_name, data_type, is_nullable
    FROM information_schema.columns
    WHERE table_name = 'model_has_permissions'
    ORDER BY column_name
");
foreach ($result as $row) {
    echo "{$row->column_name}: {$row->data_type} (nullable: {$row->is_nullable})\n";
}
