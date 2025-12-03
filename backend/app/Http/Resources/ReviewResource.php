<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Format tanggal agar mudah dibaca di frontend
        $formattedDate = \Carbon\Carbon::parse($this->created_at)->locale('id')->isoFormat('D MMMM YYYY');

        return [
            'id' => $this->id,
            'rating' => (int) $this->rating,
            'ulasan' => $this->ulasan,
            'namaPengunjung' => $this->namaPengunjung,
            'emailPengunjung' => $this->emailPengunjung,
            'provinsiPengunjung' => $this->provinsiPengunjung,
            'tanggal' => $formattedDate, // Gunakan tanggal yang sudah diformat
        ];
    }
}