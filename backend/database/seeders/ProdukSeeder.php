<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; // Pastikan Carbon diimport untuk 'now()'

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Path base untuk gambar di storage/app/public
        $imagePath = 'images/produks/';

        DB::table('produks')->insert([
            // Produk 1: Laptop Lenovo ThinkPad
            [
                'namaProduk' => 'Laptop Lenovo ThinkPad',
                'deskripsi' => 'Laptop kuat untuk ngoding',
                'harga' => 12000000,
                'stok' => 10,
                'fotoProduk' => $imagePath . 'laptop1.png', 
                'penjual_id' => 1,
                'kategori_id' => 1,
                'kondisi' => 'Baru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // Produk 2: Charger Type-C
            [
                'namaProduk' => 'Charger Type-C',
                'deskripsi' => 'Charger 65W cepat',
                'harga' => 120000,
                'stok' => 25,
                // Ganti null dengan path gambar
                'fotoProduk' => $imagePath . 'typec1.png', 
                'penjual_id' => 1,
                'kategori_id' => 2,
                'kondisi' => 'Bekas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
        
        // Catatan: Jika Anda ingin banyak foto per produk (galeri), Anda perlu tabel relasi.
        // Saat ini, kita hanya menggunakan satu foto (laptop1.png & typec1.png)
        // di kolom 'fotoProduk'.
    }
}