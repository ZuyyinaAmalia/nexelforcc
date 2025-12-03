<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    // mass assignable fields
    protected $fillable = [
        'namaProduk',
        'deskripsi',
        'kondisi',
        'harga',
        'stok',
        'fotoProduk',
        'kategori_id',
        'penjual_id',
        'statusProduk',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];
    
    // 1 produk wajib punya 1 kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    
    // 1 produk wajib punya 1 penjual
    public function penjual()
    {
        return $this->belongsTo(Penjual::class);
    }
    
    // ⭐ RELASI KE ULASAN (REVIEWS)
    public function reviews()
    {
        return $this->hasMany(Review::class, 'produk_id');
    }
}
