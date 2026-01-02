<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/products.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $handle = fopen($csvFile, 'r');

        // Skip header row
        $header = fgetcsv($handle);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) continue;

            // Image URLs dari kolom ke-6 (index 5)
            $imageUrlsRaw = trim($row[5]);
            $imageUrls = [];

            // Parse JSON array jika ada, jika tidak gunakan kosong
            if (!empty($imageUrlsRaw) && $imageUrlsRaw !== '[]') {
                $parsed = json_decode($imageUrlsRaw, true);
                if (is_array($parsed)) {
                    $imageUrls = $parsed;
                }
            }

            // Jika kosong, tambahkan public image URLs dari loremflickr (reliable placeholder service)
            if (empty($imageUrls)) {
                $productName = trim($row[0]);
                // Gunakan loremflickr untuk random coffee images - lebih reliable dari unsplash
                $imageUrls = [
                    "https://loremflickr.com/400/400?lock=" . rand(1000, 9999)
                ];
            }

            $data = [
                'name' => trim($row[0]),
                'price' => (float) $row[1],
                'capacity' => trim($row[2]),
                'category' => trim($row[3]),
                'specifications' => json_decode($row[4], true) ?: [],
                'image_urls' => $imageUrls,  // Dengan placeholder URLs
                'rating' => (float) $row[6],
                'review_count' => (int) $row[7],
                'is_active' => $row[8] === 'true',
                'created_by' => null,  // Set ke null karena admin user belum ada
                'stock' => isset($row[9]) && $row[9] !== '' ? (int) $row[9] : 50,
                'sold_count' => isset($row[10]) && $row[10] !== '' ? (int) $row[10] : 0,
                'discussion_count' => isset($row[11]) && $row[11] !== '' ? (int) $row[11] : 0,
            ];

            try {
                Product::updateOrCreate(
                    ['name' => $data['name']],
                    $data
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->warn("Error importing product: {$data['name']}");
                $this->command->warn("Error: " . $e->getMessage());
            }
        }

        fclose($handle);

        $this->command->info("✅ Imported {$count} products!");
        $this->command->info("   Gambar placeholder URLs sudah ditambahkan ke database");
    }
}
