<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alamat extends Model
{
    protected $table = 'alamats';

    // Kolom yang boleh diisi massal (mass assignment)
    protected $fillable = [
        'penjual_id',
        'jalan',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kota',
        'provinsi',
    ];

    // Cast tipe data (Opsional tapi bagus)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi: Alamat dimiliki oleh 1 User
    public function penjual(): BelongsTo
    {
        return $this->belongsTo(Penjual::class, 'penjual_id', 'id');
    }

    // Konstanta list provinsi
    const PROVINSI_LIST = [
        'Nanggroe Aceh Darussalam', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
        'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Bangka Belitung',
        'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten',
        'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
        'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
        'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
        'Maluku', 'Maluku Utara',
        'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
    ];
}