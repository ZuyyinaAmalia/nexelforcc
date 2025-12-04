# ⚡ Quick Setup Guide (10 Minutes)

Setup cepat untuk teman yang ingin pull dan run project dengan Supabase Storage.

---

## 🎯 Goal

- Pull latest changes
- Setup Supabase Storage
- Run project dengan gambar yang tampil (placeholder/real)

---

## 1️⃣ Pull & Install Dependencies (3 min)

```bash
# Pull latest changes
git pull origin main

# Backend - Install dependencies
cd backend
composer install
composer dump-autoload

# Frontend - Install dependencies
cd ../frontend/vuejs/frontend
npm install
```

---

## 2️⃣ Supabase Setup (3 min)

### Create Project & Bucket

1. **Buka:** [supabase.com/dashboard](https://supabase.com/dashboard)
2. **Login/Sign Up**
3. **New Project:**
   - Name: `nexel-storage` (atau bebas)
   - Password: Buat password kuat
   - Region: **Southeast Asia (Singapore)**
   - Click **Create new project** (tunggu 1-2 menit)

4. **Create Bucket:**
   - Sidebar → **Storage**
   - Click **New Bucket**
   - Name: `nexel-storage`
   - **Public bucket:** Toggle **ON** ✅ (PENTING!)
   - Click **Create bucket**

5. **Get Credentials:**
   - Sidebar → **Settings** ⚙️ → **API**
   - Copy:
     - Project URL: `https://xxxxx.supabase.co`
     - Project ID: `xxxxx` (dari URL)
     - anon public key: `eyJhbGc...`
     - service_role key: `eyJhbGc...` (click "Reveal" dulu)

---

## 3️⃣ Configure Environment Files (2 min)

### Backend `.env`

Edit `backend/.env`, tambahkan/update:

```env
# Supabase Storage
SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co/storage/v1/object/public/nexel-storage
SUPABASE_ENDPOINT=https://YOUR_PROJECT_ID.supabase.co/storage/v1/s3
SUPABASE_KEY=YOUR_ANON_PUBLIC_KEY
SUPABASE_SECRET=YOUR_SERVICE_ROLE_KEY
SUPABASE_BUCKET=nexel-storage
SUPABASE_PROJECT_ID=YOUR_PROJECT_ID
SUPABASE_REGION=ap-southeast-1

# Filesystem
FILESYSTEM_DISK=supabase

# Database (jika pakai Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.YOUR_PROJECT_ID
DB_PASSWORD=YOUR_DB_PASSWORD
DB_SSLMODE=require
```

**Replace:**
- `YOUR_PROJECT_ID` → Project ID dari Supabase
- `YOUR_ANON_PUBLIC_KEY` → anon key
- `YOUR_SERVICE_ROLE_KEY` → service_role key
- `YOUR_DB_PASSWORD` → Database password

### Frontend `.env`

Edit `frontend/vuejs/frontend/.env`:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
VITE_SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co
VITE_SUPABASE_KEY=YOUR_ANON_PUBLIC_KEY
```

---

## 4️⃣ Clear Cache & Migrate (1 min)

```bash
cd backend

# Clear cache
php artisan config:clear
php artisan cache:clear

# Run migrations (jika ada yang baru)
php artisan migrate
```

---

## 5️⃣ Run Servers (1 min)

### Terminal 1 - Backend

```bash
cd backend
php artisan serve
```

Output: `Server started on http://127.0.0.1:8000`

### Terminal 2 - Frontend

```bash
cd frontend/vuejs/frontend
npm run dev
```

Output: `Local: http://localhost:5173`

---

## ✅ 6️⃣ Test & Verify (1 min)

### 1. Open Homepage

```
http://localhost:5173/home
```

**Expected:**
- ✅ Page loads
- ✅ Products displayed
- ✅ All images show (placeholder from Unsplash)
- ❌ No broken images

### 2. Test Upload (Optional)

1. Login as penjual
2. Go to: `http://localhost:5173/dashboard-penjual/kelolaproduk`
3. Edit a product
4. Upload new image
5. **Expected:** Image uploads to Supabase and displays immediately

### 3. Verify in Supabase Dashboard

1. Supabase Dashboard → **Storage** → `nexel-storage`
2. **Expected:** See `produks/` folder with uploaded files

---

## 🐛 Quick Troubleshooting

### Images Don't Load?

```bash
cd backend
php artisan config:clear
php artisan cache:clear
# Restart server
```

### Upload Failed?

**Check:**
1. Bucket `nexel-storage` is **public** ✅
2. Service role key in `.env` is correct
3. No extra spaces in `.env` values

**Test:**
```bash
cd backend
php artisan tinker
```

```php
echo env('SUPABASE_PROJECT_ID');
echo env('SUPABASE_KEY');
// Should output your credentials
```

### Composer/NPM Errors?

```bash
# Backend
cd backend
rm -rf vendor composer.lock
composer install

# Frontend
cd frontend/vuejs/frontend
rm -rf node_modules package-lock.json
npm install
```

---

## 📋 Quick Checklist

```
☐ git pull origin main
☐ composer install & npm install
☐ Create Supabase project
☐ Create nexel-storage bucket (PUBLIC!)
☐ Copy credentials to .env files
☐ php artisan config:clear
☐ php artisan migrate
☐ php artisan serve (Terminal 1)
☐ npm run dev (Terminal 2)
☐ Open http://localhost:5173/home
☐ Verify images show (placeholder OK)
```

---

## 💡 Important Notes

### Why Placeholder Images?

Old products show **placeholder images** because:
- Original files were stored locally (not in Git)
- Files don't exist in Supabase yet
- **Placeholder = Better UX** than broken images ❌

**Solution:** Upload new images → They'll be stored in Supabase and display properly! ✅

### New vs Old Images

| Type | Status | Location |
|------|--------|----------|
| **Old images** | 🖼️ Placeholder | Not in Supabase |
| **New uploads** | ✅ Real image | Supabase Storage |

---

## 📚 Full Documentation

Need more details? Read:

- **`SETUP_AFTER_PULL.md`** - Comprehensive setup guide with troubleshooting
- **`SUPABASE_STORAGE_FIX.md`** - Technical details of the fix
- **`FINAL_IMAGE_FIX.md`** - Image placeholder system explanation

---

## 🆘 Still Stuck?

1. Read **SETUP_AFTER_PULL.md** (full guide)
2. Check Supabase Dashboard → Logs
3. Check Laravel logs: `backend/storage/logs/laravel.log`
4. Verify all `.env` values (no typos!)

---

**Setup Time:** ~10 minutes  
**Difficulty:** ⭐⭐ Easy-Medium

**Happy Coding! 🚀**

---

*Last Updated: December 4, 2025*
