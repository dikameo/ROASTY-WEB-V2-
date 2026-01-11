<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestSupabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:test {--upload : Upload a test file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Supabase S3 Storage connection by listing all files in the bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Supabase S3 Storage Connection...');
        $this->newLine();

        // Show current configuration
        $this->info('📋 Current Configuration:');
        $this->table(
            ['Key', 'Value'],
            [
                ['FILESYSTEM_DISK', env('FILESYSTEM_DISK', 'local')],
                ['AWS_BUCKET', env('AWS_BUCKET', 'not set')],
                ['AWS_ENDPOINT', env('AWS_ENDPOINT', 'not set')],
                ['AWS_URL', env('AWS_URL', 'not set')],
                ['AWS_ACCESS_KEY_ID', substr(env('AWS_ACCESS_KEY_ID', ''), 0, 8) . '...' ?: 'not set'],
            ]
        );
        $this->newLine();

        try {
            // Get the S3 disk
            $disk = Storage::disk('s3');

            // Test: Upload a test file if --upload flag is provided
            if ($this->option('upload')) {
                $this->info('📤 Uploading test file...');
                $testFileName = 'test_' . time() . '.txt';
                $testContent = 'Hello from Laravel! Test at: ' . now()->toDateTimeString();
                
                $disk->put($testFileName, $testContent);
                $this->info("✅ Test file uploaded: {$testFileName}");
                
                // Get URL
                $url = $disk->url($testFileName);
                $this->info("🔗 File URL: {$url}");
                $this->newLine();
            }

            // List all files in the bucket
            $this->info('📂 Listing all files in bucket...');
            $this->newLine();

            $files = $disk->allFiles();

            if (empty($files)) {
                $this->warn('⚠️  Bucket is empty. No files found.');
                $this->info('💡 Try uploading a test file with: php artisan storage:test --upload');
            } else {
                $this->info("✅ Found " . count($files) . " file(s):");
                $this->newLine();

                $tableData = [];
                foreach ($files as $index => $file) {
                    $size = $disk->size($file);
                    $lastModified = $disk->lastModified($file);
                    $url = $disk->url($file);

                    $tableData[] = [
                        $index + 1,
                        $file,
                        $this->formatBytes($size),
                        date('Y-m-d H:i:s', $lastModified),
                    ];

                    // Show URL for first 5 files
                    if ($index < 5) {
                        $this->line("   📷 {$file}");
                        $this->line("      URL: {$url}");
                        $this->newLine();
                    }
                }

                if (count($files) > 5) {
                    $this->info("   ... and " . (count($files) - 5) . " more files.");
                }

                $this->newLine();
                $this->table(
                    ['#', 'File Path', 'Size', 'Last Modified'],
                    $tableData
                );
            }

            $this->newLine();
            $this->info('✅ Supabase S3 connection is working!');

        } catch (\Exception $e) {
            $this->error('❌ Connection failed!');
            $this->newLine();
            $this->error('Error Class: ' . get_class($e));
            $this->error('Error Message: ' . $e->getMessage());
            $this->newLine();
            
            // Show more details for AWS errors
            if (method_exists($e, 'getAwsErrorCode')) {
                $this->error('AWS Error Code: ' . $e->getAwsErrorCode());
            }
            if (method_exists($e, 'getAwsErrorMessage')) {
                $this->error('AWS Error Message: ' . $e->getAwsErrorMessage());
            }
            
            $this->newLine();
            $this->warn('💡 Tips:');
            $this->line('   1. Check your AWS credentials in .env');
            $this->line('   2. Ensure the bucket exists and is public');
            $this->line('   3. Verify AWS_ENDPOINT URL is correct');
            
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
