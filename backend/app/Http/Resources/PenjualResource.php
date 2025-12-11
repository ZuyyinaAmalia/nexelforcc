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
            'namaToko' => $this->namaToko,
            'namaPenjual' => $this->namaPenjual,
            'deskripsiToko' => $this->deskripsiToko,
            'email' => $this->email,
            'noHp' => $this->noHp,
            'nik' => $this->nik,
            'foto' => StorageHelper::getPublicUrl($this->foto),
            'fotoKtp' => StorageHelper::getPublicUrl($this->fotoKtp),
            'status' => $this->status,
            'created_at' => $this->created_at,
            // Mengambil kota dari relasi alamat
            'alamat' => $this->whenLoaded('alamat'),
            'lokasi' => $this->whenLoaded('alamat', function() {
                return $this->alamat->kota ?? 'Semarang';
            }, 'Semarang'),
        ];
    }
}