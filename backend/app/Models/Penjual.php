<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjual extends Model
{

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'penjuals'; // Laravel menebak 'penjuals' dari nama model 'Penjual'

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Kolom-kolom ini aman untuk diisi melalui $model->fill() atau $model->create().
     *
     * @var array
     */
    protected $fillable = [
        'nik',
        'email',
        'namaToko',
        'deskripsiToko',
        'namaPenjual',
        'noHp',
        'foto',
        'fotoKtp',
        'status',
        'password', 
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     * Kolom sensitif seperti password disembunyikan secara default saat model dikonversi ke array/JSON.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Atribut yang harus di-cast ke tipe bawaan.
     * Berguna untuk memastikan tipe data yang benar, terutama untuk enum atau tanggal.
     *
     * @var array
     */
    protected $casts = [
        'password' => 'hashed', // Laravel 10+ secara otomatis melakukan hashing saat disimpan
        // 'status' => StatusPenjual::class, // Jika Anda menggunakan Native Enum di PHP 8.1+ dan Laravel 9+
    ];

    /**
     * Relasi: satu penjual memiliki banyak produk.
     * Pastikan model Produk dan foreign key ('penjual_id') cocok dengan skema anda.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'penjual_id');
    }

    /**
     * Scope untuk mengambil penjual yang aktif.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    /**
     * Mutator untuk menyimpan nomor HP hanya sebagai angka (hilangkan spasi/karakter non-digit).
     *
     * @param string|null $value
     * @return void
     */
    public function setNoHpAttribute($value)
    {
        $this->attributes['noHp'] = $value === null ? null : preg_replace('/\D+/', '', $value);
    }

    /**
     * Mutator sederhana untuk menyimpan nama toko dalam Title Case.
     *
     * @param string|null $value
     * @return void
     *   */
    public function setNamaTokoAttribute($value)
    {
        $this->attributes['namaToko'] = $value === null ? null : mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Relasi: penjual belongsTo alamat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }
}
     
