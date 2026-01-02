<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

Route::get('/', function () {
    return view('welcome');
});

// Serve storage files
Route::get('/storage/{path}', function ($path) {
    $fullPath = "public/{$path}";

    if (!Storage::exists($fullPath)) {
        abort(404);
    }

    $file = Storage::path($fullPath);
    return response()->file($file, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');
