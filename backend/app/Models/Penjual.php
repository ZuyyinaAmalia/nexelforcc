<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjual extends Authenticatable
{
    use Notifiable;

    protected $table = 'penjuals';

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
        'alamat_id', // ✅ PENTING
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'penjual_id');
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    /* ================= SCOPES ================= */

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /* ================= MUTATORS ================= */

    public function setNoHpAttribute($value)
    {
        $this->attributes['noHp'] =
            $value === null ? null : preg_replace('/\D+/', '', $value);
    }

    public function setNamaTokoAttribute($value)
    {
        $this->attributes['namaToko'] =
            $value === null ? null : mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}
