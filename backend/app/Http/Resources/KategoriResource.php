<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // Mengubah nama key JSON jadi snake_case agar standar
            'nama_kategori' => $this->namaKategori, 
            'jumlah_produk' => $this->whenCounted('produks'), // Opsional: info jumlah produk
        ];
    }
}