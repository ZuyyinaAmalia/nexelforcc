<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Penjual extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id'];

    // Sembunyikan password agar tidak bocor saat return JSON
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relasi ke Alamat
    public function alamat()
    {
        return $this->hasOne(Alamat::class, 'user_id');
    }
}