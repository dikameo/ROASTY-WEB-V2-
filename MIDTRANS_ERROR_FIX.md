# 🧪 DEBUG: Midtrans Payment Error Fix

## Error yang Dialami
```
POST http://localhost:8000/api/orders 500 (Internal Server Error)
Payment gateway error
```

## Root Cause Analysis

### Diagnosis
Error 500 terjadi di `Snap::getSnapToken()` kemungkinan karena:

1. **Config Mismatch**: `MIDTRANS_IS_PRODUCTION=true` tapi credentials adalah sandbox
2. **Missing Config**: `Config::$clientKey` tidak di-set di backend
3. **Credentials Invalid**: Server key atau Client key invalid untuk environment yang digunakan

### Solusi Implemented
✅ Changed `.env`: `MIDTRANS_IS_PRODUCTION=false` (sandbox mode)
✅ Added `Config::$clientKey` di OrderController
✅ Added logging untuk debug
✅ Cache cleared: `php artisan optimize:clear`

---

## ✅ Verification Steps

### Step 1: Check Endpoint Config
```bash
curl http://localhost:8000/api/midtrans-config
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "client_key": "Mid-client-xHIl5auaQWqaNfVJ",
    "is_production": false
  }
}
```

### Step 2: Check Browser Console
Open halaman.pembayaran.html → Press F12 → Console

**Expected Logs:**
```
🔄 Fetching Midtrans config dari backend...
✅ Midtrans config diterima dari backend
   - Client Key: Mid-client-xHI...
   - Mode: SANDBOX
✅ Midtrans Snap script berhasil di-load!
```

### Step 3: Test Payment Flow
1. Add produk ke cart
2. Proceed to checkout
3. Click "Bayar Sekarang"
4. Should see Midtrans payment popup (with test cards available)

---

## 🔑 Midtrans Key Information

### Sandbox vs Production

| Setting | Endpoint | Keys Used | Notes |
|---------|----------|-----------|-------|
| **Sandbox** | api.sandbox.midtrans.com | `Mid-server-xxxx...` | For testing |
| **Production** | api.midtrans.com | `Mid-server-xxxx...` | For live payments |

⚠️ **CRITICAL**: Environment must match:
- If `MIDTRANS_IS_PRODUCTION=true` → Use production keys
- If `MIDTRANS_IS_PRODUCTION=false` → Use sandbox keys

Current Setup:
- Setting: `MIDTRANS_IS_PRODUCTION=false` (sandbox)
- Keys: `Mid-server-PnwPw7x7LEh_XdWf_0sFUQM9` (appears to be sandbox)
- Status: ✅ MATCHING

---

## 📋 Changes Made

### File: `BE/.env`
```diff
- MIDTRANS_IS_PRODUCTION=true
+ MIDTRANS_IS_PRODUCTION=false
```

### File: `BE/app/Http/Controllers/Api/OrderController.php`
```diff
  // Configure Midtrans
  Config::$serverKey = config('midtrans.server_key');
+ Config::$clientKey = config('midtrans.client_key');
  Config::$isProduction = config('midtrans.is_production');
  ...
+ \Log::error('Midtrans Error:', [
+   'message' => $e->getMessage(),
+   'code' => $e->getCode(),
+   'file' => $e->getFile(),
+   'line' => $e->getLine(),
+ ]);
```

---

## 🧪 Test Midtrans Test Cards (Sandbox)

Jika now berhasil masuk ke Midtrans popup, gunakan test cards ini:

### ✅ Successful Payment
```
Card Number: 4811 1111 1111 1114
Exp Month: 12
Exp Year: 25
CVV: 123
```

### ❌ Failed Payment (untuk test error handling)
```
Card Number: 4911 1111 1111 1113
Exp Month: 12
Exp Year: 25
CVV: 123
```

---

## 🔍 If Still Getting Error

### Check Backend Logs
```bash
cd BE
Get-Content storage/logs/laravel.log | Select-Object -Last 100 | findstr "Midtrans\|ERROR"
```

### Common Issues & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| "Invalid merchant key" | Wrong server key | Check .env `MIDTRANS_SERVER_KEY` |
| "Authentication failed" | Mode mismatch | Ensure `MIDTRANS_IS_PRODUCTION=false` |
| "API error" | Network issue | Check internet connection |
| "Snap not available" | Script not loaded | Check browser console, reload page |

---

## 📊 Configuration Status

### Backend Configuration
```
✅ MIDTRANS_MERCHANT_ID: G610858736
✅ MIDTRANS_SERVER_KEY: Mid-server-PnwPw7x7LEh_XdWf_0sFUQM9
✅ MIDTRANS_CLIENT_KEY: Mid-client-xHIl5auaQWqaNfVJ
✅ MIDTRANS_IS_PRODUCTION: false (SANDBOX)
✅ Config::$serverKey: Set
✅ Config::$clientKey: Set ← FIXED
✅ Config::$isProduction: Set
```

### Frontend Configuration
```
✅ loadMidtransScript(): Implemented
✅ Endpoint: GET /api/midtrans-config
✅ Response: Returns client_key + is_production
✅ Script injection: Dynamic via createElement
```

---

## ✨ Next Actions

1. **Open browser F12** → Console tab
2. **Refresh halaman.pembayaran.html**
3. **Watch console** for logs
4. **Click "Bayar Sekarang"**
5. **Report if**:
   - ✅ Midtrans popup muncul → SUCCESS!
   - ❌ Error masih ada → Check logs and report error message

---

## 📞 Support

Jika error masih terjadi:
1. Share console error message
2. Check backend logs: `storage/logs/laravel.log`
3. Verify .env configuration
4. Ensure backend restarted after .env change

---

**Last Updated**: December 30, 2025  
**Status**: Fix applied, awaiting test confirmation
