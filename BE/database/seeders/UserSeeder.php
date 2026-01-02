<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        User::create([
            'name' => 'John Doe',
            'email' => 'john@umm.id',
            'password' => Hash::make('password123'),
            'phone' => '08123456789',
            'role' => 'customer'
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@umm.id',
            'password' => Hash::make('admin123'),
            'phone' => '08987654321',
            'role' => 'admin'
        ]);

        echo "✅ 2 users created: john@umm.id (password: password123) dan admin@umm.id (password: admin123)";
    }
}
