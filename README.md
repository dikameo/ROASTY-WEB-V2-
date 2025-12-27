# Roasty (CoffeeCommerce)


## 📑 Table of Contents

- [Backend API](#-backend-api)
  - [Deskripsi](#-deskripsi-backend)
  - [Fitur Utama](#-fitur-utama-backend)
  - [Tech Stack](#-tech-stack-backend)
  - [Instalasi & Setup](#-instalasi--setup-backend)
  - [Dokumentasi API](#-dokumentasi-api)
  - [Admin Panel](#-admin-panel)
  - [Testing](#-testing)
  - [Troubleshooting](#-troubleshooting-backend)
- [Frontend](#-frontend)
  - [Deskripsi](#-deskripsi-frontend)
  - [Fitur Utama](#-fitur-utama-frontend)
  - [Tech Stack](#-tech-stack-frontend)
  - [Instalasi & Setup](#-instalasi--setup-frontend)
  - [Dokumentasi Halaman](#-dokumentasi-halaman)
  - [Authentication Flow](#-authentication-flow)
  - [Troubleshooting](#-troubleshooting-frontend)
  - [Deployment](#-deployment)

---

# 🔧 Backend API

## 📖 Deskripsi Backend

Backend API untuk aplikasi e-commerce kopi **Roasty (CoffeeCommerce)** yang dibangun menggunakan Laravel 12, dengan fitur autentikasi JWT, integrasi payment gateway Midtrans, dan admin panel Filament.

## ✨ Fitur Utama Backend

- 🔐 **Autentikasi JWT** - Login, Register, Logout dengan token-based authentication
- 🛒 **Manajemen Produk** - CRUD produk kopi lengkap dengan gambar
- 🛍️ **Keranjang Belanja** - Add, update, delete items di cart
- 💳 **Payment Gateway** - Integrasi Midtrans untuk pembayaran
- 📦 **Manajemen Pesanan** - Tracking status pesanan
- 👤 **User Profile** - Manajemen profil dan alamat pengiriman
- 🎨 **Admin Panel** - Filament untuk dashboard administrator
- 📊 **Database** - PostgreSQL dengan Supabase

## 🛠️ Tech Stack Backend

- **Framework:** Laravel 12
- **PHP:** ^8.2
- **Database:** PostgreSQL (Supabase)
- **Authentication:** JWT (tymon/jwt-auth)
- **Payment Gateway:** Midtrans
- **Admin Panel:** Filament v3
- **File Storage:** AWS S3 / Local Storage
- **Testing:** PHPUnit

## 📋 Prerequisites

Pastikan sistem Anda sudah terinstal:

- PHP >= 8.2
- Composer
- PostgreSQL atau akses ke Supabase
- Node.js & NPM (untuk Filament assets)
- Git

## 🚀 Instalasi & Setup Backend

### 1. Clone Repository

```bash
git clone <repository-url>
cd endpoint_web
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

Buat file `.env` dengan menyalin dari `.env.example`:

```bash
cp .env.example .env
```

Kemudian edit `.env` dengan konfigurasi berikut:

```env
APP_NAME=CoffeeCommerce
APP_ENV=local
APP_KEY=base64:MjNTjlSxcbjGA2ZuNEF2Gb+oyIvibyA+kfzaqD/bdtw=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database Configuration (PostgreSQL - Supabase)
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.fiyodlfgfbcnatebudut
DB_PASSWORD=adminR0sty;4man

# Supabase Configuration
SUPABASE_URL=https://fiyodlfgfbcnatebudut.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZpeW9kbGZnZmJjbmF0ZWJ1ZHV0Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjI5OTQ0MDIsImV4cCI6MjA3ODU3MDQwMn0.pHruuh0dqQa3oUbwqYjbzEjzFiha0jFhcvO93DfkOlk
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZpeW9kbGZnZmJjbmF0ZWJ1ZHV0Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2Mjk5NDQwMiwiZXhwIjoyMDc4NTcwNDAyfQ.vdUdqGGfCEWsBzaTsQnIJv3jaY_i_SJt22gYWQBdU4o

# JWT Configuration
JWT_SECRET=OGUyNTc5OTEtMTg2NS00Y2Q0LWI1ZGUtMzM2MzY3ZDRiZmVlNTdkMDVjNDYtZmJkMi00ZGZkLWFlMjEtOGZkMzQ3ODQzYTcw
JWT_TTL=60

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="CoffeeCommerce"

# File Upload Configuration
FILESYSTEM_DISK=public
MAX_FILE_SIZE=10240
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf

# Midtrans Payment Gateway
MIDTRANS_SERVER_KEY=Mid-server-8yNrwcFuCeuKyU2MhSWy22Ad
MIDTRANS_CLIENT_KEY=Mid-client-KOAGQWpfEka2OKgh
MIDTRANS_IS_PRODUCTION=true

# Queue & Cache
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Generate JWT Secret

```bash
php artisan jwt:secret
```

### 6. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Build Assets (Filament)

```bash
npm run build
```

### 9. Jalankan Server

```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`

## 👨‍💼 Membuat Admin User

Untuk membuat user admin pertama kali:

```bash
php artisan tinker
```

Kemudian jalankan:

```php
$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@roasty.com',
    'password' => bcrypt('password'),
    'phone' => '08123456789',
    'role' => 'admin'
]);
```

Atau gunakan script `create_admin.php`:

```bash
php create_admin.php
```

## 📚 Dokumentasi API

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

#### 1. Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "08123456789"
}
```

#### 2. Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer"
  }
}
```

#### 3. Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### 4. Get Profile
```http
GET /api/profile
Authorization: Bearer {token}
```

### Product Endpoints

#### 1. List All Products
```http
GET /api/products?page=1
```

#### 2. Get Product Detail
```http
GET /api/products/{id}
```

### Cart Endpoints

#### 1. Get Cart
```http
GET /api/cart
Authorization: Bearer {token}
```

#### 2. Add to Cart
```http
POST /api/cart
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

#### 3. Update Cart Item
```http
PUT /api/cart/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "quantity": 3
}
```

#### 4. Remove from Cart
```http
DELETE /api/cart/{id}
Authorization: Bearer {token}
```

### Order Endpoints

#### 1. Create Order (Checkout)
```http
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_address_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

#### 2. Get User Orders
```http
GET /api/orders
Authorization: Bearer {token}
```

#### 3. Get Order Detail
```http
GET /api/orders/{id}
Authorization: Bearer {token}
```

### User Address Endpoints

#### 1. Get User Addresses
```http
GET /api/addresses
Authorization: Bearer {token}
```

#### 2. Create Address
```http
POST /api/addresses
Authorization: Bearer {token}
Content-Type: application/json

{
  "label": "Rumah",
  "recipient_name": "John Doe",
  "phone": "08123456789",
  "address": "Jl. Contoh No. 123",
  "city": "Jakarta",
  "province": "DKI Jakarta",
  "postal_code": "12345"
}
```

## 🎨 Admin Panel

Akses admin panel Filament di:
```
http://localhost:8000/admin
```

Login menggunakan kredensial admin yang telah dibuat.

**Fitur Admin Panel:**
- Dashboard statistik
- Manajemen produk
- Manajemen pesanan
- Manajemen user
- Role & Permission management

## 📁 Struktur Folder

```
endpoint_web/
├── app/
│   ├── Filament/         # Filament admin resources
│   ├── Helpers/          # Helper functions
│   ├── Http/
│   │   ├── Controllers/  # API Controllers
│   │   └── Middleware/   # Custom middleware
│   ├── Models/           # Eloquent models
│   └── Policies/         # Authorization policies
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Public assets
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── api.php           # API routes
│   └── web.php           # Web routes
└── storage/              # File storage
```

## 🧪 Testing

Jalankan test dengan perintah:

```bash
php artisan test
```

Atau dengan coverage:

```bash
php artisan test --coverage
```

## 🔧 Troubleshooting Backend

### Database Connection Error
- Pastikan konfigurasi database di `.env` sudah benar
- Cek koneksi ke Supabase
- Pastikan IP address Anda sudah di-whitelist di Supabase

### JWT Token Error
- Regenerate JWT secret: `php artisan jwt:secret`
- Clear cache: `php artisan config:clear`

### Storage Permission Error
```bash
chmod -R 775 storage bootstrap/cache
```

### Composer Memory Limit
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

## 📝 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# 💻 Frontend

## 📖 Deskripsi Frontend

Frontend website Frontend e-commerce kopi **Roasty (CoffeeCommerce)** yang dibangun menggunakan HTML, CSS (Tailwind CSS), dan Vanilla JavaScript. Website ini terintegrasi dengan backend Laravel API untuk fitur autentikasi, katalog produk, keranjang belanja, dan checkout.

## ✨ Fitur Utama

- 🏠 **Homepage** - Landing page dengan informasi produk unggulan
- 🛍️ **Toko** - Katalog produk kopi dengan sistem pagination
- 🛒 **Keranjang** - Manajemen item keranjang belanja
- 💳 **Checkout** - Proses pemesanan dengan integrasi Midtrans
- 👤 **User Profile** - Halaman profil dan manajemen akun
- 📦 **Pesanan** - Riwayat dan tracking pesanan
- 🔐 **Login/Register** - Autentikasi user
- ℹ️ **Tentang Kami** - Informasi tentang perusahaan
- 🎓 **Kursus** - Frontend Halaman informasi kursus kopi

## 🛠️ Tech Stack

- **HTML5** - Struktur halaman
- **CSS3** - Styling
- **Tailwind CSS** - Utility-first CSS framework
- **JavaScript (Vanilla)** - Interaktivitas dan API calls
- **Fetch API** - HTTP requests ke backend

## 📋 Prerequisites

- Web browser modern (Chrome, Firefox, Safari, Edge)
- Backend API harus sudah running di `http://localhost:8000`
- Text editor (VS Code, Sublime, dll)

## 🚀 Instalasi & Setup Frontend

### 1. Clone/Download Repository

```bash
git clone <repository-url>
cd TUBES_FRONTEND_WEB
```

### 2. Konfigurasi API

Edit file `config.js` dan sesuaikan dengan URL backend Anda:

```javascript
const CONFIG = {
    API_BASE_URL: "http://localhost:8000/api",
    assets: "http://localhost:8000/storage"
};
```

**Konfigurasi untuk Production:**

```javascript
const CONFIG = {
    API_BASE_URL: "https://your-domain.com/api",
    assets: "https://your-domain.com/storage"
};
```

### 3. Jalankan Website

#### Opsi 1: Live Server (Recommended)

Jika menggunakan VS Code, install extension **Live Server**:

1. Install extension "Live Server" di VS Code
2. Klik kanan pada `index.html`
3. Pilih "Open with Live Server"
4. Website akan terbuka di `http://127.0.0.1:5500`

#### Opsi 2: Python Simple HTTP Server

```bash
# Python 3
python -m http.server 8080

# Python 2
python -m SimpleHTTPServer 8080
```

Website akan terbuka di `http://localhost:8080`

#### Opsi 3: PHP Built-in Server

```bash
php -S localhost:8080
```

#### Opsi 4: Direct File

Bisa juga langsung buka file `index.html` di browser, tapi beberapa fitur mungkin tidak bekerja sempurna karena CORS.

## 📁 Struktur File

```
TUBES_FRONTEND_WEB/
├── index.html           # Homepage/Landing page
├── toko.html            # Halaman katalog produk
├── keranjang.html       # Halaman keranjang belanja
├── checkout.html        # Halaman checkout
├── pesanan.html         # Halaman riwayat pesanan
├── profile.html         # Halaman profil user
├── login.html           # Halaman login
├── register.html        # Halaman registrasi
├── lupapw.html          # Halaman lupa password
├── kursus.html          # Halaman kursus kopi
├── tentang-kami.html    # Halaman tentang perusahaan
└── config.js            # Konfigurasi API endpoint
```

## 📚 Dokumentasi Halaman

### 1. Homepage (`index.html`)
Landing page dengan:
- Hero section
- Produk unggulan
- Informasi promosi
- Call-to-action

### 2. Toko (`toko.html`)
Halaman katalog produk dengan:
- Grid display produk
- Pagination
- Filter (jika ada)
- Quick view produk

**Fitur:**
- Fetch products dari API `/products`
- Display gambar, nama, harga produk
- Pagination handling

### 3. Keranjang (`keranjang.html`)
Halaman keranjang belanja dengan:
- List item di keranjang
- Update quantity
- Remove item
- Subtotal dan total price
- Tombol checkout

**API Integration:**
```javascript
// Get cart items
GET /api/cart

// Add to cart
POST /api/cart
{
  "product_id": 1,
  "quantity": 2
}

// Update cart
PUT /api/cart/{id}
{
  "quantity": 3
}

// Remove from cart
DELETE /api/cart/{id}
```

### 4. Checkout (`checkout.html`)
Halaman proses pemesanan dengan:
- Form data pengiriman
- Pilihan alamat
- Summary pesanan
- Integrasi Midtrans payment

**Flow:**
1. User mengisi data pengiriman
2. Review pesanan
3. Klik "Bayar"
4. Redirect ke Midtrans payment page
5. Callback setelah payment success/failure

### 5. Pesanan (`pesanan.html`)
Halaman riwayat pesanan dengan:
- List semua pesanan user
- Status pesanan
- Detail pesanan
- Tracking information

**API Integration:**
```javascript
// Get user orders
GET /api/orders

// Get order detail
GET /api/orders/{id}
```

### 6. Profile (`profile.html`)
Halaman profil user dengan:
- Data profil (nama, email, phone)
- Alamat pengiriman
- Edit profil
- Change password

**API Integration:**
```javascript
// Get profile
GET /api/profile

// Update profile
PUT /api/profile
{
  "name": "John Doe",
  "phone": "08123456789"
}
```

### 7. Login (`login.html`)
Halaman login dengan:
- Form email & password
- Remember me option
- Link ke register
- Link lupa password

**API Integration:**
```javascript
// Login
POST /api/login
{
  "email": "user@example.com",
  "password": "password123"
}

// Response
{
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": { ... }
}

// Save token to localStorage
localStorage.setItem('token', response.token);
localStorage.setItem('user', JSON.stringify(response.user));
```

### 8. Register (`register.html`)
Halaman registrasi dengan:
- Form registrasi (nama, email, password, phone)
- Password confirmation
- Terms & conditions
- Link ke login

**API Integration:**
```javascript
// Register
POST /api/register
{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "phone": "08123456789"
}
```

## 🔐 Authentication Flow

### Login Process
```javascript
// 1. User login
const response = await fetch(`${CONFIG.API_BASE_URL}/login`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })
});

// 2. Save token
const data = await response.json();
localStorage.setItem('token', data.token);
localStorage.setItem('user', JSON.stringify(data.user));

// 3. Redirect to homepage
window.location.href = 'index.html';
```

### Protected Routes
```javascript
// Check if user is logged in
const token = localStorage.getItem('token');
if (!token) {
    window.location.href = 'login.html';
}

// Use token in API requests
const response = await fetch(`${CONFIG.API_BASE_URL}/profile`, {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
});
```

### Logout Process
```javascript
// 1. Call logout API
await fetch(`${CONFIG.API_BASE_URL}/logout`, {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`
    }
});

// 2. Clear local storage
localStorage.removeItem('token');
localStorage.removeItem('user');

// 3. Redirect to login
window.location.href = 'login.html';
```

## 🎨 Styling Guide

Website menggunakan **Tailwind CSS** via CDN:

```html
<script src="https://cdn.tailwindcss.com"></script>
```

### Custom Colors
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#a47148',
                secondary: '#897461',
                dark: '#181411'
            }
        }
    }
}
```

## 🔧 Troubleshooting Frontend

### CORS Error
Jika mendapat error CORS, pastikan:
1. Backend sudah configure CORS dengan benar
2. Request headers include `Accept: application/json`
3. Gunakan local server, jangan buka file HTML langsung

### Token Expired
```javascript
// Handle 401 Unauthorized
if (response.status === 401) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'login.html';
}
```

### API Connection Failed
- Pastikan backend API running di `http://localhost:8000`
- Check `config.js` sudah benar
- Buka browser console untuk lihat error detail

### Images Not Loading
- Pastikan `assets` URL di `config.js` benar
- Check storage link sudah dibuat di backend: `php artisan storage:link`
- Fallback ke placeholder: `https://via.placeholder.com/300`

## 📱 Responsive Design

Website sudah responsive untuk:
- 📱 Mobile (< 768px)
- 📱 Tablet (768px - 1024px)
- 💻 Desktop (> 1024px)

Gunakan Tailwind responsive classes:
```html
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Mobile: full width, Tablet: 50%, Desktop: 33% -->
</div>
```

## 🚀 Deployment

### Deploy ke Netlify/Vercel

1. Push code ke GitHub
2. Connect repository ke Netlify/Vercel
3. Update `config.js` dengan production API URL
4. Deploy!

### Deploy ke Hosting (cPanel)

1. Zip semua file
2. Upload ke public_html
3. Extract
4. Update `config.js` dengan production API URL

## 📝 Best Practices

### Error Handling
```javascript
try {
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error('Request failed');
    }
    const data = await response.json();
    // Process data
} catch (error) {
    console.error(error);
    alert('Terjadi kesalahan. Silakan coba lagi.');
}
```

### Loading States
```javascript
// Show loading
loadingElement.classList.remove('hidden');

// Fetch data
const data = await fetchData();

// Hide loading
loadingElement.classList.add('hidden');
```

### Input Validation
```javascript
// Validate email
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
if (!emailRegex.test(email)) {
    alert('Email tidak valid');
    return;
}

// Validate phone
const phoneRegex = /^08\d{8,11}$/;
if (!phoneRegex.test(phone)) {
    alert('Nomor telepon harus diawali 08 dan 10-13 digit');
    return;
}
```
---

## 👥 Tim Developer

**Roasty CoffeeCommerce Team**

