# 🧪 Product Detail Page - Test Cases & Mock Data

## Quick Start Testing

### 1. Setup Browser for Testing

Buka halaman index.html atau home, kemudian di browser console, jalankan:

```javascript
// Set mock product ID
sessionStorage.setItem('selectedProductId', '1');

// Navigate to product detail
window.location.href = 'halaman.detail.produk.html';
```

### 2. Mock API Response (untuk testing tanpa backend)

Jika backend belum ready, bisa mock response dengan mengganti fetch di console:

```javascript
// Di halaman detail produk, buka console (F12) dan run:

const mockProduct = {
  data: {
    id: 1,
    name: "Roasty Signature Espresso Blend - 1kg",
    price: 185000,
    original_price: 250000,
    stock: 120,
    rating: 4.9,
    review_count: 2100,
    sold_count: 5000,
    discussion_count: 120,
    description: "Nikmati cita rasa kopi premium dengan Roasty Signature Espresso Blend.",
    specifications: [
      "Origin: Aceh Gayo",
      "Process: Semi Wash",
      "Roast Level: Medium to Dark",
      "Notes: Dark Chocolate, Caramel, Spice",
      "Net Weight: 1000g / 1kg"
    ],
    notes: "Mohon pilih varian gilingan yang sesuai. Jika tidak memilih, akan dikirim dalam bentuk Biji Utuh.",
    image_urls: [
      "https://via.placeholder.com/500x500?text=Coffee+1",
      "https://via.placeholder.com/500x500?text=Coffee+2"
    ]
  }
};

// Then manually update UI:
document.getElementById('product-title').textContent = mockProduct.data.name;
document.getElementById('product-price').textContent = 'Rp ' + mockProduct.data.price.toLocaleString('id-ID');
// ... continue for other fields
```

## Test Cases

### Test 1: Normal Product Load ✅

**Precondition:**
- API endpoint `/products/1` returns valid product data
- Browser local storage punya token

**Steps:**
1. Set `sessionStorage.selectedProductId = '1'`
2. Open `halaman.detail.produk.html`
3. Wait for API call to complete

**Expected Result:**
```
✓ Judul produk: "Roasty Signature Espresso Blend - 1kg"
✓ Harga: "Rp 185.000"
✓ Rating: "4.9" dengan 5 bintang
✓ Ulasan: "(2.1rb Ulasan)"
✓ Terjual: "Terjual 5rb+"
✓ Diskusi: "Diskusi (120)"
✓ Stok: "99+" (karena stock > 99)
✓ Deskripsi tampil lengkap
✓ Spesifikasi list tampil
✓ Gambar tampil
✓ Subtotal: "Rp 185.000" (1 × 185000)
```

### Test 2: Product dengan Diskon ✅

**Precondition:**
- Product punya `original_price: 250000` dan `price: 185000`

**Steps:**
1. Load product dengan diskon
2. Periksa harga section

**Expected Result:**
```
✓ Harga saat ini: "Rp 185.000"
✓ Harga original: "Rp 250.000" (visible)
✓ Diskon badge: "26% OFF" (visible)
✓ Diskon dihitung benar: (250000-185000)/250000 * 100 = 26%
```

**Formula Check:**
```
discount% = ((original - current) / original) * 100
          = ((250000 - 185000) / 250000) * 100
          = (65000 / 250000) * 100
          = 26%
```

### Test 3: Product Tanpa Diskon ✅

**Precondition:**
- Product tanpa `original_price` atau `original_price == price`

**Steps:**
1. Load product tanpa diskon
2. Periksa harga section

**Expected Result:**
```
✓ Harga saat ini: "Rp 185.000"
✓ Harga original: (hidden)
✓ Diskon badge: (hidden)
```

### Test 4: Large Number Formatting ✅

**Precondition:**
```json
{
  "review_count": 2150,
  "sold_count": 5500,
  "stock": 1200
}
```

**Steps:**
1. Load product dengan large numbers
2. Verifikasi formatting

**Expected Result:**
```
✓ Review count: "(2.1rb Ulasan)" - format dengan 1 decimal
✓ Sold count: "Terjual 5.5rb+" - format dengan 1 decimal
✓ Stock: "99+" - karena > 99
```

### Test 5: Star Rating Generation ✅

**Test Data:**

| Rating | Expected | Star Display |
|--------|----------|--------------|
| 5.0 | 5 filled | ⭐⭐⭐⭐⭐ |
| 4.5 | 5 filled (rounded) | ⭐⭐⭐⭐⭐ |
| 4.4 | 4 filled | ⭐⭐⭐⭐☆ |
| 3.0 | 3 filled | ⭐⭐⭐☆☆ |
| 1.0 | 1 filled | ⭐☆☆☆☆ |
| 0 | 0 filled | ☆☆☆☆☆ |

**Steps:**
1. Load products dengan berbagai rating
2. Verifikasi star display

**Expected Result:**
```javascript
// Star generation logic:
const rating = Math.round(product.rating);
for (let i = 0; i < 5; i++) {
  if (i < rating) {
    // filled star
  } else {
    // empty star
  }
}
```

### Test 6: Quantity Change ✅

**Steps:**
1. Page load dengan quantity = 1
2. Click "+" button 3 kali
3. Verifikasi quantity dan subtotal

**Expected Result:**
```
Step 1: quantity = 1, subtotal = Rp 185.000
Step 2: quantity = 2, subtotal = Rp 370.000
Step 3: quantity = 3, subtotal = Rp 555.000
Step 4: quantity = 4, subtotal = Rp 740.000

✓ Subtotal dihitung benar: price × quantity
✓ Subtotal di-update real-time setiap klik
```

### Test 7: Quantity Validation ✅

**Steps:**
1. Input quantity melebihi max
2. Input quantity di bawah minimum
3. Verifikasi boundary

**Expected Result:**
```
✓ Min quantity = 1 (tidak bisa di bawah 1)
✓ Max quantity = 99 (tidak bisa melebihi 99)
✓ Input value auto-correct jika invalid
✓ Quantity input hanya accept number
```

### Test 8: Specification Format - String ✅

**Test Data:**
```json
{
  "specifications": "Origin: Aceh Gayo, Process: Semi Wash, Roast: Medium"
}
```

**Expected Result:**
```html
<ul class="list-disc pl-5 mt-2 space-y-1">
  <li>Origin: Aceh Gayo</li>
  <li>Process: Semi Wash</li>
  <li>Roast: Medium</li>
</ul>
```

### Test 9: Specification Format - Array ✅

**Test Data:**
```json
{
  "specifications": [
    "Origin: Aceh Gayo",
    "Process: Semi Wash",
    "Roast Level: Medium to Dark"
  ]
}
```

**Expected Result:**
```html
<ul class="list-disc pl-5 mt-2 space-y-1">
  <li>Origin: Aceh Gayo</li>
  <li>Process: Semi Wash</li>
  <li>Roast Level: Medium to Dark</li>
</ul>
```

### Test 10: Specification Format - Object ✅

**Test Data:**
```json
{
  "specifications": {
    "origin": "Aceh Gayo",
    "process": "Semi Wash",
    "roast": "Medium to Dark"
  }
}
```

**Expected Result:**
```html
<ul class="list-disc pl-5 mt-2 space-y-1">
  <li>Aceh Gayo</li>
  <li>Semi Wash</li>
  <li>Medium to Dark</li>
</ul>
```

### Test 11: Missing Optional Fields ✅

**Test Data:**
```json
{
  "id": 1,
  "name": "Minimal Product",
  "price": 100000,
  "stock": 50
  // missing: original_price, rating, review_count, etc
}
```

**Steps:**
1. Load product dengan minimal fields
2. Verify halaman tetap berfungsi

**Expected Result:**
```
✓ Halaman load tanpa error
✓ Original price: (hidden)
✓ Discount badge: (hidden)
✓ Rating: tidak ditampilkan (atau default value)
✓ Review count: tidak ditampilkan
✓ Description: default "Produk tidak memiliki deskripsi"
✓ Semua elemen lain tetap functional
```

### Test 12: API Error Handling ✅

**Precondition:**
- API endpoint returns error (500, 404, etc)

**Steps:**
1. Set invalid productId
2. Try load halaman

**Expected Result:**
```
✓ Alert: "Gagal memuat detail produk. Silakan coba lagi."
✓ No page crash
✓ Browser console shows error log
✓ User dapat retry (refresh halaman)
```

### Test 13: No Product ID ✅

**Precondition:**
- sessionStorage tidak ada selectedProductId

**Steps:**
1. Clear sessionStorage
2. Open halaman detail produk

**Expected Result:**
```
✓ Console error: "No product ID found"
✓ Halaman tidak crash
✓ User redirect atau show error message
```

### Test 14: Currency Formatting ✅

**Test Data:**
```javascript
[
  { amount: 185000, expected: "Rp 185.000" },
  { amount: 1000000, expected: "Rp 1.000.000" },
  { amount: 50000, expected: "Rp 50.000" },
  { amount: 99999, expected: "Rp 99.999" },
  { amount: 0, expected: "Rp 0" }
]
```

**Steps:**
1. Load products dengan berbagai harga
2. Verify formatting

**Expected Result:**
```
✓ Format: "Rp X.XXX" dengan locale 'id-ID'
✓ Menggunakan toLocaleString('id-ID')
✓ Ribuan separator dengan titik (.)
✓ Tidak ada desimal untuk harga
```

### Test 15: Image Loading ✅

**Precondition:**
```json
{
  "image_urls": [
    "storage/products/image1.jpg",
    "storage/products/image2.jpg",
    "storage/products/image3.jpg"
  ]
}
```

**Steps:**
1. Load product dengan multiple images
2. Verify main image
3. Verify thumbnails

**Expected Result:**
```
✓ Main image: loaded & displayed
✓ Thumbnail 1,2,3: loaded
✓ Image URLs normalized dengan assets base URL
✓ Placeholder "+3" visible jika > 5 images
```

## Manual Testing Workflow

### Scenario 1: Complete User Journey

```
1. User Login
2. User Browse Products
3. User Click Product → Set selectedProductId in sessionStorage
4. User Open halaman.detail.produk.html
5. API Load Product Data → ✅ Check Console
6. UI Update dengan Data → ✅ Visual Check
7. User Adjust Quantity → ✅ Check Subtotal
8. User Click "Tambah ke Keranjang" → Check localStorage
9. User Click "Beli Langsung" → Redirect to Payment
```

### Scenario 2: Edge Cases

```
1. Product dengan max stock (999+)
   → Expect: "99+"
   
2. Product dengan low stock (5)
   → Expect: "5"
   
3. Product dengan 0 reviews
   → Expect: "(0 Ulasan)"
   
4. Product tanpa description
   → Expect: Fallback text atau empty
   
5. Product dengan rating 0
   → Expect: No stars atau 0 stars
```

## Browser Console Commands for Testing

```javascript
// Check current product
console.log(currentProduct);

// Check current quantity
console.log(currentQuantity);

// Manually test updateSubtotal
updateSubtotal();

// Check cart
console.log(JSON.parse(localStorage.getItem('cart')));

// Get all product-related elements
console.log({
  title: document.getElementById('product-title'),
  price: document.getElementById('product-price'),
  rating: document.getElementById('product-rating'),
  stock: document.getElementById('stock-amount'),
  description: document.getElementById('product-description'),
  subtotal: document.getElementById('subtotal-price')
});

// Simulate quantity change
currentQuantity = 5;
updateSubtotal();
```

## Performance Testing

**Network Tab Check:**
- API call time < 500ms
- Image load time < 1s
- Total page load < 2s

**Console Check:**
- No errors
- No warnings
- No 404s for images

## Accessibility Testing

- [ ] Tab navigation works
- [ ] Keyboard-only users dapat access semua buttons
- [ ] Form fields punya label
- [ ] Image punya alt text
- [ ] Color contrast sufficient

---

**Total Test Cases**: 15 basic + edge cases  
**Estimated Time**: 30-45 minutes for full manual testing

Run through checklist untuk memastikan implementasi 100% correct!
