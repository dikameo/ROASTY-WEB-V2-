<?php
// Check CSV image URLs
$csvFile = 'database/data/products.csv';
$handle = fopen($csvFile, 'r');

echo "=== CSV IMAGE URLS CHECK ===\n";

$header = fgetcsv($handle);
echo "Header: " . implode(', ', $header) . "\n\n";

$count = 0;
while (($row = fgetcsv($handle)) !== false && $count < 5) {
    if (count($row) > 5) {
        echo "Product {$count}: {$row[0]}\n";
        echo "  image_urls: {$row[5]}\n";
        $count++;
    }
}

fclose($handle);
