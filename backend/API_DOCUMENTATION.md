# 📚 NEXEL API Documentation

## 🔗 Base URL
```
http://localhost:8000/api
```

---

## 📋 Table of Contents
1. [Public Routes](#1-public-routes)
2. [Admin Routes](#2-admin-routes)
3. [Penjual Routes](#3-penjual-routes)

---

## 1. PUBLIC ROUTES
> **Tidak memerlukan authentication**

### 🛍️ PRODUK

#### Get All Products
```http
GET /api/public/produks
```
**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "namaProduk": "Laptop ASUS ROG",
      "deskripsi": "Gaming laptop",
      "harga": "15000000.00",
      "stok": 10,
      "fotoProduk": "path/to/image.jpg",
      "kategori_id": 1
    }
  ]
}
```

#### Get Single Product
```http
GET /api/public/produks/{id}
```

---

### 🏷️ KATEGORI

#### Get All Categories
```http
GET /api/public/kategoris
```

#### Get Single Category
```http
GET /api/public/kategoris/{id}
```

---

### ⭐ REVIEW

#### Get All Reviews (Optional filter)
```http
GET /api/public/reviews?product_id={id}
```

#### Get Reviews by Product ID
```http
GET /api/public/reviews/product/{produkId}
```

#### Create Review
```http
POST /api/public/reviews
```
**Body:**
```json
{
  "produk_id": 1,
  "rating": 5,
  "ulasan": "Produk sangat bagus!",
  "namaPengunjung": "John Doe",
  "emailPengunjung": "john@email.com",
  "noHpPengunjung": "08123456789",
  "provinsiPengunjung": "DKI Jakarta"
}
```

#### Get Single Review
```http
GET /api/public/reviews/{id}
```

---

## 2. ADMIN ROUTES
> **Memerlukan authentication dengan role Admin**

### 🔐 AUTH

#### Admin Login
```http
POST /api/admin/login
```
**Body:**
```json
{
  "email": "admin@nexel.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "message": "Login Berhasil",
  "token": "1|xxxxxxxxxxxxx",
  "admin": {
    "id": 1,
    "nama": "Admin",
    "email": "admin@nexel.com"
  }
}
```

#### Admin Logout
```http
POST /api/admin/logout
Authorization: Bearer {token}
```

#### Get Admin Profile
```http
GET /api/admin/me
Authorization: Bearer {token}
```

---

### 👥 VERIFIKASI PENJUAL

#### Get Pending Sellers
```http
GET /api/admin/verifikasi-penjual
Authorization: Bearer {token}
```

#### Verify Seller (Accept/Reject)
```http
POST /api/admin/verifikasi-penjual/{id}
Authorization: Bearer {token}
```
**Body:**
```json
{
  "status": "ACTIVE"  // or "REJECTED"
}
```

---

### 🏷️ KELOLA KATEGORI

#### Create Category
```http
POST /api/admin/kategoris
Authorization: Bearer {token}
```
**Body:**
```json
{
  "namaKategori": "Elektronik"
}
```

#### Update Category
```http
PUT /api/admin/kategoris/{id}
Authorization: Bearer {token}
```

#### Delete Category
```http
DELETE /api/admin/kategoris/{id}
Authorization: Bearer {token}
```

---

### ⭐ MODERASI REVIEW

#### Update Review
```http
PUT /api/admin/reviews/{id}
Authorization: Bearer {token}
```

#### Delete Review
```http
DELETE /api/admin/reviews/{id}
Authorization: Bearer {token}
```

---

### 👨‍💼 KELOLA PENJUAL (CRUD)

#### Get All Sellers
```http
GET /api/admin/penjuals
Authorization: Bearer {token}
```

#### Get Single Seller
```http
GET /api/admin/penjuals/{id}
Authorization: Bearer {token}
```

#### Create Seller
```http
POST /api/admin/penjuals
Authorization: Bearer {token}
```

#### Update Seller
```http
PUT /api/admin/penjuals/{id}
Authorization: Bearer {token}
```

#### Delete Seller
```http
DELETE /api/admin/penjuals/{id}
Authorization: Bearer {token}
```

---

## 3. PENJUAL ROUTES
> **Memerlukan authentication dengan role Penjual**

### 🔐 AUTH

#### Register Penjual
```http
POST /api/penjual/register
```
**Body (multipart/form-data):**
```
email: penjual@email.com
password: Password123!@#
namaPenjual: John Doe
nik: 1234567890123456
noHp: 08123456789
foto: [file]
fotoKtp: [file]
namaToko: Toko John
deskripsiToko: Toko elektronik terlengkap
jalan: Jl. Sudirman No. 123
rt: 001
rw: 002
desa: Kelurahan ABC
kota: Jakarta Pusat
provinsi: DKI Jakarta
```

#### Login Penjual
```http
POST /api/penjual/login
```
**Body:**
```json
{
  "email": "penjual@email.com",
  "password": "Password123!@#"
}
```

#### Logout Penjual
```http
POST /api/penjual/logout
Authorization: Bearer {token}
```

---

### 👤 PROFILE

#### Get Profile
```http
GET /api/penjual/profile
Authorization: Bearer {token}
```

#### Update Profile
```http
PUT /api/penjual/profile
Authorization: Bearer {token}
```
**Body:**
```json
{
  "namaPenjual": "John Doe Updated",
  "noHp": "08123456789",
  "namaToko": "Toko John Updated",
  "deskripsiToko": "Deskripsi baru"
}
```

---

### 📊 DASHBOARD

#### Get Dashboard Statistics
```http
GET /api/penjual/dashboard/stats
Authorization: Bearer {token}
```
**Response:**
```json
{
  "success": true,
  "data": {
    "total_produk": 10,
    "total_penjualan": 5000000,
    "pesanan_baru": 3,
    "aktivitas_terbaru": [
      {
        "judul": "Produk Ditambahkan",
        "deskripsi": "Produk 'Laptop Gaming' berhasil ditambahkan",
        "waktu": "2 jam yang lalu",
        "type": "produk"
      }
    ],
    "penjual": {
      "nama": "John Doe",
      "toko": "Toko John",
      "status": "ACTIVE"
    }
  }
}
```

---

### 📦 KELOLA PRODUK

#### Get My Products
```http
GET /api/penjual/produk
Authorization: Bearer {token}
```

#### Create Product
```http
POST /api/penjual/produk
Authorization: Bearer {token}
```
**Body:**
```json
{
  "namaProduk": "Laptop ASUS",
  "deskripsi": "Gaming laptop",
  "harga": 15000000,
  "stok": 10,
  "fotoProduk": "path/to/image.jpg",
  "kategori_id": 1
}
```
**Note:** `penjual_id` akan otomatis terisi dari user yang login

#### Get Single Product
```http
GET /api/penjual/produk/{id}
Authorization: Bearer {token}
```

#### Update Product
```http
PUT /api/penjual/produk/{id}
Authorization: Bearer {token}
```

#### Delete Product
```http
DELETE /api/penjual/produk/{id}
Authorization: Bearer {token}
```

#### Upload Product Image
```http
POST /api/penjual/produk/upload-gambar
Authorization: Bearer {token}
Content-Type: multipart/form-data
```
**Body:**
```
gambar: [file]
```
**Response:**
```json
{
  "message": "Gambar berhasil diupload",
  "path": "produk/xxxxx.jpg",
  "url": "http://localhost:8000/storage/produk/xxxxx.jpg"
}
```

---

### 📍 ALAMAT

#### Get All Addresses
```http
GET /api/penjual/alamat
Authorization: Bearer {token}
```

#### Create Address
```http
POST /api/penjual/alamat
Authorization: Bearer {token}
```
**Body:**
```json
{
  "penjual_id": 1,
  "jalan": "Jl. Sudirman No. 123",
  "rt": "001",
  "rw": "002",
  "desa": "Kelurahan ABC",
  "kota": "Jakarta Pusat",
  "provinsi": "DKI Jakarta"
}
```

#### Get Single Address
```http
GET /api/penjual/alamat/{id}
Authorization: Bearer {token}
```

#### Update Address
```http
PUT /api/penjual/alamat/{id}
Authorization: Bearer {token}
```

#### Delete Address
```http
DELETE /api/penjual/alamat/{id}
Authorization: Bearer {token}
```

---

## 🔑 Authentication Headers

Untuk semua protected routes, gunakan header:
```
Authorization: Bearer {your_token_here}
```

---

## 📝 Response Format

### Success Response
```json
{
  "message": "Success message",
  "data": { }
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  }
}
```

---

## 🛠️ Database Structure

### Tables:
1. **users** - User pembeli
2. **penjuals** - Penjual/Seller
3. **admins** - Administrator
4. **produks** - Products
5. **kategoris** - Product categories
6. **reviews** - Product reviews
7. **alamats** - Addresses
8. **personal_access_tokens** - Sanctum tokens

### Relationships:
- `Penjual` **hasMany** `Produk`
- `Penjual` **belongsTo** `Alamat`
- `Produk` **belongsTo** `Kategori`
- `Produk` **belongsTo** `Penjual`
- `Review` **belongsTo** `Produk`

---

## 🚀 Quick Start

1. **Setup Backend:**
```bash
cd backend
composer install
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

2. **Test API:**
```bash
# Test endpoint
curl http://localhost:8000/api/test

# Login Admin
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@nexel.com","password":"password"}'
```

---

## 📞 Support

Jika ada pertanyaan atau bug, hubungi tim development NEXEL.
