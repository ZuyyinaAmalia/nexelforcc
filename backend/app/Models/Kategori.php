<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;


class Kategori extends Model
{
    // use HasFactory;

    // Sesuaikan dengan nama kolom di migration
    protected $fillable = [
        'namaKategori'
    ];

    // Relasi: 1 Kategori memiliki banyak Produk
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
}
