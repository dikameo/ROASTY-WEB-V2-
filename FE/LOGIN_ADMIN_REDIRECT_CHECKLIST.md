# ✅ LOGIN & ADMIN REDIRECT - CHECKLIST IMPLEMENTASI

## 📋 Persyaratan API Response

Backend API (`/login`) harus mengembalikan response dengan struktur berikut:

### Response Format (Success):
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "photo": "https://example.com/photo.jpg"
    }
  }
}
```

### Untuk Admin Response:
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "user": {
      "id": 2,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "admin",
      "photo": "https://example.com/admin-photo.jpg"
    }
  }
}
```

### Response Format (Error):
```json
{
  "success": false,
  "message": "Email atau password salah",
  "data": null
}
```

---

## ✨ Frontend Login Flow

### 1. **User Input Email & Password**
   ✓ Form submission dengan `#loginForm`

### 2. **Send POST Request ke API**
   ```javascript
   POST /login
   Content-Type: application/json
   {
     "email": "user@example.com",
     "password": "password123"
   }
   ```

### 3. **Validasi Response**
   ✓ Check HTTP status (200 = success)
   ✓ Check `data.data.token` exists
   ✓ Check `data.data.user` exists
   ✓ Check `user.role` field exists

### 4. **Simpan ke localStorage**
   ```javascript
   localStorage.setItem('token', data.data.token);
   localStorage.setItem('user', JSON.stringify(user));
   ```

### 5. **Redirect Berdasarkan Role**
   ```javascript
   if (user.role === 'admin') {
     redirect to admin_dashboard.html
   } else {
     redirect to Halaman.beranda.html
   }
   ```

---

## 🔍 Debugging Console Output

Setelah update, cek console browser (F12) untuk logs:

### Success Login (Regular User):
```
🔐 Login attempt with email: john@example.com
📡 Sending login request to: http://api.example.com/login
📊 Response status: 200
📦 Response data: {...}
👤 User data received: {id: 1, name: "John Doe", email: "john@example.com", role: "user"}
✅ Token disimpan ke localStorage
✅ User data disimpan ke localStorage
👤 User role: user
🔄 Redirect berdasarkan role: user
👤 Regular user login detected - redirecting to Halaman.beranda.html
```

### Success Login (Admin):
```
🔐 Login attempt with email: admin@example.com
📡 Sending login request to: http://api.example.com/login
📊 Response status: 200
📦 Response data: {...}
👤 User data received: {id: 2, name: "Admin User", email: "admin@example.com", role: "admin"}
✅ Token disimpan ke localStorage
✅ User data disimpan ke localStorage
👤 User role: admin
🔄 Redirect berdasarkan role: admin
👨‍💼 Admin login detected - redirecting to admin_dashboard.html
```

### Error Case:
```
🔐 Login attempt with email: test@example.com
📡 Sending login request to: http://api.example.com/login
📊 Response status: 401
❌ Login error: Email atau password salah
```

---

## 🧪 Testing Checklist

### Test 1: Regular User Login
- [ ] Login dengan email regular user (contoh: john@example.com)
- [ ] Check console untuk logs "user" role
- [ ] Verify redirect ke `Halaman.beranda.html`
- [ ] Verify localStorage.user memiliki `role: "user"`
- [ ] Verify navbar menampilkan cart & notification icons

### Test 2: Admin Login
- [ ] Login dengan email admin (contoh: admin@example.com)
- [ ] Check console untuk logs "admin" role
- [ ] Verify redirect ke `admin_dashboard.html`
- [ ] Verify localStorage.user memiliki `role: "admin"`
- [ ] Verify navbar menampilkan admin button

### Test 3: Invalid Credentials
- [ ] Login dengan email/password salah
- [ ] Verify error message ditampilkan
- [ ] Verify NOT redirected
- [ ] Verify localStorage empty

### Test 4: Network Error
- [ ] Disable network / matikan API server
- [ ] Login dan submit form
- [ ] Verify error message ditampilkan
- [ ] Check console error

### Test 5: Invalid API Response
- [ ] API return response tanpa `role` field
- [ ] Verify error message "Role field tidak ditemukan"
- [ ] Check console error

---

## 🔧 Backend API Requirements

Pastikan backend `/login` endpoint:

### ✅ Requirements:
1. **Accept POST Request**
   - Email field: `email`
   - Password field: `password`

2. **Return Proper JSON**
   - Always return valid JSON
   - Status 200 for success
   - Status 401 for invalid credentials
   - Status 400 for validation error

3. **Include Role Field**
   - `data.user.role` must be present
   - Valid values: `"admin"` or `"user"`
   - Case sensitive!

4. **Include Token**
   - `data.token` JWT or Bearer token
   - Required untuk API requests selanjutnya

5. **Include User Data**
   - `data.user.id`
   - `data.user.name`
   - `data.user.email`
   - `data.user.role` ⭐ **PENTING!**
   - `data.user.photo` (optional)

### Example Backend Implementation (Node.js/Express):
```javascript
router.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    
    // Validate user
    const user = await User.findOne({ email });
    if (!user || !user.comparePassword(password)) {
      return res.status(401).json({
        success: false,
        message: 'Email atau password salah'
      });
    }

    // Generate token
    const token = jwt.sign({ id: user.id }, JWT_SECRET);

    // Return response
    res.json({
      success: true,
      message: 'Login berhasil',
      data: {
        token,
        user: {
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role,  // ⭐ MUST INCLUDE!
          photo: user.photo
        }
      }
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: 'Server error'
    });
  }
});
```

---

## 📱 Storage Data Structure

### localStorage Keys Setelah Login:

**Key: `token`**
```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MX0.xxxxx
```

**Key: `user`**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "user",
  "photo": "https://example.com/photo.jpg"
}
```

---

## 🚀 Verifikasi Implementasi

Buka Browser DevTools (F12) dan cek:

### 1. Check API Response:
```javascript
// Di Console, ketik:
fetch('http://api.example.com/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email: 'admin@example.com', password: 'password' })
}).then(r => r.json()).then(d => console.log(d))
```

### 2. Check localStorage:
```javascript
// Di Console, ketik:
localStorage.getItem('user')
// Expected output:
// {"id":2,"name":"Admin","email":"admin@example.com","role":"admin"}

JSON.parse(localStorage.getItem('user')).role
// Expected output: "admin"
```

### 3. Check Redirect:
```javascript
// Buka F12 Network tab
// Login dan lihat navigation request ke admin_dashboard.html atau Halaman.beranda.html
```

---

## ⚠️ Common Issues & Solutions

### Issue: Tidak redirect ke admin_dashboard.html
**Solution:**
1. Check console logs untuk role value
2. Pastikan backend mengirim `role: "admin"` (case sensitive!)
3. Pastikan nama file `admin_dashboard.html` benar (case sensitive!)

### Issue: Role field undefined
**Solution:**
1. Check API response di Network tab
2. Pastikan response include field `role`
3. Verify Laravel/Backend query return role field

### Issue: Error "Role field tidak ditemukan"
**Solution:**
1. Update backend API untuk include role field
2. Ensure model User memiliki role column
3. Test API dengan Postman/Insomnia

### Issue: localStorage kosong setelah login
**Solution:**
1. Check if localStorage.setItem dijalankan
2. Check browser console untuk errors
3. Verify token dan user data tidak null

### Issue: Endless redirect loop
**Solution:**
1. Check admin_dashboard.html redirect logic
2. Ensure token validation di admin page
3. Check localStorage token tidak corrupt

---

## ✅ Final Checklist Sebelum Production

- [ ] Backend API mengirim `role` field
- [ ] Frontend login.html sudah updated
- [ ] navbar-helper.js sudah aktif di semua halaman
- [ ] Console logs bekerja dengan baik
- [ ] Admin login → redirect ke admin_dashboard.html ✓
- [ ] User login → redirect ke Halaman.beranda.html ✓
- [ ] Error handling berfungsi ✓
- [ ] localStorage token & user tersimpan ✓
- [ ] Role-based navbar muncul dengan benar ✓

---

**Last Updated:** December 30, 2025
**Status:** ✅ Ready for Testing
