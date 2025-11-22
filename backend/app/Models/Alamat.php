<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    // Nama tabel (opsional, Laravel auto-detect dari nama model)
    protected $table = 'alamats';

    // Kolom yang boleh diisi massal (mass assignment)
    protected $fillable = [
        'user_id',
        'jalan',
        'rt',
        'rw',
        'desa',
        'kota',
        'provinsi',
    ];

    // Kolom yang disembunyikan saat di-convert ke JSON
    protected $hidden = [];

    // Cast tipe data
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi: Alamat dimiliki oleh 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Konstanta list provinsi
    const PROVINSI_LIST = [
        'Aceh',
        'Sumatera Utara',
        'Sumatera Barat',
        'Riau',
        'Kepulauan Riau',
        'Jambi',
        'Sumatera Selatan',
        'Kepulauan Bangka Belitung',
        'Bengkulu',
        'Lampung',
        'DKI Jakarta',
        'Jawa Barat',
        'Banten',
        'Jawa Tengah',
        'DI Yogyakarta',
        'Jawa Timur',
        'Bali',
        'Nusa Tenggara Barat',
        'Nusa Tenggara Timur',
        'Kalimantan Barat',
        'Kalimantan Tengah',
        'Kalimantan Selatan',
        'Kalimantan Timur',
        'Kalimantan Utara',
        'Sulawesi Utara',
        'Gorontalo',
        'Sulawesi Tengah',
        'Sulawesi Barat',
        'Sulawesi Selatan',
        'Sulawesi Tenggara',
        'Maluku',
        'Maluku Utara',
        'Papua',
        'Papua Barat',
        'Papua Tengah',
        'Papua Pegunungan',
        'Papua Selatan',
        'Papua Barat Daya',
    ];
}
