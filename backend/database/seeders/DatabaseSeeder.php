<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil Seeder lokal Anda menggunakan $this->call()
        $this->call([
            // 1. Panggil Seeder yang TIDAK memiliki Foreign Key
            PenjualSeeder::class,
            KategoriSeeder::class,

            // 2. Panggil Seeder yang memiliki Foreign Key (BERGANTUNG)
            ProdukSeeder::class,
            
            // 3. Panggil Seeder yang BERGANTUNG pada Produk
            // ReviewSeeder harus dipanggil setelah ProdukSeeder
            ReviewSeeder::class, // <-- ReviewSeeder ditambahkan di sini.
            // Opsional: Jika Anda membuat AdminSeeder/ReviewSeeder, letakkan di sini.
        ]);



        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Panggil seeders
        $this->call([
            AdminSeeder::class,
            PenjualSeeder::class,
        ]);
    }
}
