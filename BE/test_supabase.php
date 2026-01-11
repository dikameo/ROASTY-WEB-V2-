<?php

// Simple test script for Supabase S3 connection
// Run with: php test_supabase.php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🔍 Testing Supabase S3 Connection...\n\n";

// Configuration
$accessKeyId = $_ENV['AWS_ACCESS_KEY_ID'] ?? '';
$secretAccessKey = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '';
$region = $_ENV['AWS_DEFAULT_REGION'] ?? 'ap-southeast-1';
$bucket = $_ENV['AWS_BUCKET'] ?? '';
$endpoint = $_ENV['AWS_ENDPOINT'] ?? '';

echo "📋 Configuration:\n";
echo "   Access Key ID: " . substr($accessKeyId, 0, 8) . "...\n";
echo "   Region: $region\n";
echo "   Bucket: $bucket\n";
echo "   Endpoint: $endpoint\n\n";

try {
    // Create S3 client - for Supabase, we need to use the correct endpoint
    // Supabase S3 endpoint format: https://<project-ref>.supabase.co/storage/v1/s3
    $s3Client = new S3Client([
        'version' => 'latest',
        'region' => $region,
        'endpoint' => $endpoint,
        'credentials' => [
            'key' => $accessKeyId,
            'secret' => $secretAccessKey,
        ],
        'use_path_style_endpoint' => true, // Supabase requires path style
        'http' => [
            'verify' => false, // Disable SSL verification for testing
        ],
    ]);

    echo "📂 Listing objects in bucket '$bucket'...\n\n";

    // List objects
    $result = $s3Client->listObjectsV2([
        'Bucket' => $bucket,
        'MaxKeys' => 100,
    ]);

    $objects = $result['Contents'] ?? [];

    if (empty($objects)) {
        echo "⚠️  Bucket is empty. No files found.\n";
    } else {
        echo "✅ Found " . count($objects) . " file(s):\n\n";
        
        foreach ($objects as $index => $object) {
            $key = $object['Key'];
            $size = $object['Size'];
            $lastModified = $object['LastModified']->format('Y-m-d H:i:s');
            
            echo sprintf("   %d. %s (%s bytes, %s)\n", $index + 1, $key, number_format($size), $lastModified);
            
            // Show public URL
            $publicUrl = preg_replace('/\/s3$/', '/object/public/' . $bucket . '/' . $key, $endpoint);
            echo "      URL: $publicUrl\n\n";
        }
    }

    echo "\n✅ Supabase S3 connection is working!\n";

} catch (AwsException $e) {
    echo "❌ AWS Error!\n";
    echo "   Code: " . $e->getAwsErrorCode() . "\n";
    echo "   Message: " . $e->getAwsErrorMessage() . "\n";
    echo "   Full: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
