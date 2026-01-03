# Panduan Integrasi Midtrans Payment Gateway

## Status Implementasi ✅

Integrasi Midtrans dengan Roasty telah diimplementasikan dengan benar di **Sandbox Mode**.

## 1. Konfigurasi Backend (.env)

```env
# Midtrans Payment Gateway (Sandbox Mode) ✅
MIDTRANS_MERCHANT_ID=G610858736
MIDTRANS_SERVER_KEY=Mid-server-PnwPw7x7LEh_XdWf_0sFUQM9
MIDTRANS_CLIENT_KEY=Mid-client-xHIl5auaQWqaNfVJ
MIDTRANS_IS_PRODUCTION=false    # ✅ SANDBOX MODE - JANGAN UBAH!
```

**Catatan Penting:**
- `MIDTRANS_IS_PRODUCTION=false` untuk Sandbox (Development)
- Jika ingin production, ubah ke `true` dan ganti credentials
- Jangan share Server Key di frontend!

## 2. Payment Flow - Cara Kerja

```
User di Frontend (halaman.pembayaran.html)
    ↓
    ├─ Pilih metode pembayaran (GoPay, BCA VA, BNI VA, Mandiri VA, Permata VA, Kartu Kredit)
    │
    ├─ Click "Bayar Sekarang"
    │
    ↓
Backend API: POST /api/orders
    ├─ Validasi data order
    │
    ├─ Simpan order ke database dengan status "pendingPayment"
    │
    ├─ Generate Snap Token dari Midtrans (Config::setServerKey(), Snap::getSnapToken())
    │
    ↓
Frontend: Terima Snap Token
    ├─ Load Midtrans Snap SDK dari: https://app.midtrans.com/snap/snap.js
    │
    ├─ Panggil: window.snap.pay(snapToken)
    │
    ├─ Modal Midtrans muncul
    │
    ↓
User: Proses Pembayaran di Modal Midtrans
    ├─ Pilih metode pembayaran
    │
    ├─ Selesaikan pembayaran
    │
    ↓
Midtrans: Send Webhook
    └─ POST http://localhost:8000/api/midtrans-webhook
       ├─ Update order status
       │
       ├─ Log hasil pembayaran
       │
       ↓
Frontend: Callback Handler
    ├─ onSuccess: Tampilkan "Pembayaran berhasil"
    │
    ├─ onError: Tampilkan error message
    │
    ├─ onPending: Tampilkan status pending
    │
    └─ onClose: User tutup modal
```

## 3. Metode Pembayaran yang Didukung

| Metode | ID Midtrans | Status |
|--------|-------------|--------|
| GoPay | `gopay` | ✅ Aktif |
| BCA Virtual Account | `bca_va` | ✅ Aktif |
| BNI Virtual Account | `bni_va` | ✅ Aktif |
| Mandiri Virtual Account | `mandiri_va` | ✅ Aktif |
| Permata Virtual Account | `permata_va` | ✅ Aktif |
| Kartu Kredit | `credit_card` | ✅ Aktif |

**Catatan:** Frontend harus mengirim payment method ID yang benar ke backend untuk enabled_payments configuration.

## 4. Order Status Flow

```
Order dibuat
    ↓
"pendingPayment"  ← Order menunggu pembayaran
    ↓
    ├─ Pembayaran berhasil → "paid"
    │
    ├─ Pembayaran pending → "pendingPayment" (tetap)
    │
    ├─ Pembayaran ditolak → "cancelled"
    │
    ├─ Pembayaran expired → "expired"
    │
    └─ Refund → "refunded"
```

## 5. Endpoint API

### Public Endpoints (Tidak butuh Authentication)

#### GET `/api/midtrans-config`
**Tujuan:** Ambil client key untuk frontend
```json
{
    "success": true,
    "data": {
        "client_key": "Mid-client-xHIl5auaQWqaNfVJ",
        "is_production": false
    }
}
```

#### POST `/api/midtrans-webhook`
**Tujuan:** Menerima callback dari Midtrans saat pembayaran selesai
```json
{
    "order_id": "ORD-20250101-001",
    "status_code": "200",
    "transaction_status": "settlement",
    "gross_amount": 238000,
    "payment_type": "gopay",
    "signature_key": "..."
}
```

### Protected Endpoints (Butuh Authentication)

#### POST `/api/orders`
**Tujuan:** Buat order baru dan generate snap token
```json
{
    "items": [
        {"product_id": 1, "quantity": 2, "price": 50000}
    ],
    "shipping_address": "Jalan Mawar 123",
    "payment_method": "gopay",
    "subtotal": 100000,
    "shipping_cost": 15000,
    "total": 115000
}
```

**Response:**
```json
{
    "success": true,
    "message": "Order created successfully",
    "data": {
        "order": {
            "id": "ORD-20250101-001",
            "user_id": "...",
            "status": "pendingPayment",
            "total": 115000,
            "order_date": "2025-01-01T10:00:00Z"
        },
        "snap_token": "0123456789abcdef0123456789abcdef"
    }
}
```

## 6. Testing Checkout

### Langkah Testing:

1. **Login User**
   - Buka halaman.beranda.html
   - Login dengan akun user

2. **Tambah Produk ke Keranjang**
   - Pilih produk
   - Klik "Tambah ke Keranjang"

3. **Checkout**
   - Buka keranjang
   - Klik "Checkout"
   - Sistem akan redirect ke halaman.pembayaran.html

4. **Pembayaran**
   - Pastikan data order muncul dengan benar
   - Pilih metode pembayaran
   - Klik "Bayar Sekarang"
   - Modal Midtrans akan muncul

5. **Testing di Midtrans Sandbox**
   - **GoPay:** Scan QR dengan aplikasi GoPay sandbox
   - **Virtual Account:** Lihat nomor VA dan transfer dengan nominal tepat
   - **Kartu Kredit:** Gunakan test card: `4011111111111111` (expiry: 12/25, CVV: 123)

### Sandbox Test Numbers:
```
GoPay Phone: +62 8123456789
Credit Card: 4011111111111111 / 12/25 / 123
```

## 7. Log & Debugging

### Backend Logs
Lihat di: `BE/storage/logs/laravel.log`

Cari log dengan pattern:
- `🔑 Midtrans Config Request` - Config diambil
- `Order saved to database` - Order berhasil dibuat
- `✅ Snap Token Generated Successfully` - Token snap berhasil
- `🔔 Midtrans Webhook Received` - Webhook diterima
- `Order status updated` - Status order diupdate

### Frontend Logs
Buka Console Browser (F12 → Console)

Cari log dengan pattern:
- `✓ Midtrans script loaded` - SDK Midtrans berhasil dimuat
- `💳 Payment button clicked` - Button pembayaran diklik
- `📤 Sending order to backend...` - Order dikirim ke backend
- `✅ Payment success` - Pembayaran berhasil
- `❌ Payment error` - Pembayaran gagal

### Simpan Debug Logs
Frontend menyimpan debug logs di localStorage:
```javascript
// Di Console, ketik:
JSON.parse(localStorage.getItem('payment_debug_logs'))
```

## 8. Troubleshooting

### Error: "Midtrans script tidak muncul"
**Solusi:**
- Pastikan internet stabil
- Refresh halaman
- Cek di Console apakah script berhasil dimuat
- Pastikan MIDTRANS_CLIENT_KEY benar

### Error: "Snap token invalid"
**Solusi:**
- Cek backend logs di `BE/storage/logs/laravel.log`
- Pastikan MIDTRANS_SERVER_KEY benar di .env
- Pastikan amount/total tidak nol

### Error: "Order creation failed"
**Solusi:**
- Lihat detail error di response JSON
- Pastikan user sudah login (ada token)
- Pastikan keranjang tidak kosong
- Cek validasi items di backend

### Status order tidak terupdate setelah pembayaran
**Solusi:**
- Webhook mungkin belum diterima
- Cek di log backend untuk log "Midtrans Webhook Received"
- Midtrans setting → Notification URL sudah benar ke: `http://localhost:8000/api/midtrans-webhook`

## 9. Security Best Practices ✅

1. **Server Key tidak boleh di-expose ke frontend** ✅ Sudah benar
2. **Webhook signature divalidasi** ✅ Implemented
3. **Order validation di backend** ✅ Implemented
4. **User hanya bisa lihat order mereka sendiri** ✅ Implemented
5. **Admin bisa lihat semua order** ✅ Implemented

## 10. Production Checklist

Sebelum go live:

- [ ] Ubah `MIDTRANS_IS_PRODUCTION=false` ke `true`
- [ ] Ganti credentials dengan production keys dari Midtrans Dashboard
- [ ] Update Notification URL di Midtrans Dashboard ke production domain
- [ ] Test pembayaran real dengan metode real
- [ ] Buat monitoring untuk webhook failures
- [ ] Backup database sebelum production
- [ ] Test refund flow
- [ ] Setup email notification untuk order

## 11. Contact & Support

**Midtrans Support:**
- Dashboard: https://dashboard.midtrans.com
- Documentation: https://docs.midtrans.com
- Sandbox Testing: https://simulator.midtrans.com

---

**Last Updated:** 31 Desember 2025  
**Status:** ✅ Ready for Testing  
**Mode:** Sandbox (Development)
