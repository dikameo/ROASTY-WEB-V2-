# 🚀 QUICK START - Midtrans Integration

## ✅ IMPLEMENTASI SUDAH SELESAI

Halaman pembayaran di Roasty telah terhubung dengan **Midtrans Payment Gateway** dalam mode **Sandbox**.

---

## 🎯 Cara Menggunakan

### Step 1: Jalankan Backend
```bash
cd BE
php artisan serve
# Output: Starting Laravel development server on http://127.0.0.1:8000
```

### Step 2: Buka Frontend
```
Buka browser: http://localhost:8000/FE/Halaman.beranda.html
```

### Step 3: User Login/Register
- Login dengan email & password
- Atau register user baru

### Step 4: Tambah Produk ke Keranjang
- Lihat produk di halaman beranda
- Klik "Tambah ke Keranjang"

### Step 5: Checkout
- Buka keranjang belanja
- Klik "Checkout"
- Sistem otomatis redirect ke halaman.pembayaran.html

### Step 6: Lanjutkan Pembayaran
**Halaman pembayaran akan menampilkan:**
- ✅ Alamat pengiriman
- ✅ Daftar produk yang dibeli
- ✅ Metode pengiriman (Reguler/Next Day/Instant)
- ✅ **Metode pembayaran** ← Pilih di sini

**Metode Pembayaran Tersedia:**
- 📱 **GoPay** - Scan QRIS
- 🏦 **BCA Virtual Account** - Transfer VA
- 🏦 **BNI Virtual Account** - Transfer VA
- 🏦 **Mandiri Virtual Account** - Transfer VA
- 🏦 **Permata Virtual Account** - Transfer VA
- 💳 **Kartu Kredit** - Visa, MC, Amex

### Step 7: Click "Bayar Sekarang"
- Modal Midtrans akan muncul
- Pilih metode pembayaran yang lebih detail
- Ikuti instruksi untuk selesaikan pembayaran

---

## 🧪 Testing dengan Sandbox

Gunakan nomor test di bawah:

### GoPay Testing
```
Phone: +62 8123456789
Gunakan app GoPay sandbox untuk approve payment
```

### Virtual Account Testing
Sistem akan generate nomor VA
Transfer dengan nominal yang TEPAT (jangan kurang/lebih)

### Credit Card Testing
```
Card Number: 4011111111111111
Expiry: 12/25
CVV: 123
OTP: 123456 (jika diminta)
```

---

## 📊 Verifikasi Payment Status

### Di Frontend
1. Buka Browser Console (F12)
2. Lihat log dengan pattern:
   - ✅ `Payment success` - Pembayaran berhasil
   - ❌ `Payment error` - Pembayaran gagal
   - ⏳ `Payment pending` - Pembayaran pending

### Di Backend
1. Lihat log file: `BE/storage/logs/laravel.log`
2. Cari pattern:
   - ✅ `Order created successfully` - Order dibuat
   - ✅ `Snap Token Generated` - Token snap generated
   - ✅ `Midtrans Webhook Received` - Webhook diterima
   - ✅ `Order status updated` - Status order diupdate

### Di Database
Lihat tabel `orders`:
```sql
SELECT id, status, total, payment_method, created_at 
FROM orders 
ORDER BY created_at DESC;
```

Status order akan berubah:
- `pendingPayment` → Menunggu pembayaran
- `paid` → Pembayaran diterima ✅
- `cancelled` → Pembayaran ditolak
- `expired` → Pembayaran expired

---

## ⚙️ Konfigurasi Saat Ini

| Setting | Nilai |
|---------|-------|
| Mode | Sandbox (Development) |
| MIDTRANS_IS_PRODUCTION | false ✅ |
| Merchant ID | G610858736 |
| Server Key | Mid-server-PnwPw7x7LEh_XdWf_0sFUQM9 |
| Client Key | Mid-client-xHIl5auaQWqaNfVJ |

**Jangan ubah konfigurasi tanpa tahu apa yang dilakukan!**

---

## 🐛 Troubleshooting

### Modal Midtrans tidak muncul
```
1. Refresh halaman
2. Cek internet connection
3. Buka F12 → Console untuk lihat error
4. Cek jika localhost:8000 bisa diakses
```

### "Gagal membuat order"
```
1. Pastikan sudah login
2. Pastikan keranjang ada item
3. Lihat error message detail
4. Cek backend logs
```

### "Payment gateway error"
```
1. Cek .env MIDTRANS_SERVER_KEY & CLIENT_KEY
2. Cek MIDTRANS_IS_PRODUCTION=false (jangan true!)
3. Restart artisan serve
```

### Order tidak terupdate setelah bayar
```
1. Webhook mungkin belum diterima
2. Cek log: "🔔 Midtrans Webhook Received"
3. Tunggu beberapa saat (webhook delay)
4. Refresh halaman manual
```

---

## 📚 File Dokumentasi Lengkap

- **MIDTRANS_INTEGRATION_GUIDE.md** - Panduan lengkap & detail
- **MIDTRANS_IMPLEMENTATION_COMPLETE.md** - Summary perubahan
- **test_midtrans.sh** - Script testing
- **validate_midtrans_config.sh** - Validasi konfigurasi

---

## ✨ Yang Sudah Diimplementasikan

✅ Integrasi Midtrans API  
✅ Snap Token Generation  
✅ Payment Gateway Modal  
✅ Webhook Callback Handler  
✅ Order Status Management  
✅ Error Handling & Logging  
✅ Retry Logic  
✅ Security Validation  

---

## 🎓 Cara Kerja Teknis

### Frontend → Backend Flow
```
1. User klik "Bayar Sekarang"
2. Frontend kirim POST /api/orders
3. Backend generate Snap Token
4. Backend return snap_token ke frontend
5. Frontend panggil window.snap.pay(token)
6. Modal Midtrans muncul
```

### Midtrans → Backend Callback
```
1. User selesai payment di modal Midtrans
2. Midtrans kirim webhook ke /api/midtrans-webhook
3. Backend validate signature
4. Backend update order status
5. Payment sukses / gagal / pending
```

---

## ❓ FAQ

**Q: Apakah ini real payment atau test saja?**  
A: Ini sandbox mode (test), tidak ada uang nyata yang terambil. Gunakan test card/phone yang disediakan.

**Q: Bagaimana cara ganti ke production?**  
A: Ubah `MIDTRANS_IS_PRODUCTION=true` di .env dan ganti credentials dengan production keys. Tapi jangan lakukan jika belum siap!

**Q: Apa yang terjadi jika user tutup modal?**  
A: Order tetap tersimpan dengan status `pendingPayment`. User bisa bayar nanti atau checkout ulang.

**Q: Apakah webhook penting?**  
A: Ya! Webhook adalah cara Midtrans beritahu backend bahwa pembayaran sudah diterima. Tanpa webhook, order tidak akan terupdate.

---

## 🎯 Next Steps untuk Production

1. ✅ Test checkout dengan berbagai payment method
2. ✅ Test webhook callback
3. Setup email notification
4. Ubah ke MIDTRANS_IS_PRODUCTION=true
5. Ganti dengan production credentials
6. Update webhook URL di Midtrans Dashboard
7. Test dengan real payment
8. Setup monitoring & backup

---

## 📞 Butuh Bantuan?

### Lihat Log
```bash
# Backend logs
tail -f BE/storage/logs/laravel.log

# Frontend console
F12 → Console tab
```

### Cek Status Order
```bash
# Di Midtrans Dashboard
Transactions → Cari order ID
```

### Reset Untuk Testing Ulang
```bash
# Clear keranjang di browser
localStorage.removeItem('cart')
localStorage.removeItem('payment_debug_logs')
```

---

**Status: ✅ SIAP DITEST**  
**Mode: Sandbox (Development)**  
**Last Updated: 31 Desember 2025**
