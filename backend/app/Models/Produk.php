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
        'harga',
        'stok',
        'fotoProduk',
        'kategori_id',
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
}
