# 🚀 Setup Cloud Storage untuk NEXEL - RINGKASAN

## ⚠️ PENTING: Install Package Terlebih Dahulu

Sebelum menggunakan Supabase Storage, install package AWS S3:

```bash
cd backend
composer require league/flysystem-aws-s3-v3 "^3.0"
composer dump-autoload
```

## 📝 Konfigurasi yang Sudah Dibuat

### 1. **File yang Sudah Diupdate:**

✅ `config/filesystems.php` - Tambah disk 'supabase'
✅ `.env` - Tambah konfigurasi Supabase Storage
✅ `app/Helpers/StorageHelper.php` - Helper untuk manage file
✅ `app/Http/Resources/ProdukResource.php` - Auto generate URL publik
✅ `app/Http/Resources/PenjualResource.php` - Auto generate URL publik
✅ `app/Http/Controllers/PenjualController.php` - Upload ke Supabase
✅ `routes/api.php` - Upload produk ke Supabase
✅ `composer.json` - Autoload helper

### 2. **Yang Perlu Anda Lakukan:**

#### A. Setup Supabase Storage Bucket

1. Login ke **Supabase Dashboard**: https://app.supabase.com
2. Pilih project: `luvojjczbzbduplwvjtg`
3. Klik menu **Storage** → **New Bucket**
4. Nama: `nexel-storage`
5. **Centang "Public bucket"** ✅
6. Klik **Create**

#### B. Set Bucket Policy (PENTING!)

Setelah bucket dibuat:

1. Klik bucket `nexel-storage`
2. Tab **Policies** → **New Policy**
3. Pilih template: "Allow public read access"
4. **Save**

Atau gunakan SQL custom policy:

```sql
-- Allow public to read files
CREATE POLICY "Public Read Access"
ON storage.objects FOR SELECT
USING ( bucket_id = 'nexel-storage' );

-- Allow authenticated users to upload
CREATE POLICY "Authenticated Upload"
ON storage.objects FOR INSERT
WITH CHECK ( bucket_id = 'nexel-storage' );
```

#### C. Dapatkan API Keys

1. **Project Settings** (gear icon) → **API**
2. Copy kedua keys:
   - **anon** (public key)
   - **service_role** (secret key)

#### D. Update File `.env`

Replace placeholders dengan key Anda:

```env
FILESYSTEM_DISK=supabase

# Supabase Storage
SUPABASE_KEY=eyJhbGc...  # Paste anon key di sini
SUPABASE_SECRET=eyJhbGc...  # Paste service_role key di sini
SUPABASE_BUCKET=nexel-storage
SUPABASE_REGION=ap-southeast-1
SUPABASE_PROJECT_ID=luvojjczbzbduplwvjtg
SUPABASE_URL=https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage
SUPABASE_ENDPOINT=https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/s3
```

#### E. Clear Cache & Restart Server

```bash
cd backend
php artisan config:clear
php artisan cache:clear
php artisan serve
```

## ✅ Test Upload

### Via Postman/Thunder Client:

**Endpoint:** `POST http://localhost:8000/api/penjual/produks/upload-gambar`

**Headers:**
```
Authorization: Bearer {your-token}
Content-Type: multipart/form-data
```

**Body (form-data):**
```
gambar: [select image file]
```

**Expected Response:**
```json
{
  "message": "Gambar berhasil diupload",
  "path": "produks/1733289472_product.jpg",
  "url": "https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage/produks/1733289472_product.jpg"
}
```

## 📂 Struktur Folder di Supabase

```
nexel-storage/
├── produks/               ← Foto produk
│   └── 1733289472_product.jpg
├── penjuals/
│   ├── foto_profil/      ← Foto profil penjual
│   │   └── 1733289473_foto_penjual.jpg
│   └── ktp/              ← Foto KTP penjual
│       └── 1733289474_ktp_penjual.jpg
```

## 🔧 Troubleshooting

### Error: "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"

**Solusi:**
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
composer dump-autoload
```

### Error: 403 Access Denied

**Solusi:** Cek bucket policies di Supabase, pastikan public read sudah aktif

### Gambar tidak muncul di Frontend

1. Cek URL response API (harus full URL Supabase)
2. Buka URL langsung di browser untuk test
3. Pastikan bucket **public**
4. Clear cache: `php artisan config:clear`

### Timeout saat upload

1. Cek koneksi internet
2. Cek file size (max 2MB)
3. Verify SUPABASE_ENDPOINT benar

## 🎯 Keuntungan Cloud Storage

✅ File tersimpan di cloud (bukan local)
✅ CDN untuk akses cepat global
✅ Scalable tanpa batas (sesuai plan)
✅ Auto backup oleh Supabase
✅ Built-in security
✅ **1GB free tier** dari Supabase

## 📊 Monitor Usage

Di Supabase Dashboard:
**Storage** → `nexel-storage` → **Usage**

## 🔄 Migrasi Gambar Lama (Opsional)

Jika sudah ada gambar di `storage/app/public/`, buat script untuk upload ke Supabase:

```php
use App\Helpers\StorageHelper;
use App\Models\Produk;

Produk::whereNotNull('fotoProduk')->chunk(50, function($produks) {
    foreach($produks as $produk) {
        $localPath = storage_path('app/public/' . $produk->fotoProduk);
        
        if (file_exists($localPath)) {
            $file = new \Illuminate\Http\UploadedFile($localPath, basename($localPath));
            $newPath = StorageHelper::uploadFile($file, 'produks');
            $produk->update(['fotoProduk' => $newPath]);
        }
    }
});
```

## 📚 Referensi

- Supabase Storage Docs: https://supabase.com/docs/guides/storage
- Laravel Filesystem: https://laravel.com/docs/filesystem
- AWS S3 Driver: https://github.com/thephpleague/flysystem-aws-s3-v3

---

**🎉 Selesai! Sekarang semua upload gambar akan otomatis tersimpan di Supabase Storage cloud.**
