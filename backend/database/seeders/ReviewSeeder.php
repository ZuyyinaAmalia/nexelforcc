<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Produk; 

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Produk yang ingin diulas
        $laptop = Produk::where('namaProduk', 'Laptop Lenovo ThinkPad')->first();
        $charger = Produk::where('namaProduk', 'Charger Type-C')->first(); // ⭐ AMBIL PRODUK CHARGER

        if (!$laptop || !$charger) {
             echo "⚠️ Warning: Produk 'Laptop' atau 'Charger' tidak ditemukan. Review seeder dilewati.\n";
             return;
        }

        $laptopId = $laptop->id;
        $chargerId = $charger->id; // ⭐ ID Produk Charger

        // 2. Data Review Dummy
        $reviewsData = [
            // Ulasan 1, 2, 3 (Asosiasi ke LAPTOP)
            [
                'produk_id' => $laptopId, // Menggunakan ID Laptop
                'rating' => 5,
                'ulasan' => 'Produknya sangat bagus dan original. Sesuai deskripsi!',
                'namaPengunjung' => 'Andi Wijaya',
                'emailPengunjung' => 'andi@example.com',
                'noHpPengunjung' => '081234567890',
                'provinsiPengunjung' => 'DKI Jakarta',
            ],
            // ... (Tambahkan dua ulasan laptop lainnya di sini)

            // ⭐ Ulasan Baru: Untuk Produk Charger Type-C
            [
                'produk_id' => $chargerId, // Menggunakan ID Charger
                'rating' => 4,
                'ulasan' => 'Charger berfungsi baik, fast charging-nya oke!',
                'namaPengunjung' => 'Doni Ardiansyah',
                'emailPengunjung' => 'doni@example.com',
                'noHpPengunjung' => '089987654321',
                'provinsiPengunjung' => 'Banten',
            ],
             [
                'produk_id' => $chargerId, // Menggunakan ID Charger
                'rating' => 5,
                'ulasan' => 'Barang sesuai harga, kualitas oke. Walaupun bekas, masih mulus.',
                'namaPengunjung' => 'Eka Putri',
                'emailPengunjung' => 'eka@example.com',
                'noHpPengunjung' => '081122334455',
                'provinsiPengunjung' => 'Jawa Tengah',
            ],
        ];

        // 3. Masukkan Data ke Database
        foreach ($reviewsData as $review) {
            Review::create($review);
        }
    }
}