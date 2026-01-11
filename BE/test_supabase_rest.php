<?php

// Test Supabase Storage using REST API (simpler approach)
// Run with: php test_supabase_rest.php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🔍 Testing Supabase Storage Connection via REST API...\n\n";

// Configuration from .env
$supabaseUrl = 'https://fiyodlfgfbcnatebudut.supabase.co';
$bucket = $_ENV['AWS_BUCKET'] ?? 'product-images';
$accessKeyId = $_ENV['AWS_ACCESS_KEY_ID'] ?? '';
$secretKey = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '';

echo "📋 Configuration:\n";
echo "   Supabase URL: $supabaseUrl\n";
echo "   Bucket: $bucket\n";
echo "   Access Key ID: " . substr($accessKeyId, 0, 8) . "...\n\n";

// Supabase Storage REST API endpoint
$listUrl = "$supabaseUrl/storage/v1/object/list/$bucket";

echo "📂 Listing objects in bucket '$bucket'...\n";
echo "   API URL: $listUrl\n\n";

// Make request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $listUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secretKey",
    "Content-Type: application/json",
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'prefix' => '',
    'limit' => 100,
    'offset' => 0,
]));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "📊 HTTP Response Code: $httpCode\n\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
    exit(1);
}

if ($httpCode >= 400) {
    echo "❌ API Error:\n";
    echo $response . "\n";
    exit(1);
}

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ JSON Parse Error: " . json_last_error_msg() . "\n";
    echo "Raw response: " . substr($response, 0, 500) . "\n";
    exit(1);
}

if (empty($data)) {
    echo "⚠️  Bucket is empty. No files found.\n";
    echo "💡 Try uploading a file via Admin Panel first.\n";
} else {
    echo "✅ Found " . count($data) . " item(s):\n\n";
    
    foreach ($data as $index => $item) {
        $name = $item['name'] ?? 'unknown';
        $id = $item['id'] ?? '';
        $createdAt = $item['created_at'] ?? '';
        $metadata = $item['metadata'] ?? [];
        $size = $metadata['size'] ?? 0;
        
        echo sprintf("   %d. %s\n", $index + 1, $name);
        
        if (!empty($id)) {
            // Construct public URL
            $publicUrl = "$supabaseUrl/storage/v1/object/public/$bucket/$name";
            echo "      URL: $publicUrl\n";
        }
        
        if ($size > 0) {
            echo "      Size: " . number_format($size) . " bytes\n";
        }
        
        echo "\n";
    }
}

echo "\n✅ Test completed!\n";
