<?php
$url = "https://unfollowed-corrin-unorchestrated.ngrok-free.dev/storage/uploads/products/1767357677_bitcoin.png.jpg";

$parsed = parse_url($url);
$path = $parsed['path'] ?? '';

echo "Full URL: " . $url . PHP_EOL;
echo "Parsed path: " . $path . PHP_EOL;
echo "Path starts with /storage: " . (strpos($path, '/storage') === 0 ? 'YES' : 'NO') . PHP_EOL;

if (strpos($path, '/storage') === 0) {
    $result = ltrim(str_replace('/storage', '', $path), '/');
    echo "Result after normalization: " . $result . PHP_EOL;
}
?>
