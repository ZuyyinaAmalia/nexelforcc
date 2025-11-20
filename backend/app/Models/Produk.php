<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    // 1 produk wajib punya 1 kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
