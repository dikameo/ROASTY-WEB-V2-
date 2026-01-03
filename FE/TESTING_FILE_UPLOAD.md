# File Upload Testing Guide

## Test Environment Setup
- Frontend: http://localhost:3000 (or FE folder opened in browser)
- Backend: API endpoint configured in config.js
- Admin account: Already logged in

## Test Scenarios

### Test 1: Add Product with Image File ✓
1. Navigate to Admin Dashboard
2. Click "Tambah Produk" tab (should be visible by default)
3. Fill in form:
   - Nama Produk: "Test Coffee 1"
   - Harga: 50000
   - Deskripsi: "Test description"
   - **Gambar Produk: Select JPG/PNG file from computer**
   - Stok: 10
   - Kategori: "Test Category"
4. Check that selected filename appears below file input
5. Click "Tambah Produk"
6. Expected: 
   - "Produk berhasil ditambahkan!" alert
   - Form resets (filename disappears)
   - Products list reloads
   - New product appears in table with image stored

### Test 2: Edit Product - Update Image ✓
1. In Products tab, click "Edit" on any product
2. Modal opens showing current product details
3. "Gambar saat ini: filename.png" should appear (if product has image)
4. **Select new image file from computer**
5. Check that "File baru dipilih: newfilename.jpg" appears
6. Click "Simpan"
7. Expected:
   - "Produk berhasil diupdate!" alert
   - Modal closes
   - Products list reloads
   - New image replaces old image in storage

### Test 3: Edit Product - Keep Existing Image ✓
1. In Products tab, click "Edit" on any product
2. Modal opens showing current product details
3. "Gambar saat ini: filename.png" should appear
4. **Do NOT select new image** (leave file input empty)
5. Change another field (e.g., price)
6. Click "Simpan"
7. Expected:
   - "Produk berhasil diupdate!" alert
   - Modal closes
   - Products list reloads
   - Image unchanged (same filename in current image display)

### Test 4: Add Product Without Image ✓
1. Navigate to Add Product form
2. Fill all fields
3. **Do NOT select image file**
4. Click "Tambah Produk"
5. Expected:
   - Alert: "Silahkan pilih gambar terlebih dahulu"
   - Form stays open
   - No product added

### Test 5: Image File Validation ✓
1. Try to select non-image file (text file, PDF, etc.)
2. Click "Tambah Produk"
3. Expected:
   - Validation error from backend (422)
   - Alert showing error
   - File not stored

### Test 6: Large File Upload ✓
1. Try to select image larger than 10 MB
2. Click "Tambah Produk"
3. Expected:
   - Validation error from backend (422)
   - Alert: "Gagal menambah produk" or specific size error
   - File not stored

## Console Logging Points

### Add Product
- "Add product" section in console (should be empty, clean submission)

### Update Product
- "🔐 Token: ✓ Present" (or ✗ Missing)
- "📝 Product ID: {id}"
- "🖼️ New image file: {filename}" (if file selected)
- "📌 No new image selected, keeping existing" (if no file)
- "📊 Response status: 200" (success)
- "✅ Updated product: {data}"

### Error Cases
- "❌ Error response: {error}" (validation failure)
- "❌ Error: {error}" (network/fetch error)

## File Storage Verification

### Check Backend Storage
1. Navigate to: `/BE/storage/app/public/uploads/products/`
2. Expected files: `{timestamp}_{original_filename}.{ext}`
   Example: `1735637890_coffee_bag.jpg`

### Check API Response
1. Open browser DevTools → Network tab
2. Create/Update product
3. Check POST/PUT request
4. Response should show:
   ```json
   {
     "success": true,
     "message": "Product created/updated successfully",
     "data": {
       "id": "...",
       "name": "...",
       "image_urls": ["http://api.url/storage/uploads/products/123456_filename.jpg"]
     }
   }
   ```

## Database Verification

### Check Product Record
1. Open database admin (Supabase / phpMyAdmin)
2. Query products table
3. Find product by name
4. Check `image_urls` column contains: `["http://...storage/uploads/products/123456_filename.jpg"]`

## Common Issues & Solutions

### Issue: File input not showing file name
**Solution:** Check browser console for errors, ensure event listener attached

### Issue: "Gagal menambah produk" but no specific error
**Solution:** 
- Check file type (must be jpg, jpeg, or png)
- Check file size (must be < 10 MB)
- Check server logs: `BE/storage/logs/laravel.log`

### Issue: File appears to upload but doesn't show in next list
**Solution:**
- Check products are reloading: look for API calls in Network tab
- Verify image_urls field in API response
- Check storage directory has file

### Issue: Edit keeps replacing image instead of preserving
**Solution:**
- Check no file selected in edit form
- Browser auto-select can happen in some cases
- Refresh modal before editing

## Performance Notes

### Image File Size Optimization
- Max size: 10 MB (backend limit)
- Recommended: < 1 MB for web (use compression tools)
- Recommended formats: JPG (photos), PNG (graphics with transparency)

### Upload Speed
- Typical 1 MB file: < 1 second on good connection
- Display filename immediately to show selection
- Alert shows after server responds (~1-2 seconds)

## Security Considerations

✅ **Implemented:**
- File type validation (jpg, jpeg, png only)
- File size limit (10 MB max)
- Timestamp prefix prevents filename collisions
- Files stored in public storage (served via Laravel)
- MIME type validation on backend

⚠️ **Recommended Future:**
- Virus scanning for uploaded files
- Image dimension validation
- Watermarking for brand protection
- CDN for faster delivery
- Thumbnail generation for list views

## Testing Checklist

- [ ] Add product with small image (< 1 MB)
- [ ] Add product with medium image (1-5 MB)
- [ ] Edit product and change image
- [ ] Edit product without changing image
- [ ] Add product without selecting image (should fail)
- [ ] Verify files appear in storage directory
- [ ] Verify image_urls in database is correct
- [ ] Verify API response includes image URL
- [ ] Check console logs during add/edit
- [ ] Test on different browsers (Chrome, Firefox, Safari, Edge)
- [ ] Test on mobile devices (file picker works)
