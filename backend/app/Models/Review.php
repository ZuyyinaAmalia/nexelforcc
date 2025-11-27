<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 
// Hapus 'use Illuminate\Support\Str;' jika tidak dipakai di tempat lain
// Tapi kita biarkan saja karena mungkin dipakai untuk hal lain.

class Review extends Model
{
    use HasFactory;

    // KONFIGURASI UUID DIHAPUS agar menggunakan ID integer standar Laravel:
    // protected $keyType = 'string'; // Dihapus
    // public $incrementing = false;  // Dihapus

    protected $fillable = [
        'produk_id',
        'rating',
        'ulasan',
        'namaPengunjung',
        'emailPengunjung',
        'noHpPengunjung',
        'provinsiPengunjung',
    ];

    public const PROVINSI = [
        'Nanggroe Aceh Darussalam', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
        'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Bangka Belitung',
        'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten',
        'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
        'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
        'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
        'Maluku', 'Maluku Utara',
        'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
    ];

    // Fungsi boot() yang menghasilkan UUID DIHAPUS
    /*
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    */

    public function produk()
    {
        return $this->belongsTo(\App\Models\Produk::class, 'produk_id'); 
    }
}