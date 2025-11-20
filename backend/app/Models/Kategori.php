<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    // 1 kategori bisa punya banyak produk
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
}
