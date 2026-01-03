# 🎯 Quick Start - Midtrans Dynamic Loading

## 📋 Ringkas Saja (5 Menit untuk Understand)

### Yang Diubah:

**BACKEND** ✅
```php
// OrderController.php - TAMBAH method ini
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

// routes/api.php - TAMBAH route ini
Route::get('/midtrans-config', [OrderController::class, 'getMidtransConfig']);
```

**FRONTEND** ✅
```javascript
// halaman.pembayaran.html - TAMBAH function ini
async function loadMidtransScript() {
    const response = await fetch(`${API_URL}/midtrans-config`);
    const configData = await response.json();
    
    const script = document.createElement('script');
    script.src = 'https://app.midtrans.com/snap/snap.js';
    script.setAttribute('data-client-key', configData.data.client_key);
    document.getElementById('midtrans-script-container').appendChild(script);
}

// DOMContentLoaded - TAMBAH panggilan ini
loadMidtransScript();  // 👈 Load Midtrans sebelum payment

// halaman.pembayaran.html (HTML) - GANTI bagian ini
<!-- DARI: hardcoded script tag -->
<script src="https://app.midtrans.com/snap/snap.js" 
        data-client-key="Mid-client-KOAGQWpfEka2OKgh"></script>

<!-- JADI: container untuk dynamic script -->
<div id="midtrans-script-container"></div>

// config.js - HAPUS baris ini
MIDTRANS_CLIENT_KEY: "Mid-client-KOAGQWpfEka2OKgh"
```

---

## 🔍 Penjelasan dalam 2 Kalimat

**Sebelum**: Midtrans client key ditulis di-hardcode di frontend, jadi setiap kali di .env berubah harus update frontend.

**Sesudah**: Frontend sekarang fetch client key dari API backend saat halaman loading, jadi cukup update .env backend dan frontend otomatis menyesuaikan.

---

## ✅ Verification

```bash
# 1. Cek backend endpoint
curl http://localhost:8000/api/midtrans-config

# Expected Response:
# {
#   "success": true,
#   "data": {
#     "client_key": "Mid-client-xHI...",
#     "is_production": true
#   }
# }

# 2. Buka halaman pembayaran di browser
# 3. Buka console (F12) → lihat logs:
#    🔄 Fetching Midtrans config dari backend...
#    ✅ Midtrans config diterima dari backend
#    ✅ Midtrans Snap script berhasil di-load!
```

---

## 🎓 Konsep Penting

| Konsep | Penjelasan |
|--------|-----------|
| **Frontend Script Loading** | Script Midtrans dibuat dan di-load saat runtime (dinamis) bukan saat parse HTML |
| **Fetch dari API** | Client key tidak hardcoded, tapi diambil dari backend via API |
| **Dynamic Attributes** | Atribut `data-client-key` di-set dynamically sesuai response dari API |
| **Single Source of Truth** | Config Midtrans hanya ada di `BE/.env`, tidak duplikasi di frontend |

---

## 🧪 Testing Payment

```javascript
// 1. Load halaman pembayaran
window.location = 'halaman.pembayaran.html'

// 2. Console harus show:
console.log('✅ Midtrans Snap script berhasil di-load!')

// 3. Klik tombol "Bayar Sekarang"
// 4. Midtrans popup harus muncul dengan metode pembayaran

// 5. Done! 🎉
```

---

## 🚨 Jika Ada Error

### Error: "Midtrans Snap tidak tersedia"
→ Script gagal load, cek `/api/midtrans-config` endpoint

### Error: "Gagal memuat konfigurasi pembayaran"
→ Backend endpoint error, cek `.env` file punya `MIDTRANS_CLIENT_KEY`

### Console: "404 /api/midtrans-config"
→ Route belum di-register, tambahkan ke `routes/api.php`

---

## 📂 Files Modified

```
BE/
  ├── app/Http/Controllers/Api/OrderController.php  ✏️ Modified
  └── routes/api.php                                ✏️ Modified

FE/
  ├── halaman.pembayaran.html                       ✏️ Modified
  └── config.js                                     ✏️ Modified
```

---

## 💾 Commit Message (jika pakai git)

```
feat: implement dynamic Midtrans client key loading from backend

- Add getMidtransConfig() endpoint in OrderController
- Add /api/midtrans-config route (public)
- Implement loadMidtransScript() in payment page
- Remove hardcoded MIDTRANS_CLIENT_KEY from frontend
- Client key now fetched from backend .env at runtime
```

---

## 🎉 Status

✅ **SELESAI** - Midtrans sekarang dynamic, fetch dari .env backend

Selanjutnya: Test payment flow end-to-end
