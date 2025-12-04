<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\StorageHelper;

class PenjualResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_toko' => $this->namaToko,
            'nama_penjual' => $this->namaPenjual,
            'deskripsi_toko' => $this->deskripsiToko,
            'email' => $this->email,
            'no_hp' => $this->noHp,
            'foto' => StorageHelper::getPublicUrl($this->foto),
            'foto_ktp' => StorageHelper::getPublicUrl($this->fotoKtp),
            'status' => $this->status,
            // Mengambil kota dari relasi alamat
            'lokasi' => $this->whenLoaded('alamat', function() {
                return $this->alamat->kota ?? 'Semarang';
            }, 'Semarang'),
        ];
    }
}