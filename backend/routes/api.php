<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ===== IMPORT CONTROLLERS =====
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPenjualController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AlamatController;

/*
|--------------------------------------------------------------------------
| API Routes - NEXEL E-Commerce
|--------------------------------------------------------------------------
| Routes dikelompokkan berdasarkan:
| 1. PUBLIC Routes (Tanpa Auth)
| 2. ADMIN Routes (Perlu Auth Admin)
| 3. PENJUAL Routes (Perlu Auth Penjual)
| 4. SHARED Routes (Bisa diakses berbagai role)
*/

// ===== TEST ENDPOINT =====
Route::get('/test', function () {
    return ['message' => 'API NEXEL berjalan dengan baik!', 'version' => '1.0'];
});

// =========================================================================
// 1. PUBLIC ROUTES (Tidak Perlu Authentication)
// =========================================================================

Route::prefix('public')->group(function () {
    
    // ----- PRODUK (Public - untuk customer lihat produk) -----
    Route::get('/produks', [ProdukController::class, 'index']);
    Route::get('produks/search', [ProdukController::class, 'search']);
    Route::get('/produks/{produk}', [ProdukController::class, 'show']);
    
    // ----- KATEGORI (Public - untuk filter produk) -----
    Route::get('/kategoris', [KategoriController::class, 'index']);
    Route::get('/kategoris/{kategori}', [KategoriController::class, 'show']);

    
    // ----- REVIEW (Public - customer bisa lihat & buat review tanpa login) -----
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/product/{produkId}', [ReviewController::class, 'getReviewByIdProduk']);
    Route::get('/reviews/{id}', [ReviewController::class, 'show']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});

// =========================================================================
// 2. ADMIN ROUTES
// =========================================================================

// ----- ADMIN AUTH (Login/Logout) -----
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminController::class, 'login']);
    
    // Routes yang perlu auth admin
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout']);
        Route::get('/me', function (Request $request) {
            return new \App\Http\Resources\AdminResource($request->user());
        });
        
        // ----- VERIFIKASI PENJUAL (Admin Only) -----
        Route::get('/verifikasi-penjual', [AdminPenjualController::class, 'index']);
        Route::post('/verifikasi-penjual/{id}', [AdminPenjualController::class, 'verifikasi']);
        
        // ----- KELOLA KATEGORI (Admin Only) -----
        Route::post('/kategoris', [KategoriController::class, 'store']);
        Route::put('/kategoris/{kategori}', [KategoriController::class, 'update']);
        Route::delete('/kategoris/{kategori}', [KategoriController::class, 'destroy']);
        
        // ----- KELOLA REVIEW (Admin bisa moderasi review) -----
        Route::put('/reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
        
        // ----- KELOLA SEMUA PENJUAL (Admin CRUD) -----
        Route::apiResource('penjuals', PenjualController::class);
    });
});

// =========================================================================
// 3. PENJUAL ROUTES
// =========================================================================

Route::prefix('penjual')->group(function () {
    
    // ----- AUTH PENJUAL (Register/Login) -----
    Route::post('/register', [PenjualController::class, 'register']);
    Route::post('/login', [PenjualController::class, 'login']);
    
    // Routes yang perlu auth penjual
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [PenjualController::class, 'logout']);
        
        // ----- PROFILE PENJUAL -----
        Route::get('/profile', [PenjualController::class, 'profile']);
        Route::put('/profile', [PenjualController::class, 'updateProfile']);
        
        // ----- DASHBOARD STATS (untuk halaman dashboard penjual) -----
        Route::get('/dashboard/stats', [PenjualController::class, 'dashboardStats']);
        
        // ----- KELOLA PRODUK PENJUAL -----
        Route::prefix('produk')->group(function () {
            Route::get('/', [ProdukController::class, 'index']); // List produk penjual
            Route::post('/', [ProdukController::class, 'store']); // Tambah produk
            Route::get('/{produk}', [ProdukController::class, 'show']); // Detail produk
            Route::put('/{produk}', [ProdukController::class, 'update']); // Update produk
            Route::delete('/{produk}', [ProdukController::class, 'destroy']); // Hapus produk
            
            // Upload gambar produk
            Route::post('/upload-gambar', function (Request $request) {
                $request->validate([
                    'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
                ]);
                
                $path = $request->file('gambar')->store('images/produks', 'public');
                
                return response()->json([
                    'message' => 'Gambar berhasil diupload',
                    'path' => $path,
                    'url' => asset('storage/' . $path)
                ]);
            });
        });
        
        // ----- ALAMAT PENJUAL -----
        Route::prefix('alamat')->group(function () {
            Route::get('/', [AlamatController::class, 'index']);
            Route::post('/', [AlamatController::class, 'store']);
            Route::get('/{id}', [AlamatController::class, 'show']);
            Route::put('/{id}', [AlamatController::class, 'update']);
            Route::delete('/{id}', [AlamatController::class, 'destroy']);
        });
    });
});
