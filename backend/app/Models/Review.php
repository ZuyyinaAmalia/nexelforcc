<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Jangan lupa import ini

class Review extends Model
{
    use HasFactory;

    // 1. Matikan Auto Increment bawaan (karena kita pakai String)
    public $incrementing = false;

    // 2. Beritahu tipe data Primary Key adalah String
    protected $keyType = 'string';

    // 3. Daftarkan kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'produk_id',
        'rating',
        'ulasan',
        'namaPengunjung',
        'emailPengunjung',
        'noHpPengunjung',
        'provinsiPengunjung',
    ];

    // 4. Buat ID otomatis terisi UUID saat data dibuat
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                // Mengisi ID dengan UUID (contoh: 550e8400-e29b-41d4-a716-446655440000)
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // 5. Relasi ke Produk (Opsional, buat jaga-jaga)
    public function produk()
    {
        // Asumsi nama model produkmu adalah 'Product' atau 'Produk'
        // Sesuaikan dengan nama class model produk kamu
        return $this->belongsTo(Produk::class, 'produk_id'); 
    }
}