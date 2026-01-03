# File Upload Implementation for Product Images

## Overview
Converted product image management from URL input to local file upload in the admin dashboard. Users can now select image files directly from their computer instead of pasting URLs.

## Frontend Changes

### 1. Image Input Fields (FE/admin_dashboard.html)

#### Add Product Form (Line ~65)
**Before:**
```html
<label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar</label>
<input type="url" id="productImage" required class="..." placeholder="https://...">
```

**After:**
```html
<label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
<input type="file" id="productImage" accept="image/*" required class="...">
<p id="productImageName" class="text-sm text-gray-500 mt-1"></p>
```

#### Edit Product Modal (Line ~158)
**Added:**
```html
<label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
<input type="file" id="editProductImage" accept="image/*" class="...">
<p id="editProductImageName" class="text-sm text-gray-500 mt-1"></p>
<p id="editProductCurrentImage" class="text-sm text-gray-600 mt-1"></p>
```

### 2. Form Submission Handlers

#### Add Product Handler (Line ~280)
**Changes:**
- Get file from `document.getElementById('productImage').files[0]`
- Build FormData instead of JSON:
  ```javascript
  const formData = new FormData();
  formData.append('name', ...);
  formData.append('price', ...);
  formData.append('description', ...);
  formData.append('category', ...);
  formData.append('stock', ...);
  formData.append('image', imageFile);  // Add file
  ```
- Send without Content-Type header (browser sets it automatically to multipart/form-data)
- Validate file selection before submit

#### Edit Product Handler (Line ~355)
**Changes:**
- Get optional file from `document.getElementById('editProductImage').files[0]`
- Build FormData with same structure
- Only append 'image' if user selected a new file
- Logging for debugging:
  - `🖼️ New image file: ${fileName}` when file provided
  - `📌 No new image selected, keeping existing` when no file

### 3. Event Listeners

#### Add Product Image Change (New)
```javascript
document.getElementById('productImage').addEventListener('change', (e) => {
    const fileName = e.target.files[0]?.name || '';
    document.getElementById('productImageName').textContent = 
        fileName ? `File dipilih: ${fileName}` : '';
});
```

#### Edit Product Image Change (New)
```javascript
document.getElementById('editProductImage').addEventListener('change', (e) => {
    const fileName = e.target.files[0]?.name || '';
    document.getElementById('editProductImageName').textContent = 
        fileName ? `File baru dipilih: ${fileName}` : '';
});
```

### 4. Edit Product Modal Population

Updated `editProduct()` function to handle both:
- `image_url` (singular) - for legacy compatibility
- `image_urls` (array) - from latest backend

```javascript
let currentImageUrl = product.image_url;
if (!currentImageUrl && product.image_urls && Array.isArray(product.image_urls) && product.image_urls.length > 0) {
    currentImageUrl = product.image_urls[0];
}
```

Shows current image name in modal: `Gambar saat ini: filename.png`

## Backend Support

### Backend Already Supports File Uploads ✅

**ProductController (BE/app/Http/Controllers/Api/ProductController.php)**

#### Store Method (Line ~99)
```php
// Handle image upload
if ($request->hasFile('image')) {
    $image = $request->file('image');
    $imageName = time() . '_' . $image->getClientOriginalName();
    $imagePath = $image->storeAs('uploads/products', $imageName, 'public');
    $data['image_urls'] = [Storage::url($imagePath)];
}
```

#### Update Method (Line ~194)
```php
// Handle image upload
if ($request->hasFile('image')) {
    $image = $request->file('image');
    $imageName = time() . '_' . $image->getClientOriginalName();
    $imagePath = $image->storeAs('uploads/products', $imageName, 'public');
    $data['image_urls'] = [Storage::url($imagePath)];
}
```

**Validation:**
- File type: jpg, jpeg, png
- Max size: 10240 KB (10 MB)
- Field name: `image`

**Storage:**
- Location: `/storage/app/public/uploads/products/`
- Format: `{timestamp}_{original_filename}`
- Returned as: `image_urls` array in product response

## How It Works

### Adding a Product
1. Admin fills form fields (name, price, description, etc.)
2. Admin clicks file input and selects image from computer
3. Selected filename appears below input: "File dipilih: product.jpg"
4. Admin submits form
5. Frontend validates file exists
6. FormData sent to `POST /api/products`
7. Backend receives file, stores in `/storage/app/public/uploads/products/`
8. Backend returns product with `image_urls` array containing file URL
9. Success message shown, form reset, products reloaded

### Editing a Product
1. Admin clicks "Edit" button on product
2. Modal opens with current product details
3. Current image filename shown: "Gambar saat ini: product.jpg"
4. Admin can optionally select new image
5. If new image selected, filename shows: "File baru dipilih: newimage.jpg"
6. Admin clicks "Simpan"
7. FormData sent to `PUT /api/products/{id}`
8. If file included, backend updates image; otherwise keeps existing
9. Success message shown, modal closed, products reloaded

## Testing Checklist

- [ ] Add new product with image file → Image URL stored and displays
- [ ] Edit product and change image → Old image replaced with new
- [ ] Edit product without changing image → Image unchanged
- [ ] Try to add product without selecting file → Alert "Silahkan pilih gambar terlebih dahulu"
- [ ] Upload various image formats (jpg, png) → All accepted
- [ ] Try to upload non-image file → Rejected by backend
- [ ] Try to upload file > 10MB → Rejected by backend
- [ ] Check Storage directory → Files stored with timestamp prefix
- [ ] Check API response → image_urls field populated correctly

## File Locations Modified

1. **FE/admin_dashboard.html**
   - Add product form input (line ~65)
   - Edit modal form input (line ~158)
   - Add product handler (line ~280)
   - File change listener for add (new)
   - editProduct function (line ~344)
   - File change listener for edit (new)
   - Edit form handler (line ~355)

## Notes

- Backend already had full file upload support
- No new backend endpoints needed
- Uses `FormData` API for multipart/form-data submission
- Browser automatically sets correct Content-Type header
- Optional image update on edit (keeps existing if not changed)
- Images stored in Laravel's public storage directory
- Timestamp added to filenames to prevent conflicts
- Images served via Storage::url() for secure access

## Browser Compatibility

- File API: All modern browsers (IE10+)
- FormData: All modern browsers (IE10+)
- Optional chaining (?.): Chrome 80+, Firefox 74+, Safari 13.1+
- Nullish coalescing (??): Chrome 80+, Firefox 75+, Safari 13.1+

All modern browsers fully supported. Consider polyfills if IE11 support needed.
