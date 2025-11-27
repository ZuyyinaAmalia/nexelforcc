<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
<<<<<<< Updated upstream
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
=======
use App\Http\Controllers\AlamatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di sini otomatis punya prefix '/api'
| Contoh: Route::get('/alamats') → http://localhost:8000/api/alamats
*/

// Route untuk autentikasi user (default Laravel)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| CRUD Alamat Routes
|--------------------------------------------------------------------------
*/

Route::get('/alamats', [AlamatController::class, 'index']);           // GET semua alamat
Route::post('/alamats', [AlamatController::class, 'store']);          // POST tambah alamat
Route::get('/alamats/{id}', [AlamatController::class, 'show']);       // GET 1 alamat
Route::put('/alamats/{id}', [AlamatController::class, 'update']);     // PUT update alamat
Route::delete('/alamats/{id}', [AlamatController::class, 'destroy']); // DELETE hapus alamat


/*
|--------------------------------------------------------------------------
| Test Route
|--------------------------------------------------------------------------
*/
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Laravel berjalan!',
        'timestamp' => now()
    ]);
>>>>>>> Stashed changes
});
