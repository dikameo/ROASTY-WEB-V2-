<?php
// Update products.csv to add stock columns

$csvFile = 'database/data/products.csv';

if (!file_exists($csvFile)) {
    echo "CSV file not found\n";
    exit(1);
}

$handle = fopen($csvFile, 'r');
$rows = [];

// Read all rows
while (($row = fgetcsv($handle)) !== false) {
    $rows[] = $row;
}
fclose($handle);

// Update header (first row)
if (count($rows) > 0) {
    // Add new columns to header
    $rows[0][] = 'stock';
    $rows[0][] = 'sold_count';
    $rows[0][] = 'discussion_count';

    // Add default values to data rows
    for ($i = 1; $i < count($rows); $i++) {
        $rows[$i][] = '50';      // stock
        $rows[$i][] = '0';       // sold_count
        $rows[$i][] = '0';       // discussion_count
    }
}

// Write back to CSV
$handle = fopen($csvFile, 'w');
foreach ($rows as $row) {
    fputcsv($handle, $row);
}
fclose($handle);

echo "✓ CSV updated successfully with stock columns!\n";
echo "  - Added 'stock' column (default: 50)\n";
echo "  - Added 'sold_count' column (default: 0)\n";
echo "  - Added 'discussion_count' column (default: 0)\n";
