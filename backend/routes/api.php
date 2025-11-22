<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/test', function () {
        return ['message' => 'API berjalan!'];
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
