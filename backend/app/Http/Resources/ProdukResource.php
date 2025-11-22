<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this merujuk pada data Model (Produk)
        return [
            'id' => $this->id,
            'nama_produk' => $this->namaProduk, // Anda bisa merubah nama key JSON sesuka hati
            'deskripsi' => $this->deskripsi,
            'harga' => (int) $this->harga, // Casting ke integer agar aman
            'stok' => (int) $this->stok,
            'foto_url' => $this->fotoProduk, // Misal ubah nama jadi foto_url
            'kategori' => $this->kategori?->namaKategori ?? 'Tanpa Kategori',
        ];
    }
}