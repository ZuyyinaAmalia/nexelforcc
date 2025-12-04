# 🚀 Setup Guide Setelah Pull Remote Changes

Panduan ini untuk teman yang ingin pull perubahan terbaru dan menjalankan project dengan **Supabase Storage Integration**.

---

## 📋 Prerequisites

Pastikan sudah punya:
- ✅ Git installed
- ✅ Composer installed
- ✅ Node.js & npm installed
- ✅ PHP 8.1+ & PostgreSQL
- ✅ Akun Supabase (akan disetup di step 3)

---

## 🔄 Step 1: Pull Remote Changes

```bash
# 1. Stash perubahan lokal jika ada
git stash

# 2. Pull remote changes
git pull origin main

# 3. Apply stashed changes kembali (jika ada)
git stash pop
```

---

## 📦 Step 2: Update Dependencies

### Backend (Laravel)

```bash
cd backend

# Install/update Composer dependencies
composer install

# Update composer autoload
composer dump-autoload
```

**⚠️ Dependencies Baru:**
- `league/flysystem-aws-s3-v3` - Untuk S3-compatible storage (Supabase)

### Frontend (Vue.js)

```bash
cd frontend/vuejs/frontend

# Install/update npm dependencies
npm install
```

**⚠️ Dependencies Baru:**
- `chart.js` - Untuk charts/grafik di dashboard

---

## 🔧 Step 3: Setup Supabase Storage

### 3.1 Create Supabase Project

1. Buka [https://supabase.com/dashboard](https://supabase.com/dashboard)
2. Sign up atau login
3. Klik **New Project**
4. Isi form:
   - **Name:** Nexel Storage (atau bebas)
   - **Database Password:** Buat password yang kuat
   - **Region:** Southeast Asia (Singapore) - paling dekat
5. Klik **Create new project**
6. Tunggu 1-2 menit sampai project selesai provisioning

### 3.2 Create Storage Bucket

1. Di dashboard Supabase, klik **Storage** di sidebar kiri
2. Klik **New Bucket**
3. Isi form:
   - **Name:** `nexel-storage`
   - **Public bucket:** Toggle **ON** ✅ (PENTING!)
   - **Allowed MIME types:** Kosongkan (allow all)
   - **File size limit:** 2MB (atau sesuai kebutuhan)
4. Klik **Create bucket**

### 3.3 Set Bucket Policies (Public Access)

1. Masih di **Storage** → Klik bucket `nexel-storage`
2. Klik tab **Policies**
3. Jika belum ada policy, klik **New Policy**
4. Pilih template **Allow public access for all users**
5. Atau buat custom policy:
   ```sql
   CREATE POLICY "Public Access"
   ON storage.objects FOR SELECT
   TO public
   USING (bucket_id = 'nexel-storage');
   ```

### 3.4 Get Supabase Credentials

Di dashboard project Supabase:

1. Klik ikon **Settings** (⚙️) di sidebar
2. Klik **API** 
3. Lihat section **Project API keys**
4. Copy credentials berikut:

```
Project URL: https://xxxxxxxxxxxxx.supabase.co
Project ID: xxxxxxxxxxxxx (ambil dari URL)
anon public: eyJhbGc... (key yang panjang)
service_role: eyJhbGc... (klik "Reveal" dulu, lalu copy)
```

⚠️ **JANGAN SHARE service_role key ke public!**

### 3.5 Update Backend `.env`

Edit file `backend/.env` dan update/tambahkan konfigurasi Supabase:

```env
# Database Configuration (Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.xxxxxxxxxxxxx
DB_PASSWORD=your-database-password
DB_SSLMODE=require

# Supabase Storage Configuration
SUPABASE_URL=https://xxxxxxxxxxxxx.supabase.co/storage/v1/object/public/nexel-storage
SUPABASE_ENDPOINT=https://xxxxxxxxxxxxx.supabase.co/storage/v1/s3
SUPABASE_KEY=your-anon-public-key
SUPABASE_SECRET=your-service-role-key
SUPABASE_BUCKET=nexel-storage
SUPABASE_REGION=ap-southeast-1
SUPABASE_PROJECT_ID=xxxxxxxxxxxxx

# Filesystem Configuration
FILESYSTEM_DISK=supabase
```

**⚠️ REPLACE:**
- `xxxxxxxxxxxxx` → Project ID Supabase Anda
- `your-anon-public-key` → anon public key dari dashboard
- `your-service-role-key` → service_role key dari dashboard
- `your-database-password` → password database Supabase

**💡 Cara mudah dapat DB credentials:**
1. Di Supabase dashboard → Settings → Database
2. Copy **Connection string** → pilih format **URI** atau **Session pooler**
3. Parse ke format .env di atas

### 3.6 Update Frontend `.env`

Edit file `frontend/vuejs/frontend/.env`:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
VITE_SUPABASE_URL=https://xxxxxxxxxxxxx.supabase.co
VITE_SUPABASE_KEY=your-anon-public-key
```

---

## 🗄️ Step 4: Database Setup

```bash
cd backend

# Clear config cache
php artisan config:clear

# Run migrations (jika ada yang baru)
php artisan migrate

# Optional: Seed database dengan data dummy
php artisan db:seed
```

**Note:** Database sudah terkoneksi ke Supabase PostgreSQL, bukan MySQL lokal!

---

## 🧪 Step 5: Test Configuration

### Test Supabase Bucket Access

```bash
cd backend

# Test apakah bucket accessible
php artisan tinker
```

Di tinker, jalankan:

```php
use Illuminate\Support\Facades\Http;

// Test bucket info
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . env('SUPABASE_SECRET'),
    'apikey' => env('SUPABASE_KEY'),
])->get('https://' . env('SUPABASE_PROJECT_ID') . '.supabase.co/storage/v1/bucket/nexel-storage');

echo "Status: " . $response->status() . "\n";
echo $response->body();

// Expected: Status 200 dan info bucket
```

### Test StorageHelper

```php
// Di tinker
use App\Helpers\StorageHelper;

// Test placeholder
echo StorageHelper::getPublicUrl(null);
// Expected: URL Unsplash placeholder

// Test Supabase URL generation
echo StorageHelper::getPublicUrl('produks/test.jpg');
// Expected: https://xxxxx.supabase.co/storage/v1/object/public/nexel-storage/produks/test.jpg
```

---

## ▶️ Step 6: Run Application

### Terminal 1 - Backend (Laravel)

```bash
cd backend
php artisan serve
# Running on http://127.0.0.1:8000
```

### Terminal 2 - Frontend (Vue + Vite)

```bash
cd frontend/vuejs/frontend
npm run dev
# Running on http://localhost:5173
```

### Terminal 3 - Queue Worker (Optional)

```bash
cd backend
php artisan queue:work
```

---

## ✅ Step 7: Verify Everything Works

### 1. Check Home Page
- Buka browser: `http://localhost:5173/home`
- **Expected:** 
  - Halaman home tampil
  - Semua gambar produk tampil dengan **placeholder** (gambar dari Unsplash)
  - Tidak ada broken image ❌

### 2. Check Dashboard Penjual
- Login sebagai penjual (register dulu jika belum punya akun)
- Buka: `http://localhost:5173/dashboard-penjual/kelolaproduk`
- **Expected:** 
  - Dashboard tampil dengan chart
  - List produk tampil
  - Gambar produk tampil dengan **placeholder**

### 3. Test Upload Gambar Baru
1. Di dashboard penjual, klik **Edit** pada salah satu produk
2. Upload gambar baru (format JPG/PNG, max 2MB)
3. Klik **Simpan**
4. **Expected:** 
   - Upload berhasil
   - Gambar langsung tampil (bukan placeholder)
   - Gambar ter-upload ke Supabase Storage

### 4. Verify di Supabase Dashboard
1. Buka Supabase Dashboard → **Storage** → `nexel-storage`
2. **Expected:** 
   - Melihat folder `produks/`
   - Ada file gambar yang baru di-upload (format: `timestamp_uniqueid.jpg`)
3. Klik file → Copy URL → Paste di browser
4. **Expected:** Gambar tampil dengan URL format:
   ```
   https://xxxxx.supabase.co/storage/v1/object/public/nexel-storage/produks/1733320000_abc123.jpg
   ```

---

## 🐛 Troubleshooting

### Problem 1: Gambar Tidak Tampil (Semua Placeholder)

**Symptoms:** Semua produk menampilkan placeholder, tidak ada gambar asli

**Explanation:** Ini **NORMAL**! File gambar lama tidak ada di Supabase karena:
- Gambar lama di-upload sebelum integrasi Supabase
- File hanya ada di local storage (tidak ter-commit ke Git)
- Placeholder otomatis ditampilkan untuk UX yang lebih baik

**Solution:** 
1. Upload gambar baru untuk produk
2. Gambar baru akan tersimpan di Supabase dan tampil dengan benar

### Problem 2: Error "FILESYSTEM_DISK not found"

**Solution:**
```bash
cd backend

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart server
php artisan serve
```

### Problem 3: Upload Gambar Gagal

**Check:**
1. Bucket `nexel-storage` di Supabase adalah **public** ✅
2. Service role key di `.env` benar (tidak ada spasi extra)
3. Project ID di `.env` benar

**Test manually dengan curl:**
```bash
# Test upload ke Supabase
curl -X POST \
  'https://YOUR_PROJECT_ID.supabase.co/storage/v1/object/nexel-storage/produks/test.jpg' \
  -H 'Authorization: Bearer YOUR_SERVICE_ROLE_KEY' \
  -H 'apikey: YOUR_ANON_KEY' \
  -H 'Content-Type: image/jpeg' \
  --data-binary '@test-image.jpg'
```

**Expected Response:**
```json
{
  "Key": "produks/test.jpg"
}
```

### Problem 4: Composer Dependencies Error

```bash
cd backend

# Remove vendor dan reinstall
rm -rf vendor
rm composer.lock
composer install
composer dump-autoload
```

### Problem 5: NPM Dependencies Error

```bash
cd frontend/vuejs/frontend

# Remove node_modules dan reinstall
rm -rf node_modules
rm package-lock.json
npm install
```

### Problem 6: Storage Helper Not Found

**Symptoms:** Error `Class 'App\Helpers\StorageHelper' not found`

**Solution:**
```bash
cd backend
composer dump-autoload
php artisan config:clear
```

### Problem 7: Database Connection Failed

**Check:**
1. Database credentials di `.env` benar
2. Supabase project aktif (tidak paused)
3. IP Anda tidak di-block (Supabase kadang limit connection)

**Solution:**
```bash
# Test database connection
cd backend
php artisan tinker

\DB::connection()->getPdo();
// Should return PDO object, not error
```

---

## 📚 Important Files Changed

Perhatikan file-file yang berubah significant:

### Backend:

1. **`backend/config/filesystems.php`**
   - Tambah konfigurasi disk `supabase`
   - Support S3-compatible storage

2. **`backend/app/Helpers/StorageHelper.php`** (NEW!)
   - Helper untuk generate URL gambar
   - Logic placeholder untuk gambar yang tidak ada
   - Smart detection untuk old vs new uploads

3. **`backend/app/Http/Controllers/ProdukController.php`**
   - Method `show()` sekarang menggunakan `ProdukResource`
   - Consistent API response

4. **`backend/app/Http/Controllers/PenjualController.php`**
   - Upload foto profil & KTP ke Supabase
   - Tidak lagi ke local storage

5. **`backend/routes/api.php`**
   - Route `/upload-gambar` menggunakan HTTP API Supabase
   - Tambah `use Illuminate\Support\Facades\Http;`

6. **`backend/composer.json`**
   - Dependency baru: `league/flysystem-aws-s3-v3`
   - Autoload helper: `app/Helpers/StorageHelper.php`

### Frontend:

1. **`frontend/vuejs/frontend/src/views/ProductDetail.vue`**
   - Fixed: Gunakan store methods bukan axios manual
   - `await produkStore.fetchPublicProdukById()`
   - `await reviewStore.createReview()`

2. **`frontend/vuejs/frontend/src/views/HomeView.vue`**
   - Fixed: Remove duplicate code
   - Gunakan store methods konsisten
   - Fixed template syntax errors

3. **`frontend/vuejs/frontend/package.json`**
   - Dependency baru: `chart.js` untuk dashboard charts

---

## 📖 Additional Documentation

Untuk referensi lebih lanjut, baca dokumentasi berikut:

- **`SUPABASE_STORAGE_FIX.md`** - Detail technical fix Supabase Storage
- **`FINAL_IMAGE_FIX.md`** - Penjelasan placeholder system & troubleshooting
- **`backend/TEST_UPLOAD.md`** - Panduan testing upload via Postman

---

## 🎯 Quick Checklist

Gunakan checklist ini untuk memastikan setup berhasil:

```
☐ Pull remote changes (git pull origin main)
☐ Backend: composer install
☐ Frontend: npm install
☐ Create Supabase project
☐ Create bucket nexel-storage (public!)
☐ Set bucket policies for public access
☐ Copy Supabase credentials (Project ID, keys)
☐ Update backend/.env dengan credentials
☐ Update frontend/.env dengan credentials
☐ Run: php artisan config:clear
☐ Run: php artisan migrate
☐ Test Supabase connection di tinker
☐ Start backend: php artisan serve
☐ Start frontend: npm run dev
☐ Open http://localhost:5173/home
☐ Verify gambar tampil (placeholder OK)
☐ Login penjual
☐ Test upload gambar baru
☐ Verify gambar baru tampil (bukan placeholder)
☐ Check Supabase dashboard → Storage → Ada file baru
```

---

## 💡 Important Notes

### 1. Gambar Lama vs Gambar Baru

**Gambar Lama (Placeholder):**
- Produk yang sudah ada sebelum integrasi Supabase
- File gambar tidak ada di Supabase Storage
- System otomatis menampilkan **placeholder image** dari Unsplash
- Tidak ada broken image, UX tetap bagus ✅

**Gambar Baru (Real Image):**
- Upload setelah setup Supabase ini
- File tersimpan di Supabase Storage
- Langsung tampil dengan URL Supabase
- Accessible dari mana saja (cloud storage) ☁️

### 2. Storage Location

**Before (Local Storage):**
```
backend/storage/app/public/produks/
backend/storage/app/public/penjuals/
```
❌ File tidak ter-commit ke Git
❌ Tidak accessible untuk deployment

**After (Supabase Storage):**
```
https://xxxxx.supabase.co/storage/v1/object/public/nexel-storage/produks/
https://xxxxx.supabase.co/storage/v1/object/public/nexel-storage/penjuals/
```
✅ Cloud-based, accessible globally
✅ Public URL untuk semua file
✅ Scalable dan production-ready

### 3. Environment Variables

**Development:**
- Gunakan Supabase project untuk development
- Database: Supabase PostgreSQL (pooler)
- Storage: Supabase Storage

**Production (Nanti):**
- Buat Supabase project terpisah untuk production
- Copy credentials production ke `.env.production`
- Deploy ke server dengan .env production

### 4. Local Storage Files

Folder `backend/storage/app/public/` masih ada tapi:
- ❌ Tidak perlu di-commit ke Git
- ❌ File tidak akan di-load oleh aplikasi
- ✅ Sudah ada di `.gitignore`
- ✅ Setiap developer punya file lokal sendiri

---

## 🔐 Security Best Practices

1. **JANGAN commit `.env` ke Git** ❌
2. **JANGAN share service_role key** ke publik ❌
3. **Gunakan bucket policies** yang tepat (public hanya untuk read)
4. **Limit file size** di Supabase (default 2MB)
5. **Monitor storage usage** di Supabase dashboard

---

## 📊 Expected Project Structure

Setelah setup, struktur project:

```
PPL_NEXEL/
├── backend/
│   ├── app/
│   │   └── Helpers/
│   │       └── StorageHelper.php          (NEW!)
│   ├── config/
│   │   └── filesystems.php                (Modified)
│   ├── routes/
│   │   └── api.php                        (Modified)
│   ├── .env                               (Update dengan Supabase credentials)
│   └── composer.json                      (New dependency)
│
├── frontend/vuejs/frontend/
│   ├── src/
│   │   ├── views/
│   │   │   ├── HomeView.vue              (Fixed)
│   │   │   └── ProductDetail.vue         (Fixed)
│   │   └── stores/
│   │       └── index.d.ts                (Type definitions)
│   ├── .env                               (Update dengan Supabase URL)
│   └── package.json                       (New dependency: chart.js)
│
└── Documentation/
    ├── SETUP_AFTER_PULL.md                (This file)
    ├── QUICK_SETUP.md
    ├── SUPABASE_STORAGE_FIX.md
    └── FINAL_IMAGE_FIX.md
```

---

## 🆘 Still Need Help?

Jika masih ada masalah setelah mengikuti guide ini:

1. **Check Troubleshooting Section** di atas
2. **Verify semua credentials** di `.env` benar (no typos!)
3. **Check Supabase Dashboard:**
   - Project aktif? (tidak paused)
   - Bucket `nexel-storage` exist?
   - Bucket adalah public?
4. **Clear all caches:**
   ```bash
   cd backend
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   composer dump-autoload
   ```
5. **Check logs:**
   - Backend: `backend/storage/logs/laravel.log`
   - Frontend: Browser console (F12)
   - Supabase: Dashboard → Logs

---

## 🎉 Success Criteria

Setup berhasil jika:

✅ Homepage tampil dengan semua gambar (placeholder)
✅ Dashboard penjual tampil dengan charts
✅ Upload gambar baru berhasil
✅ Gambar baru tampil di frontend
✅ File gambar ada di Supabase Storage dashboard
✅ URL gambar format Supabase (bukan localhost)

---

**Setup Time:** ~10-15 menit
**Difficulty:** ⭐⭐⭐ Medium

**Happy Coding! 🚀**

---

*Last Updated: December 4, 2025*
*Version: 1.0*
