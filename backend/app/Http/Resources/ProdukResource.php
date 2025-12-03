<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_produk' => $this->namaProduk, 
            'deskripsi' => $this->deskripsi,
            'harga' => (int) $this->harga, 
            'stok' => (int) $this->stok,
            'statusProduk' => $this->statusProduk, 
            'foto_url' => $this->fotoProduk, 
            
            'created_at' => $this->created_at, 
            
            'penjual' => new PenjualResource($this->whenLoaded('penjual')), 
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}