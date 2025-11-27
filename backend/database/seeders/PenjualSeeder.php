<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Penjual;

class PenjualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Penjual dengan status PENDING (Untuk dites verifikasi)
        Penjual::create([
            'nik' => '3201123456789001',
            'email' => 'ninazuyyina16@gmail.com',
            'namaToko' => 'Toko Menunggu',
            'deskripsiToko' => 'Toko ini masih menunggu persetujuan admin.',
            'namaPenjual' => 'Budi Pending',
            'noHp' => '081234567891',
            'foto' => 'default.jpg',
            'fotoKtp' => 'ktp_budi.jpg',
            'status' => 'PENDING',
            'password' => 'password123', // Otomatis di-hash oleh model
        ]);

        // 2. Penjual dengan status ACTIVE (Untuk dites login)
        Penjual::create([
            'nik' => '3201123456789002',
            'email' => 'active@toko.com',
            'namaToko' => 'Toko Laris Manis',
            'deskripsiToko' => 'Toko ini sudah aktif dan bisa berjualan.',
            'namaPenjual' => 'Siti Aktif',
            'noHp' => '081234567892',
            'foto' => 'default.jpg',
            'fotoKtp' => 'ktp_siti.jpg',
            'status' => 'ACTIVE',
            'password' => 'password123',
        ]);

        // 3. Penjual dengan status REJECTED (Untuk tes histori tolak)
        Penjual::create([
            'nik' => '3201123456789003',
            'email' => 'rejected@toko.com',
            'namaToko' => 'Toko Ditolak',
            'deskripsiToko' => 'Maaf toko ini ditolak karena data tidak lengkap.',
            'namaPenjual' => 'Joko Ditolak',
            'noHp' => '081234567893',
            'foto' => 'default.jpg',
            'fotoKtp' => 'ktp_joko.jpg',
            'status' => 'PENDING',
            'password' => 'password123',
        ]);
    }
}