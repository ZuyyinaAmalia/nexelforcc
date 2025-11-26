<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Factories\HasFactory; // Uncomment jika pakai factory

class Penjual extends Authenticatable
{
    use Notifiable;
    // use HasFactory; 

    /**
     * Nama tabel.
     */
    protected $table = 'penjuals';

    /**
     * Atribut yang dapat diisi (Mass Assignable).
     * Saya menggunakan $fillable dari kode bagian bawah karena lebih lengkap.
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
        // 'alamat_id', // Tambahkan ini JIKA relasinya adalah belongsTo
    ];

    /**
     * Atribut yang disembunyikan (Hidden).
     * Gabungan dari kedua versi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data.
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /* ==================================================
       RELASI (RELATIONSHIPS)
       ================================================== */

    /**
     * Relasi: Penjual memiliki banyak Produk.
     */
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'penjual_id');
    }

    /**
     * Relasi: Alamat.
     * CATATAN: Saya memilih 'hasOne' karena di $fillable tidak ada 'alamat_id'.
     * Artinya tabel 'alamats' yang punya kolom 'user_id' atau 'penjual_id'.
     */
    public function alamat(): HasOne
    {
        // Pastikan parameter kedua ('user_id') sesuai dengan nama kolom di tabel alamats
        return $this->hasOne(Alamat::class, 'user_id');
    }

    /* ==================================================
       SCOPES & MUTATORS
       ================================================== */

    /**
     * Scope untuk mengambil penjual yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Mutator: Bersihkan format No HP (hanya angka).
     */
    public function setNoHpAttribute($value)
    {
        $this->attributes['noHp'] = $value === null ? null : preg_replace('/\D+/', '', $value);
    }

    /**
     * Mutator: Format Nama Toko menjadi Title Case.
     */
    public function setNamaTokoAttribute($value)
    {
        $this->attributes['namaToko'] = $value === null ? null : mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

} 