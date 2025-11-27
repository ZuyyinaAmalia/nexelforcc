<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\AdminController;


Route::middleware('api')->group(function () {
    Route::get('/test', function () {
        return ['message' => 'API berjalan!'];
    });
    
    // Produk CRUD API
    Route::apiResource('produks', ProdukController::class);
    Route::apiResource('kategoris', KategoriController::class);
});

// Route Public (Bisa diakses siapa saja untuk daftar/login awal)
Route::post('/admin/login', [AdminController::class, 'login']);

Route::apiResource('penjuals', PenjualController::class);

Route::post('/register-penjual', [PenjualController::class, 'register']);
Route::post('/login-penjual', [PenjualController::class, 'login']);

// === ROUTE PROTECTED (Butuh Token) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout-penjual', [PenjualController::class, 'logout']);
    
    // Nanti endpoint CRUD Produk ditaruh di sini agar aman
    Route::get('/penjual-profile', function (Request $request) {
        return $request->user();
    });
});

// Route Protected (Hanya bisa diakses jika punya token admin)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    
    // Cek profile admin sendiri
    Route::get('/admin/me', function (Request $request) {
        return new \App\Http\Resources\AdminResource($request->user());
    });
});

Route::prefix('reviews')->group(function () {
    
    // 1. Ambil semua review (bisa filter ?product_id=...)
    Route::get('/', [ReviewController::class, 'index']);

    // 2. Ambil review KHUSUS berdasarkan ID Produk (Sesuai Diagram)
    // Contoh: /api/reviews/product/9912-xkkl-1231
    Route::get('/product/{produkId}', [ReviewController::class, 'getReviewByIdProduk']);

    // 3. Buat review baru
    Route::post('/', [ReviewController::class, 'store']);

    // 4. Lihat detail satu review
    Route::get('/{id}', [ReviewController::class, 'show']);

    // 5. Edit review (Opsional)
    Route::put('/{id}', [ReviewController::class, 'update']);

    // 6. Hapus review
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
});
