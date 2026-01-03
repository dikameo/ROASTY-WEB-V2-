# 📋 TESTING CHECKLIST - Midtrans Integration

## Pre-Testing Setup

- [ ] Backend berjalan: `php artisan serve` (port 8000)
- [ ] Database sudah migrasi dengan table orders
- [ ] File `.env` di folder BE sudah configured
- [ ] Midtrans credentials ada di .env:
  - [ ] MIDTRANS_MERCHANT_ID
  - [ ] MIDTRANS_SERVER_KEY
  - [ ] MIDTRANS_CLIENT_KEY
  - [ ] MIDTRANS_IS_PRODUCTION=false ✓

---

## 🎯 Test Case 1: Happy Path (GoPay)

**Objective:** Test pembayaran GoPay dari awal sampai selesai

### Steps
1. [ ] Buka browser: `http://localhost:8000/FE/Halaman.beranda.html`
2. [ ] Login user
3. [ ] Tambah 1-2 produk ke keranjang
4. [ ] Buka keranjang → Checkout
5. [ ] Redirect ke halaman.pembayaran.html
6. [ ] Verifikasi data tampil:
   - [ ] Alamat pengiriman
   - [ ] Daftar produk
   - [ ] Metode pengiriman
   - [ ] Ringkasan belanja
7. [ ] Scroll ke "Metode Pembayaran"
8. [ ] Pilih: **GoPay**
9. [ ] Klik: **"Bayar Sekarang"**
10. [ ] Tunggu modal Midtrans muncul (3-5 detik)
11. [ ] Modal muncul dengan pilihan payment
12. [ ] Pilih GoPay → Scan QR atau Tap
13. [ ] Gunakan test phone: +62 8123456789
14. [ ] Complete pembayaran di simulator
15. [ ] Lihat pesan: "Pembayaran berhasil"
16. [ ] Halaman redirect ke beranda

### Verifikasi
- [ ] Browser console (F12):
  - [ ] ✓ "Order created"
  - [ ] ✓ "Snap token received"
  - [ ] ✓ "snap.pay() called"
  - [ ] ✓ "Payment success"
- [ ] Backend log (`BE/storage/logs/laravel.log`):
  - [ ] ✓ "Order saved to database"
  - [ ] ✓ "Snap Token Generated Successfully"
  - [ ] ✓ "Midtrans Webhook Received"
  - [ ] ✓ "Order status updated" (paid)
- [ ] Database:
  ```sql
  SELECT id, status, payment_method FROM orders WHERE id='ORD-...';
  -- Status harus: paid
  -- payment_method harus: gopay
  ```

---

## 🎯 Test Case 2: Virtual Account (BCA)

**Objective:** Test pembayaran Virtual Account

### Steps
1. [ ] Login user baru (atau same user, buat order baru)
2. [ ] Checkout dengan produk berbeda
3. [ ] Pilih payment: **BCA Virtual Account**
4. [ ] Klik "Bayar Sekarang"
5. [ ] Modal muncul
6. [ ] Pilih BCA VA
7. [ ] Lihat nomor VA yang di-generate
8. [ ] Transfer dengan nominal **TEPAT** (jangan kurang/lebih)
9. [ ] Tunggu 1-2 detik untuk webhook
10. [ ] Lihat status updated

### Verifikasi
- [ ] Modal muncul dengan VA number
- [ ] Payment berhasil setelah transfer
- [ ] Status order: paid
- [ ] Log "Webhook Received"

---

## 🎯 Test Case 3: Credit Card

**Objective:** Test pembayaran Kartu Kredit

### Steps
1. [ ] Checkout dengan produk
2. [ ] Pilih: **Kartu Kredit**
3. [ ] Klik "Bayar Sekarang"
4. [ ] Modal muncul
5. [ ] Isikan:
   - Card: `4011111111111111`
   - Expiry: `12/25`
   - CVV: `123`
   - Name: test
6. [ ] Click Pay
7. [ ] OTP muncul (isikan: 123456)
8. [ ] Payment sukses

### Verifikasi
- [ ] Modal muncul dengan form kartu
- [ ] Payment processed
- [ ] Status order: paid

---

## 🎯 Test Case 4: Payment Error (Deny)

**Objective:** Test error handling jika pembayaran ditolak

### Steps
1. [ ] Checkout dengan produk
2. [ ] Pilih payment method
3. [ ] Klik "Bayar Sekarang"
4. [ ] Modal muncul
5. [ ] SENGAJA DENY/CANCEL pembayaran di modal
6. [ ] Klik tombol "Deny" atau "Cancel"
7. [ ] Lihat error message

### Verifikasi
- [ ] Error message muncul: "Pembayaran gagal"
- [ ] Modal close
- [ ] Button "Bayar Sekarang" kembali aktif
- [ ] Keranjang TIDAK dikosongkan
- [ ] Order status: masih pendingPayment (atau cancelled)
- [ ] User bisa retry pembayaran

---

## 🎯 Test Case 5: User Close Modal

**Objective:** Test jika user tutup modal tanpa bayar

### Steps
1. [ ] Checkout
2. [ ] Klik "Bayar Sekarang"
3. [ ] Modal muncul
4. [ ] SENGAJA TUTUP modal (X button atau Escape)
5. [ ] Modal disappear

### Verifikasi
- [ ] Modal close
- [ ] Button "Bayar Sekarang" kembali aktif
- [ ] Keranjang tetap ada (tidak dikosongkan)
- [ ] Order status: pendingPayment
- [ ] User bisa checkout lagi

---

## 🎯 Test Case 6: Network Error

**Objective:** Test jika koneksi error saat checkout

### Steps
1. [ ] Checkout
2. [ ] Saat klik "Bayar Sekarang", matikan internet (atau throttle network)
3. [ ] Lihat error message

### Verifikasi
- [ ] Error message muncul
- [ ] Button re-enabled
- [ ] Keranjang tidak berubah
- [ ] Tidak ada looping/retry infinite

---

## 🎯 Test Case 7: Different Payment Methods

**Objective:** Test semua metode pembayaran

| Method | Test with | Expected |
|--------|-----------|----------|
| GoPay | +62 8123456789 | ✓ Success |
| BCA VA | Transfer VA | ✓ Success |
| BNI VA | Transfer VA | ✓ Success |
| Mandiri VA | Transfer VA | ✓ Success |
| Permata VA | Transfer VA | ✓ Success |
| Credit Card | 4011111111111111 | ✓ Success |

- [ ] Test semua 6 metode
- [ ] Catat time untuk each payment
- [ ] Verify all dapat status "paid"

---

## 🔍 Logging Verification

### Frontend Logs (Browser Console)

Expected logs saat payment:
```
✓ loadMidtransScript() called
✓ Midtrans Config Request
✓ Config diterima, client key: Mid-client-...
✓ Midtrans script loaded
✓ window.snap siap digunakan
✓ Payment button found, setting up...
✓ Payment button clicked
✓ Selected payment method: gopay (atau lainnya)
✓ Sending order to backend...
✓ Order created
✓ Snap token received
✓ Calling snap.pay() dengan callback
✓ snap.pay() called with callbacks
✓ Payment success (atau error/pending/close)
```

### Backend Logs (laravel.log)

```bash
tail -50 BE/storage/logs/laravel.log | grep -E "Order|Snap|Webhook|Midtrans"
```

Expected logs:
```
Order saved to database: ORD-XXXXX
✅ Snap Token Generated Successfully: token_preview...
🔔 Midtrans Webhook Received: status_code=200, transaction_status=settlement
Order status updated: pendingPayment → paid
```

---

## 🐛 Debugging Commands

Jika ada error, jalankan:

```bash
# 1. Check .env
grep MIDTRANS BE/.env

# 2. Check recent logs
tail -100 BE/storage/logs/laravel.log

# 3. Check orders in database
mysql -h localhost -u root roasty_db -e "SELECT id, status, payment_method, total FROM orders ORDER BY created_at DESC LIMIT 5;"

# 4. Check Laravel logs (real-time)
tail -f BE/storage/logs/laravel.log

# 5. Clear logs if needed
echo "" > BE/storage/logs/laravel.log
```

---

## 📊 Test Results Template

Copy & paste untuk document hasil test:

```
TEST DATE: _______________
TESTER: ___________________

TEST CASE 1 (GoPay):
- Order Created: ✓ / ✗
- Snap Token: ✓ / ✗
- Payment Success: ✓ / ✗
- Status Updated: ✓ / ✗
- Notes: _______________________

TEST CASE 2 (BCA VA):
- Order Created: ✓ / ✗
- VA Number Generated: ✓ / ✗
- Payment Success: ✓ / ✗
- Status Updated: ✓ / ✗
- Notes: _______________________

TEST CASE 3 (Credit Card):
- Order Created: ✓ / ✗
- Card Form: ✓ / ✗
- Payment Success: ✓ / ✗
- Status Updated: ✓ / ✗
- Notes: _______________________

TEST CASE 4 (Error Handling):
- Error Message: ✓ / ✗
- Button Re-enabled: ✓ / ✗
- No Looping: ✓ / ✗
- Notes: _______________________

TEST CASE 5 (Modal Close):
- Modal Close: ✓ / ✗
- Button Re-enabled: ✓ / ✗
- Cart Preserved: ✓ / ✗
- Notes: _______________________

OVERALL RESULT: PASS / FAIL

Issues Found:
1. ___________________________
2. ___________________________
3. ___________________________

Notes:
_______________________________
```

---

## ✅ Final Check

Sebelum dinyatakan "SELESAI TESTING":

- [ ] Semua 7 test cases PASS
- [ ] Tidak ada error looping
- [ ] Payment methods semua work
- [ ] Logs clean (no error spam)
- [ ] Database update benar
- [ ] Webhook reception verified
- [ ] Error handling works
- [ ] Modal behavior correct

---

## 🎉 Success Criteria

✅ **PASS** jika:
- Semua test case sukses
- Tidak ada infinite loop
- Order status update benar
- Payment methods work
- Error handling proper

❌ **FAIL** jika:
- Ada test yang fail
- Ada error looping
- Order status tidak update
- Webhook tidak diterima
- Payment method error

---

**Happy Testing! 🚀**

Date: 31 Desember 2025
