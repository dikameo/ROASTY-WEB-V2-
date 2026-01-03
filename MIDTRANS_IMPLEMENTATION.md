# 🎫 Dokumentasi Implementasi Midtrans - Fetch dari .env Backend

## 📌 Ringkasan Perbaikan

Implementasi Midtrans telah diperbaiki sehingga **client key tidak lagi hardcoded di frontend**. Sebaliknya, frontend akan **fetch client key dari API backend** yang membaca dari file `.env`.

---

## ❌ Masalah Lama (Sebelum Perbaikan)

```javascript
// ❌ BAD: Hardcoded di config.js
const CONFIG = {
    API_BASE_URL: "http://localhost:8000/api",
    MIDTRANS_CLIENT_KEY: "Mid-client-KOAGQWpfEka2OKgh"  // Hardcoded!
};

// ❌ BAD: Hardcoded di HTML
<script src="https://app.midtrans.com/snap/snap.js" 
        data-client-key="Mid-client-KOAGQWpfEka2OKgh"></script>
```

### Masalah:
1. **Tidak Fleksibel**: Jika client key di `.env` berubah, frontend harus di-update manual
2. **Keamanan**: Client key terekspos di source code
3. **Sulit Maintenance**: Perlu deploy frontend setiap kali config berubah

---

## ✅ Solusi Baru (Setelah Perbaikan)

### Alur Kerja:
```
1. Frontend loading
   ↓
2. loadMidtransScript() dipanggil
   ↓
3. Fetch GET /api/midtrans-config (Public endpoint)
   ↓
4. Backend mengirim client key dari .env
   ↓
5. Frontend create script element dinamis dengan client key
   ↓
6. Midtrans Snap script ter-load dengan config yang benar
```

---

## 📝 Perubahan di Backend

### 1️⃣ Tambah Method di OrderController

**File**: [BE/app/Http/Controllers/Api/OrderController.php](BE/app/Http/Controllers/Api/OrderController.php)

```php
/**
 * Get Midtrans configuration (public endpoint)
 * Frontend akan fetch client key dari sini
 */
public function getMidtransConfig()
{
    return response()->json([
        'success' => true,
        'data' => [
            'client_key' => config('midtrans.client_key'),
            'is_production' => config('midtrans.is_production'),
        ]
    ]);
}
```

**Penjelasan**:
- Endpoint ini **public** (tidak perlu autentikasi)
- Membaca `MIDTRANS_CLIENT_KEY` dari file `.env`
- Membaca `MIDTRANS_IS_PRODUCTION` dari file `.env`
- Mengirim response JSON dengan data config

---

### 2️⃣ Tambah Route untuk Endpoint

**File**: [BE/routes/api.php](BE/routes/api.php)

```php
// Public Midtrans config endpoint (untuk frontend fetch client key)
Route::get('/midtrans-config', [OrderController::class, 'getMidtransConfig']);
```

**Lokasi**: Ditambahkan di **public routes** section (bukan protected routes)

---

## 🎨 Perubahan di Frontend

### 1️⃣ Hapus Hardcoded Script di HTML

**File**: [FE/halaman.pembayaran.html](FE/halaman.pembayaran.html)

**Sebelum** ❌:
```html
<!-- Hardcoded dengan client key di HTML -->
<script src="https://app.midtrans.com/snap/snap.js" 
        data-client-key="Mid-client-KOAGQWpfEka2OKgh"></script>
```

**Sesudah** ✅:
```html
<!-- Container untuk script yang akan di-load dinamis -->
<div id="midtrans-script-container"></div>
```

---

### 2️⃣ Tambah Function untuk Load Script Dinamis

**File**: [FE/halaman.pembayaran.html](FE/halaman.pembayaran.html) (bagian `<script>`)

```javascript
/**
 * Fetch Midtrans client key dari backend API
 * dan load Midtrans Snap script secara dinamis
 */
async function loadMidtransScript() {
    try {
        console.log('🔄 Fetching Midtrans config dari backend...');
        
        // Fetch config dari backend API
        const response = await fetch(`${API_URL}/midtrans-config`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const configData = await response.json();
        const clientKey = configData.data.client_key;
        const isProduction = configData.data.is_production;
        
        console.log('✅ Midtrans config diterima dari backend');
        console.log('   - Client Key:', clientKey.substring(0, 15) + '...');
        console.log('   - Mode:', isProduction ? 'PRODUCTION' : 'SANDBOX');
        
        // Buat script element secara dinamis
        const scriptContainer = document.getElementById('midtrans-script-container');
        const script = document.createElement('script');
        script.src = 'https://app.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', clientKey);
        script.async = true;
        
        // Tambahkan ke DOM
        scriptContainer.appendChild(script);
        
        // Handle script load success
        script.onload = function() {
            console.log('✅ Midtrans Snap script berhasil di-load!');
            console.log('   Window.snap tersedia:', typeof window.snap === 'object' ? '✓' : '✗');
        };
        
        // Handle script load error
        script.onerror = function() {
            console.error('❌ Gagal load Midtrans Snap script');
            alert('Gagal memuat payment gateway. Silakan refresh halaman.');
        };
        
    } catch (error) {
        console.error('❌ Error loading Midtrans config:', error);
        alert('Gagal memuat konfigurasi pembayaran: ' + error.message);
    }
}
```

**Penjelasan Code**:
1. **Fetch config**: Mengambil client key dari API backend
2. **Create script element**: Membuat `<script>` tag secara dinamis
3. **Set attribute**: Menetapkan `data-client-key` dengan nilai dari backend
4. **Append to DOM**: Menambahkan script ke dalam container
5. **Error handling**: Menangani jika script gagal di-load

---

### 3️⃣ Panggil Function saat Page Load

**File**: [FE/halaman.pembayaran.html](FE/halaman.pembayaran.html) (bagian `DOMContentLoaded`)

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'login.html';
        return;
    }
    
    // 👈 Load Midtrans script dengan client key dari backend
    loadMidtransScript();
    
    // Load data pembayaran
    loadPaymentData();
    setupNavigation();
});
```

---

### 4️⃣ Update config.js

**File**: [FE/config.js](FE/config.js)

**Sebelum** ❌:
```javascript
const CONFIG = {
    API_BASE_URL: "http://localhost:8000/api",
    assets: "http://localhost:8000/storage",
    MIDTRANS_CLIENT_KEY: "Mid-client-KOAGQWpfEka2OKgh"  // Hardcoded
};
```

**Sesudah** ✅:
```javascript
const CONFIG = {
    API_BASE_URL: "http://localhost:8000/api",
    assets: "http://localhost:8000/storage"
    // MIDTRANS_CLIENT_KEY dihapus
    // Sekarang di-fetch dari API backend
};
```

---

## 🔄 Alur Pembayaran (Update)

```
User klik "Bayar Sekarang"
    ↓
setupPaymentButton() triggered
    ↓
Fetch POST /api/orders
    ↓
Backend:
  1. Validate order data
  2. Setup Midtrans Config dengan env variables
  3. Call Midtrans Snap::getSnapToken()
  4. Return snap_token
    ↓
Frontend:
  1. Terima snap_token dari response
  2. Call window.snap.pay(snapToken) ← Midtrans Snap sudah loaded dengan client key dari backend
  3. User melakukan pembayaran
    ↓
Midtrans redirect callback
    ↓
Order status updated (via webhook)
```

---

## 🧪 Testing

### Verifikasi Setup:

1. **Check Backend Endpoint**:
```bash
curl http://localhost:8000/api/midtrans-config
```

Response yang diharapkan:
```json
{
  "success": true,
  "data": {
    "client_key": "Mid-client-xHIl5auaQWqaNfVJ",
    "is_production": true
  }
}
```

2. **Check Frontend Console**:
Buka browser dev tools (F12) → Console:
```
🔄 Fetching Midtrans config dari backend...
✅ Midtrans config diterima dari backend
   - Client Key: Mid-client-xHI...
   - Mode: PRODUCTION
✅ Midtrans Snap script berhasil di-load!
   Window.snap tersedia: ✓
```

3. **Test Payment Flow**:
- Buka halaman checkout (`halaman.pembayaran.html`)
- Console harus menunjukkan logs di atas
- Klik tombol "Bayar Sekarang"
- Midtrans popup harus muncul

---

## 📋 Keuntungan Solusi Ini

### ✅ Fleksibel
- Client key bisa berubah di `.env` tanpa deploy frontend
- Backend otomatis menggunakan config terbaru

### ✅ Aman
- Client key tidak terekspos di source code
- Frontend tidak perlu tahu nilai secret key

### ✅ Maintainable
- Satu sumber kebenaran (backend `.env`)
- Mudah update config di production

### ✅ Scalable
- Jika ada multiple environments, tinggal ubah `.env`
- Frontend otomatis mengikuti

---

## 🚨 Troubleshooting

### Issue: "Midtrans Snap tidak tersedia"
**Penyebab**: Script gagal di-load atau endpoint `/midtrans-config` error

**Solusi**:
1. Cek backend running di `http://localhost:8000`
2. Verify endpoint `GET /api/midtrans-config` 
3. Check `.env` file punya `MIDTRANS_CLIENT_KEY`
4. Lihat browser console untuk error details

---

### Issue: "Gagal memuat konfigurasi pembayaran"
**Penyebab**: Response dari endpoint error atau timeout

**Solusi**:
1. Test endpoint dengan curl
2. Check backend logs
3. Verify CORS configuration

---

### Issue: "Payment gateway error"
**Penyebab**: Client key atau Server key tidak valid

**Solusi**:
1. Verify `.env` punya `MIDTRANS_CLIENT_KEY` dan `MIDTRANS_SERVER_KEY` yang valid
2. Check credentials di Midtrans dashboard
3. Verify `MIDTRANS_IS_PRODUCTION` setting

---

## 📚 Endpoint API Reference

### GET /api/midtrans-config
Fetch Midtrans configuration dari backend

**Method**: `GET`  
**Auth**: ❌ Public  
**Response**:
```json
{
  "success": true,
  "data": {
    "client_key": "string",
    "is_production": boolean
  }
}
```

---

## 📄 Files yang Dimodifikasi

1. ✅ [BE/app/Http/Controllers/Api/OrderController.php](BE/app/Http/Controllers/Api/OrderController.php) - Tambah method `getMidtransConfig()`
2. ✅ [BE/routes/api.php](BE/routes/api.php) - Tambah route `/midtrans-config`
3. ✅ [FE/halaman.pembayaran.html](FE/halaman.pembayaran.html) - Update script loading
4. ✅ [FE/config.js](FE/config.js) - Remove hardcoded client key

---

## 🎉 Status

✅ **IMPLEMENTASI SELESAI**

Midtrans sekarang menggunakan configuration dari `.env` backend, dan frontend akan otomatis fetch client key saat page loading.
