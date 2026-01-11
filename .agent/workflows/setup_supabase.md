---
description: Panduan Setup Supabase Storage untuk Laravel
---

# Panduan Setup Supabase Storage

Berikut adalah langkah-langkah untuk menghubungkan Laravel Backend dengan Supabase Storage untuk menyimpan gambar produk.

## 1. Setup di Supabase Dashboard

1.  Login ke [Supabase Dashboard](https://supabase.com/dashboard).
2.  Pilih Project Anda.
3.  Pergi ke menu **Storage** (icon folder di sidebar kiri).
4.  Klik **"New Bucket"**.
5.  Isi detail bucket:
    *   **Name**: `uploads` (PENTING: Gunakan nama ini agar sesuai dengan struktur folder).
    *   **Public bucket**: ✅ **Aktifkan** (Switch ON). Ini wajib agar gambar bisa diakses publik.
    *   Klik **"Create Bucket"**.
6.  Setelah bucket dibuat, pergi ke **Project Settings** (icon gerigi di bawah) -> **Storage**.
7.  Di bagian **S3 Connection**, Anda akan melihat credential yang dibutuhkan.

## 2. Update File .env di Backend

Buka file `d:\web di rodok\ROASTY-WEB-V2-\BE\.env` dan tambahkan/update konfigurasi berikut menggunakan data dari Supabase S3 Connection tadi:

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID= [Copy dari ACCESS KEY ID Supabase]
AWS_SECRET_ACCESS_KEY= [Copy dari SECRET ACCESS KEY Supabase]
AWS_DEFAULT_REGION=ap-southeast-1  # Region Supabase Anda (misal: ap-southeast-1 untuk Singapore)
AWS_BUCKET=uploads                 # Nama bucket yang Anda buat
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_ENDPOINT= [Copy dari ENDPOINT Supabase]
AWS_URL=https://[Project-ID].supabase.co/storage/v1/object/public/uploads
```

**Catatan:**
*   `AWS_ENDPOINT` biasanya formatnya `https://[Project-ID].supabase.co/storage/v1/s3`
*   `AWS_URL` adalah URL publik untuk akses file. Formatnya: `https://[Project-ID].supabase.co/storage/v1/object/public/[NAMA_BUCKET]`

## 3. Pastikan Dependencies Terinstall

Pastikan package driver S3 sudah terinstall (ini sudah ada di project ini):

```bash
composer require league/flysystem-aws-s3-v3
```

## 4. Restart Server

Setelah mengubah `.env`, jangan lupa restart server Laravel:

```bash
# Di terminal Backend (BE):
Ctrl+C
php artisan config:clear
php artisan serve
```

## 5. Testing

Coba upload produk baru via Admin Panel. Gambar akan otomatis tersimpan di Supabase bucket `uploads/products` dan URL akan mengarah ke Supabase.
