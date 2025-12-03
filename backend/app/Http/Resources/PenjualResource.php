<?php
// App\Http\Resources\PenjualResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenjualResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_toko' => $this->namaToko,
            'nama_penjual' => $this->namaPenjual,
            'deskripsi_toko' => $this->deskripsiToko,
            // Tambahkan data alamat jika Anda punya kolom alamat di Model Penjual
            'lokasi' => $this->city ?? 'Semarang', // Contoh jika ada kolom city
            
        ];
    }
}