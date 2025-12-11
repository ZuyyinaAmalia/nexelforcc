<?php

namespace Database\Seeders;

use App\Models\Penjual;
use App\Models\Alamat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenjualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Penjual 1
        $penjual1 = Penjual::create([
            'namaPenjual' => 'Budi Santoso',
            'namaToko' => 'Toko Elektronik Budi',
            'deskripsiToko' => 'Menjual berbagai elektronik berkualitas dengan harga terjangkau',
            'email' => 'budi@tokoelektronik.com',
            'password' => Hash::make('password123'),
        ]);

        Alamat::create([
            'user_id' => $penjual1->id,
            'jalan' => 'Jl. Sudirman No. 123',
            'rt' => '05',
            'rw' => '03',
            'desa' => 'Kembangsari',
            'kecamatan' => 'Semarang Tengah',
            'kota' => 'Semarang',
            'provinsi' => 'Jawa Tengah',
        ]);

        // Penjual 2
        $penjual2 = Penjual::create([
            'namaPenjual' => 'Siti Rahayu',
            'namaToko' => 'Fashion House Siti',
            'deskripsiToko' => 'Koleksi fashion terkini untuk pria dan wanita',
            'email' => 'siti@fashionhouse.com',
            'password' => Hash::make('password123'),
        ]);

        Alamat::create([
            'user_id' => $penjual2->id,
            'jalan' => 'Jl. Pemuda No. 45',
            'rt' => '02',
            'rw' => '01',
            'desa' => 'Pleburan',
            'kecamatan' => 'Semarang Selatan',
            'kota' => 'Semarang',
            'provinsi' => 'Jawa Tengah',
        ]);

        // Penjual 3
        $penjual3 = Penjual::create([
            'namaPenjual' => 'Ahmad Hidayat',
            'namaToko' => 'Toko Buku Hidayat',
            'deskripsiToko' => 'Menyediakan buku-buku pelajaran, novel, dan komik',
            'email' => 'ahmad@tokobuku.com',
            'password' => Hash::make('password123'),
        ]);

        Alamat::create([
            'user_id' => $penjual3->id,
            'jalan' => 'Jl. Pandanaran No. 88',
            'rt' => '08',
            'rw' => '04',
            'desa' => 'Mugassari',
            'kecamatan' => 'Semarang Barat',
            'kota' => 'Semarang',
            'provinsi' => 'Jawa Tengah',
        ]);
    }
}
