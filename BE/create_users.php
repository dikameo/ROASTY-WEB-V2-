<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Create users
$users = [
    [
        'name' => 'John Doe',
        'email' => 'john@umm.id',
        'password' => 'password123',
        'phone' => '08123456789',
        'role' => 'customer'
    ],
    [
        'name' => 'Admin User',
        'email' => 'admin@umm.id',
        'password' => 'admin123',
        'phone' => '08987654321',
        'role' => 'admin'
    ]
];

$User = \App\Models\User::class;
$Hash = \Illuminate\Support\Facades\Hash::class;

foreach ($users as $userData) {
    $User::updateOrCreate(
        ['email' => $userData['email']],
        [
            'name' => $userData['name'],
            'password' => $Hash::make($userData['password']),
            'phone' => $userData['phone'],
            'role' => $userData['role']
        ]
    );
    echo "✅ User created: {$userData['email']}\n";
}

echo "\n✅ Users ready for login testing!\n";
