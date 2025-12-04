# ✅ Supabase Storage - Perbaikan Preview Gambar

## 🎯 Masalah yang Diperbaiki

1. **URL Generation Error** - Storage::disk('supabase')->url() tidak generate URL dengan benar
2. **Line Break di URL** - URL terpotong karena format yang salah
3. **Localhost Path** - Gambar lama masih menggunakan path localhost
4. **Resource tidak digunakan** - API show() tidak menggunakan ProdukResource

## 🔧 Perubahan yang Dilakukan

### 1. **StorageHelper.php** - Improved URL Generation
```php
public static function getPublicUrl(?string $path): ?string
{
    // Automatic conversion dari localhost URL ke Supabase URL
    // Format: https://{project-id}.supabase.co/storage/v1/object/public/{bucket}/{path}
}
```

**Fitur baru:**
- ✅ Otomatis convert localhost URL ke Supabase URL
- ✅ Support path relatif dan absolute URL
- ✅ Handle edge cases (leading slash, null path, etc)

### 2. **filesystems.php** - Added Project ID
```php
'supabase' => [
    'project_id' => env('SUPABASE_PROJECT_ID'), // BARU!
    // ... other configs
]
```

### 3. **routes/api.php** - Updated Upload Route
```php
Route::post('/upload-gambar', function (Request $request) {
    // ... upload logic
    $url = \App\Helpers\StorageHelper::getPublicUrl($path); // Menggunakan helper
});
```

### 4. **ProdukController.php** - Use Resource in show()
```php
public function show(Produk $produk)
{
    $produk->load(['penjual', 'kategori', 'reviews']);
    $produk->loadAvg('reviews', 'rating');
    
    return response()->json(new ProdukResource($produk)); // Menggunakan Resource!
}
```

## 📋 Format URL yang Benar

### ❌ URL Lama (Localhost)
```
http://localhost:8000/storage/produk/filename.jpg
```

### ✅ URL Baru (Supabase)
```
https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage/produks/filename.jpg
```

## 🚀 Cara Upload Gambar Baru (untuk Testing)

### Via Postman / Thunder Client:

**Endpoint:**
```
POST http://127.0.0.1:8000/api/penjual/produks/upload-gambar
```

**Headers:**
```
Authorization: Bearer {your-penjual-token}
Content-Type: multipart/form-data
```

**Body (form-data):**
```
gambar: [pilih file gambar: jpg, png, jpeg max 2MB]
```

**Expected Response:**
```json
{
    "message": "Gambar berhasil diupload",
    "path": "produks/1733318400_abc123.jpg",
    "url": "https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage/produks/1733318400_abc123.jpg"
}
```

### Lalu Update Produk dengan URL tersebut:

**Endpoint:**
```
PUT http://127.0.0.1:8000/api/penjual/produks/{id}
```

**Body (JSON):**
```json
{
    "fotoProduk": "produks/1733318400_abc123.jpg"
}
```

## 🔍 Verifikasi

### 1. Check API Response
```bash
curl http://127.0.0.1:8000/api/public/produks/2
```

### 2. Check URL di Browser
Buka URL yang dikembalikan di browser untuk memastikan gambar tampil.

### 3. Check di Supabase Dashboard
1. Login ke https://supabase.com
2. Pilih project `luvojjczbzbduplwvjtg`
3. Ke menu **Storage** > **nexel-storage**
4. Folder **produks/** harus berisi file gambar yang di-upload

## ⚠️ Important Notes

### Untuk Gambar yang Sudah Ada (Lama)
Gambar yang sudah di-upload **sebelum** konfigurasi Supabase **tidak akan otomatis tersedia** di Supabase Storage. Ada 2 pilihan:

**Opsi 1: Re-upload Manual** (Recommended untuk jumlah sedikit)
- Upload ulang gambar melalui form di frontend/Postman
- Update fotoProduk dengan path yang baru

**Opsi 2: Migration Script** (Untuk jumlah banyak)
- Buat script untuk copy files dari local storage ke Supabase
- Update database dengan path baru

### Gambar Baru
Semua gambar yang di-upload **setelah** perbaikan ini akan:
- ✅ Otomatis tersimpan di Supabase Storage
- ✅ Generate URL yang benar
- ✅ Preview langsung bisa dilihat

## 🎨 Testing di Frontend

Pastikan di Vue component, gambar di-load dari response API:

```vue
<img :src="product.fotoProduk" :alt="product.namaProduk" />
```

URL akan otomatis dalam format Supabase yang benar.

## 📝 Checklist

- [x] Config Supabase di .env
- [x] Update filesystems.php
- [x] Fix StorageHelper URL generation
- [x] Fix upload route
- [x] Fix ProdukController show() method
- [x] Clear config cache
- [ ] **Upload gambar baru untuk testing** ⭐ (Action yang perlu dilakukan)
- [ ] **Verify gambar tampil di browser** ⭐
- [ ] **Check di Supabase Storage Dashboard** ⭐

## 🆘 Troubleshooting

### Gambar tidak muncul setelah upload baru
1. Check Supabase Storage Dashboard - apakah file ada?
2. Check bucket policy - apakah public?
3. Check URL format di response API
4. Test URL langsung di browser

### Error 403 Forbidden
- Bucket policy belum public
- Pergi ke Supabase Dashboard > Storage > nexel-storage > Policies
- Pastikan ada policy "Public Access" untuk SELECT

### Error 401 Unauthorized saat upload
- Token penjual tidak valid atau expired
- Re-login untuk get token baru

---

**Status: ✅ FIXED - Ready for Testing!**
