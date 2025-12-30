# 🔧 BACKEND API VERIFICATION GUIDE

## ⚠️ CRITICAL: Backend harus siap

Admin redirect ke `admin_dashboard.html` **HANYA BERFUNGSI** jika backend API mengirimkan `role` field dengan benar!

---

## 🎯 Backend API Requirements

### Endpoint: `POST /login`

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Success Response (Status 200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
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

**Error Response (Status 401):**
```json
{
  "success": false,
  "message": "Email atau password salah",
  "data": null
}
```

---

## 🔍 Critical Field: `role`

### ⭐ HARUS ADA!
Field `role` di dalam `data.user` **WAJIB** ada untuk redirect bekerja:

```javascript
// Frontend akan cek:
if (data.data.user && data.data.user.role === 'admin') {
  // Redirect ke admin_dashboard.html
}
```

### Valid Role Values:
```
"admin"  → Redirect ke admin_dashboard.html
"user"   → Redirect ke Halaman.beranda.html
```

---

## 🔧 Testing Backend API

### 1. Using cURL:
```bash
# Test admin login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "admin123"
  }' | jq .

# Expected output:
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "...",
    "user": {
      "id": 2,
      "name": "Admin",
      "email": "admin@example.com",
      "role": "admin",
      "photo": "..."
    }
  }
}
```

### 2. Using Postman:
```
1. Create new POST request
2. URL: http://localhost:8000/api/login
3. Headers:
   - Content-Type: application/json
   - Accept: application/json
4. Body (raw JSON):
   {
     "email": "admin@example.com",
     "password": "admin123"
   }
5. Send request
6. Check response untuk field "role"
```

### 3. Using Python:
```python
import requests
import json

url = 'http://localhost:8000/api/login'
payload = {
    'email': 'admin@example.com',
    'password': 'admin123'
}

response = requests.post(url, json=payload)
data = response.json()

print('Status:', response.status_code)
print('Response:', json.dumps(data, indent=2))

# Check role field
if data['data']['user']['role'] == 'admin':
    print('✅ Admin role detected!')
else:
    print('❌ Role is not admin:', data['data']['user']['role'])
```

---

## 📋 Backend Implementation Examples

### Laravel (PHP):
```php
// routes/api.php
Route::post('/login', [AuthController::class, 'login']);

// app/Http/Controllers/AuthController.php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }

    $user = Auth::user(); // HARUS LOAD dengan role!
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,  // ⭐ CRITICAL!
                'photo' => $user->photo
            ]
        ]
    ]);
}
```

### Node.js (Express + JWT):
```javascript
// routes/auth.js
router.post('/login', async (req, res) => {
    try {
        const { email, password } = req.body;

        // Validate input
        if (!email || !password) {
            return res.status(400).json({
                success: false,
                message: 'Email dan password diperlukan'
            });
        }

        // Find user with role!
        const user = await User.findOne({ email });
        
        if (!user || !(await user.comparePassword(password))) {
            return res.status(401).json({
                success: false,
                message: 'Email atau password salah'
            });
        }

        // Generate token
        const token = jwt.sign(
            { id: user._id, role: user.role },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );

        // Return response with role
        return res.json({
            success: true,
            message: 'Login berhasil',
            data: {
                token,
                user: {
                    id: user._id,
                    name: user.name,
                    email: user.email,
                    role: user.role,  // ⭐ CRITICAL!
                    photo: user.photo
                }
            }
        });

    } catch (error) {
        res.status(500).json({
            success: false,
            message: 'Server error: ' + error.message
        });
    }
});
```

### Python (Flask):
```python
from flask import Blueprint, request, jsonify
from werkzeug.security import check_password_hash
import jwt
from datetime import datetime, timedelta

auth_bp = Blueprint('auth', __name__, url_prefix='/api')

@auth_bp.route('/login', methods=['POST'])
def login():
    try:
        data = request.get_json()
        email = data.get('email')
        password = data.get('password')

        if not email or not password:
            return jsonify({
                'success': False,
                'message': 'Email dan password diperlukan'
            }), 400

        # Find user with role!
        user = User.query.filter_by(email=email).first()

        if not user or not check_password_hash(user.password, password):
            return jsonify({
                'success': False,
                'message': 'Email atau password salah'
            }), 401

        # Generate token
        token = jwt.encode({
            'id': user.id,
            'role': user.role,
            'exp': datetime.utcnow() + timedelta(hours=24)
        }, app.config['SECRET_KEY'], algorithm='HS256')

        # Return response with role
        return jsonify({
            'success': True,
            'message': 'Login berhasil',
            'data': {
                'token': token,
                'user': {
                    'id': user.id,
                    'name': user.name,
                    'email': user.email,
                    'role': user.role,  # ⭐ CRITICAL!
                    'photo': user.photo
                }
            }
        })

    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'Server error: {str(e)}'
        }), 500
```

---

## 🔐 Database User Model

### Laravel Migration:
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['user', 'admin'])->default('user');  // ⭐ Include role!
    $table->string('photo')->nullable();
    $table->timestamps();
});
```

### User Model (Eloquent):
```php
class User extends Model {
    protected $fillable = ['name', 'email', 'password', 'role', 'photo'];
    
    // Ensure role is included when querying
    protected $appends = ['role'];  // ⭐ Make sure included
}
```

### MongoDB Schema:
```javascript
const userSchema = {
    _id: ObjectId,
    name: String,
    email: String,
    password: String,
    role: String,  // ⭐ Include role!
    photo: String,
    createdAt: Date,
    updatedAt: Date
};
```

---

## ✅ Verification Checklist

- [ ] User model memiliki `role` field
- [ ] Database migration include role column
- [ ] Login query include select role field
- [ ] API response include `data.user.role`
- [ ] Role value adalah "admin" atau "user"
- [ ] No null values untuk role field
- [ ] Token dibuat dengan benar
- [ ] CORS headers configured jika different domain

---

## 🧪 Full Integration Test

### Scenario 1: Admin Login
```
1. POST /login dengan admin@example.com
2. Response include role: "admin"
3. Frontend redirect ke admin_dashboard.html
4. ✅ SUCCESS
```

### Scenario 2: User Login
```
1. POST /login dengan user@example.com
2. Response include role: "user"
3. Frontend redirect ke Halaman.beranda.html
4. ✅ SUCCESS
```

### Scenario 3: Invalid Credentials
```
1. POST /login dengan wrong password
2. Response status 401
3. Frontend show error message
4. ✅ SUCCESS
```

---

## 🚨 Common Backend Issues

### Issue 1: Role field missing in response
```
❌ WRONG:
"user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com"
    // ❌ MISSING: role field!
}

✅ CORRECT:
"user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "role": "admin"  // ✅ Include!
}
```

### Issue 2: Role value inconsistent
```
❌ WRONG:
- Sometimes "admin", sometimes "Admin", sometimes "ADMIN"
- Frontend only checks for lowercase "admin"

✅ CORRECT:
- Always return "admin" or "user" in lowercase
- Consistent across all responses
```

### Issue 3: Role loaded as null
```
❌ WRONG:
$user = User::find($id);
return $user; // Role might be null if not included

✅ CORRECT:
$user = User::with('role')->find($id);
// or
$user = User::find($id)->load('role');
```

---

## 📡 Debug API Response

### Using Frontend Console:
```javascript
// Login, then check response:
const user = JSON.parse(localStorage.getItem('user'));
console.log('Role:', user.role);
console.log('Is admin?', user.role === 'admin');

// Should output:
// Role: admin
// Is admin? true
```

### Using cURL:
```bash
# Check API response structure
curl -X POST http://api.example.com/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"pass"}' \
  | jq '.data.user.role'

# Should output:
# "admin"
```

---

## ✅ Before Going Live

Ensure:
1. ✅ All users have role value in database
2. ✅ API returns role field in login response
3. ✅ Role values are consistent ("admin" or "user")
4. ✅ Tested with both admin and user accounts
5. ✅ Token working for authenticated requests
6. ✅ CORS properly configured
7. ✅ No console errors or warnings

---

**Status:** Waiting for Backend Implementation

**Frontend Ready:** ✅ Yes (login.html updated)

**Next Step:** 
1. Verify backend API response includes role field
2. Test with actual admin & user accounts
3. Check redirect works correctly
4. Deploy to production

---

*Last Updated: December 30, 2025*
