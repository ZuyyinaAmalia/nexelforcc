<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
         $this->call([
            PenjualSeeder::class,
            KategoriSeeder::class,
            ProdukSeeder::class,
        ]);

        
        \App\Models\Admin::create([
        'email' => 'admin@gmail.com',
        'password' => 'admin123', // Otomatis di-hash oleh Model
        ]);
    }
}
