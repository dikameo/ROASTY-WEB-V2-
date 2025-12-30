# Navbar Role-Based Implementation Guide

## Overview
Sistem navbar dinamis yang menampilkan berbagai elemen berdasarkan role user (Admin atau User Biasa).

## Fitur

### Untuk User Admin:
- ✅ **Admin Panel Button** - Tombol untuk akses admin dashboard
- ❌ **Cart Icon** - Tidak ditampilkan
- ❌ **Notification Icon** - Tidak ditampilkan

### Untuk User Biasa (Regular User):
- ❌ **Admin Panel Button** - Tidak ditampilkan
- ✅ **Cart Icon** - Menampilkan jumlah item di keranjang (dengan badge)
- ✅ **Notification Icon** - Untuk notifikasi

## Struktur HTML Navbar

Navbar menggunakan struktur HTML berikut (sudah diimplementasikan di semua halaman):

```html
<!-- Admin Button - Only for Admin Users -->
<a id="admin-button" href="admin_dashboard.html" class="hidden flex ...">
    <span class="material-symbols-outlined">admin_panel_settings</span>
    <span class="hidden md:inline">Admin Panel</span>
</a>

<!-- User Navigation Icons -->
<div id="user-icons" class="flex items-center gap-3">
    <!-- Notification Icon -->
    <button id="notification-button" class="hidden">
        <span class="material-symbols-outlined">notifications</span>
        <span id="notification-badge" class="hidden">...</span>
    </button>

    <!-- Cart Icon -->
    <button id="cart-button" class="hidden">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span id="cart-badge" class="hidden">0</span>
    </button>
</div>

<!-- Profile Button - For All Users -->
<div id="profile-button">
    <div id="profile-avatar">...</div>
    <span id="profile-name">...</span>
</div>
```

## Integrasi JavaScript

### 1. Import navbar-helper.js
Tambahkan di setiap halaman HTML (sebelum script utama):

```html
<script src="config.js"></script>
<script src="navbar-helper.js"></script>
<script>
    // Your page-specific scripts...
</script>
```

### 2. Halaman yang Sudah Diupdate:
- ✅ `Halaman.beranda.html`
- ✅ `halaman.detail.produk.html`
- ✅ `halaman.keranjang.belanja.html`
- ✅ `halaman.daftar.produk.html`
- ✅ `halaman.profil.html`
- ✅ `halaman.pembayaran.html`

## Fungsi-Fungsi Utama

### `initializeNavbar()`
Dipanggil otomatis saat DOM selesai dimuat. Fungsi ini:
- Membaca role user dari localStorage
- Menampilkan/menyembunyikan elemen navbar sesuai role
- Setup event listeners untuk cart dan notification buttons
- Update cart badge

### `updateCartBadge()`
Memperbarui tampilan badge di cart icon:
- Menampilkan jumlah total item di keranjang
- Menyembunyikan badge jika keranjang kosong

### `dispatchCartUpdateEvent()`
Mengirim custom event ketika cart diperbarui. Gunakan fungsi ini setelah mengubah localStorage cart

### `addToCart(productId, quantity)`
Helper function untuk menambah item ke keranjang:
```javascript
addToCart(123, 2); // Add product ID 123 with quantity 2
```

### `getCartTotal()`
Mendapatkan total jumlah item di keranjang:
```javascript
const total = getCartTotal(); // returns number
```

### `clearCart()`
Menghapus semua item dari keranjang

## Cara Kerja Logika Role

### Pengecekan Role:
```javascript
const user = JSON.parse(localStorage.getItem('user'));

if (user && user.role === 'admin') {
    // User adalah admin
    // Tampilkan admin button
} else {
    // User adalah regular user
    // Tampilkan cart dan notification icons
}
```

### Penyimpanan User Data:
User data disimpan di localStorage saat login, misalnya:
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "photo": "https://..."
}
```

Untuk admin:
```json
{
    "id": 2,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin",
    "photo": "https://..."
}
```

## Cara Mengupdate Cart

Setiap kali menambah item ke cart, dispatch event untuk update badge:

```javascript
// Add to cart
let cart = JSON.parse(localStorage.getItem('cart') || '[]');
cart.push({
    id: productId,
    quantity: qty
});
localStorage.setItem('cart', JSON.stringify(cart));

// Update badge
dispatchCartUpdateEvent();
```

Atau gunakan helper function:
```javascript
addToCart(productId, quantity);
```

## Testing

### Test dengan Admin User:
1. Login dengan akun admin
2. Verifikasi:
   - ✅ Admin Panel button terlihat
   - ❌ Cart icon tidak terlihat
   - ❌ Notification icon tidak terlihat

### Test dengan Regular User:
1. Login dengan akun regular user (contoh: john)
2. Verifikasi:
   - ❌ Admin Panel button tidak terlihat
   - ✅ Cart icon terlihat
   - ✅ Notification icon terlihat
3. Tambah item ke keranjang
4. Verifikasi:
   - ✅ Cart badge menunjukkan jumlah item
   - ✅ Badge hilang ketika keranjang kosong

## Customization

### Mengubah ID Element:
Jika ingin menggunakan ID yang berbeda, update di HTML dan navbar-helper.js:

```javascript
const adminBtn = document.getElementById('admin-button');
const notificationBtn = document.getElementById('notification-button');
const cartBtn = document.getElementById('cart-button');
const profileBtn = document.getElementById('profile-button');
```

### Menambah Fitur Notifikasi:
```javascript
function updateNotificationBadge(hasNotifications = false) {
    const notificationBadge = document.getElementById('notification-badge');
    if (hasNotifications) {
        notificationBadge.classList.remove('hidden');
    } else {
        notificationBadge.classList.add('hidden');
    }
}
```

### Event Listeners Custom:
```javascript
window.addEventListener('cartUpdated', function() {
    // Do something when cart is updated
    console.log('Cart updated!');
});
```

## Troubleshooting

### Navbar tidak menampilkan dengan benar:
1. Pastikan `navbar-helper.js` sudah di-import
2. Periksa console untuk error messages
3. Verifikasi localStorage memiliki data `user` dan `token`

### Cart badge tidak update:
1. Pastikan menggunakan `dispatchCartUpdateEvent()` setelah update cart
2. Periksa localStorage cart data
3. Reload halaman untuk test

### Admin button tidak hilang untuk regular user:
1. Verifikasi role di localStorage: `JSON.parse(localStorage.getItem('user')).role`
2. Periksa element ID di HTML harus `id="admin-button"`

## Informasi Lebih Lanjut

Lihat file `navbar-helper.js` untuk dokumentasi lengkap fungsi-fungsi yang tersedia.
