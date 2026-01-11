<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Frontend Routes (Migrated from HTML)
Route::get('/', function () { return view('beranda'); })->name('home');
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::get('/lupa-password', function () { return view('auth.lupa_password'); })->name('password.request');

// Product Routes
Route::get('/produk', function () { return view('produk.daftar'); })->name('produk.index');
Route::get('/produk/detail', function () { return view('produk.detail'); })->name('produk.detail');

// User Routes
Route::get('/keranjang', function () { return view('keranjang'); })->name('keranjang');
Route::get('/profil', function () { return view('profil'); })->name('profil');
Route::get('/pembayaran', function () { return view('pembayaran'); })->name('pembayaran');

// Admin Route
Route::get('/admin/dashboard', function () { return view('admin.dashboard'); })->name('admin.dashboard');

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
