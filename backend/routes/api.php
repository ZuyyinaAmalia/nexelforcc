<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
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

// Route Protected (Hanya bisa diakses jika punya token admin)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    
    // Cek profile admin sendiri
    Route::get('/admin/me', function (Request $request) {
        return new \App\Http\Resources\AdminResource($request->user());
    });
});
