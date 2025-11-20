<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');

            $table->integer('rating');
            $table->text('ulasan');
            $table->string('namaPengunjung');
            $table->string('emailPengunjung');
            $table->string('noHpPengunjung');

            $table->enum('provinsiPengunjung', [
                // Sumatera (10)
                'Nanggroe Aceh Darussalam', 
                'Sumatera Utara', 
                'Sumatera Barat', 
                'Riau', 
                'Kepulauan Riau',
                'Jambi', 
                'Sumatera Selatan', 
                'Bengkulu', 
                'Lampung', 
                'Bangka Belitung',

                // Jawa (6)
                'DKI Jakarta', 
                'Jawa Barat', 
                'Jawa Tengah', 
                'DI Yogyakarta', 
                'Jawa Timur',
                'Banten',

                // Bali & Nusa Tenggara (3)
                'Bali', 
                'Nusa Tenggara Barat', 
                'Nusa Tenggara Timur',

                // Kalimantan (5)
                'Kalimantan Barat', 
                'Kalimantan Tengah', 
                'Kalimantan Selatan',
                'Kalimantan Timur', 
                'Kalimantan Utara',

                // Sulawesi (6)
                'Sulawesi Utara', 
                'Sulawesi Tengah', 
                'Sulawesi Selatan', 
                'Sulawesi Tenggara',
                'Gorontalo', 
                'Sulawesi Barat',

                // Maluku (2)
                'Maluku', 
                'Maluku Utara',

                // Papua (6) - Update Terbaru
                'Papua', 
                'Papua Barat', 
                'Papua Selatan',      // Baru
                'Papua Tengah',       // Baru
                'Papua Pegunungan',   // Baru
                'Papua Barat Daya'    // Baru
            ]);

            $table->unique(['produk_id', 'emailPengunjung']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
