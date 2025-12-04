<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\StorageHelper;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'namaProduk' => $this->namaProduk, 
            'deskripsi' => $this->deskripsi,
            'harga' => (int) $this->harga, 
            'stok' => (int) $this->stok,
            'kondisi' => $this->kondisi, 
            'fotoProduk' => StorageHelper::getPublicUrl($this->fotoProduk), 
            
            'created_at' => $this->created_at, 

            
            'rating' => $this->reviews_avg_rating ? round($this->reviews_avg_rating, 1) : 0,
            
            'penjual' => new PenjualResource($this->whenLoaded('penjual')), 
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}