# Setup Supabase Storage untuk NEXEL

## Langkah-langkah Setup Supabase Storage

### 1. Buat Storage Bucket di Supabase

1. Login ke [Supabase Dashboard](https://app.supabase.com)
2. Pilih project Anda: `luvojjczbzbduplwvjtg`
3. Klik menu **Storage** di sidebar
4. Klik **New Bucket**
5. Nama bucket: `nexel-storage`
6. **Public bucket**: ✅ Centang (agar file bisa diakses publik)
7. Klik **Create bucket**

### 2. Setup Bucket Policies (Penting!)

Setelah bucket dibuat, set policy agar file bisa diakses publik:

1. Klik bucket `nexel-storage`
2. Klik tab **Policies**
3. Klik **New Policy**
4. Pilih template: **Allow public read access**
5. Atau buat custom policy dengan SQL:

```sql
CREATE POLICY "Public Access"
ON storage.objects FOR SELECT
USING ( bucket_id = 'nexel-storage' );

CREATE POLICY "Authenticated can upload"
ON storage.objects FOR INSERT
WITH CHECK ( bucket_id = 'nexel-storage' );

CREATE POLICY "Authenticated can update"
ON storage.objects FOR UPDATE
USING ( bucket_id = 'nexel-storage' );

CREATE POLICY "Authenticated can delete"
ON storage.objects FOR DELETE
USING ( bucket_id = 'nexel-storage' );
```

### 3. Dapatkan API Keys

1. Klik menu **Project Settings** (icon gear di sidebar)
2. Klik **API**
3. Copy:
   - **anon** key (public key)
   - **service_role** key (secret key)

### 4. Update File .env

Update file `backend/.env` dengan credentials Supabase Anda:

```env
FILESYSTEM_DISK=supabase

# Supabase Storage Configuration
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... # Paste anon key
SUPABASE_SECRET=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9... # Paste service_role key
SUPABASE_BUCKET=nexel-storage
SUPABASE_REGION=ap-southeast-1
SUPABASE_PROJECT_ID=luvojjczbzbduplwvjtg
SUPABASE_URL=https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage
SUPABASE_ENDPOINT=https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/s3
```

### 5. Install AWS SDK (Diperlukan untuk S3 Driver)

```bash
cd backend
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### 6. Test Upload

Setelah setup selesai, test upload gambar:

```bash
# Restart Laravel server
php artisan config:clear
php artisan cache:clear
php artisan serve
```

Test upload via API:
- Endpoint: `POST http://localhost:8000/api/penjual/produks/upload-gambar`
- Header: `Authorization: Bearer {token}`
- Body: form-data dengan key `gambar` dan file image

### 7. Verifikasi di Supabase Dashboard

1. Buka Supabase Dashboard → Storage → nexel-storage
2. Lihat folder yang dibuat:
   - `produks/` - untuk foto produk
   - `penjuals/foto_profil/` - untuk foto profil penjual
   - `penjuals/ktp/` - untuk foto KTP penjual

### 8. Struktur URL File

File yang diupload akan memiliki URL publik:
```
https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage/produks/1234567890_product.jpg
```

## Fitur yang Sudah Dikonfigurasi

✅ Upload gambar produk otomatis ke Supabase
✅ Upload foto profil penjual ke Supabase  
✅ Upload foto KTP penjual ke Supabase
✅ Generate URL publik otomatis untuk semua file
✅ Helper class `StorageHelper` untuk manajemen file
✅ Resource classes update URL gambar otomatis

## Troubleshooting

### Error: "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"

**Solusi**: Install AWS SDK
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### Error: "Access Denied" atau 403

**Solusi**: Cek bucket policies di Supabase Dashboard, pastikan public read access sudah diaktifkan

### Gambar tidak muncul di frontend

**Solusi**: 
1. Cek URL yang dikembalikan oleh API (harus full URL Supabase)
2. Pastikan bucket sudah public
3. Clear cache Laravel: `php artisan config:clear && php artisan cache:clear`

### Timeout saat upload

**Solusi**: 
1. Cek koneksi internet
2. Cek ukuran file (max 2MB sesuai validasi)
3. Cek SUPABASE_ENDPOINT di .env sudah benar

## Migrasi Data Lama (Opsional)

Jika sudah ada gambar di `storage/app/public`, Anda bisa upload manual ke Supabase atau buat script migrasi.

Contoh script migrasi:

```php
// backend/database/migrations/migrate_images_to_supabase.php
use Illuminate\Support\Facades\Storage;
use App\Models\Produk;

$produks = Produk::whereNotNull('fotoProduk')->get();

foreach ($produks as $produk) {
    $localPath = storage_path('app/public/' . $produk->fotoProduk);
    
    if (file_exists($localPath)) {
        $content = file_get_contents($localPath);
        $newPath = 'produks/' . basename($produk->fotoProduk);
        
        Storage::disk('supabase')->put($newPath, $content, 'public');
        
        $produk->update(['fotoProduk' => $newPath]);
        echo "Migrated: {$produk->namaProduk}\n";
    }
}
```

## Keuntungan Supabase Storage

✅ **Cloud Storage** - File tersimpan di cloud, bukan local server
✅ **CDN** - File di-serve melalui CDN untuk akses lebih cepat
✅ **Skalabilitas** - Tidak ada batasan storage (tergantung plan)
✅ **Backup** - Supabase handle backup otomatis
✅ **Security** - Built-in security dan access control
✅ **Free Tier** - 1GB storage gratis

## Monitoring

Cek usage storage di Supabase Dashboard:
- Menu **Storage** → Click bucket → Tab **Usage**
- Lihat total files dan storage size

## Support

Dokumentasi Supabase Storage: https://supabase.com/docs/guides/storage
