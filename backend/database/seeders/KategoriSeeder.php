<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategoris')->insert([
            [
                'namaKategori' => 'Laptop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'namaKategori' => 'Charger',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'namaKategori' => 'Aksesoris',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}