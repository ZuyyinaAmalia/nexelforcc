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
        Schema::create('alamats', function (Blueprint $table) {
            $table->id();
            
            // --- PENTING: GUNAKAN 'user_id' ---
            // Kita namakan 'user_id' agar cocok dengan kodingan di Controller.
            // Tapi kita sambungkan (constrained) ke tabel 'penjuals'.
            $table->foreignId('user_id')->constrained('penjuals')->onDelete('cascade');
            
            $table->string('jalan');
            $table->string('rt', 3)->nullable(); // Kasih nullable biar aman
            $table->string('rw', 3)->nullable();
            $table->string('desa');
            $table->string('kota');
            
            // Enum Provinsi (Pastikan isinya sama persis dengan yang di Model & Vue)
            $table->enum('provinsi', [
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
                'DKI Jakarta', 
                'Jawa Barat', 
                'Jawa Tengah', 
                'DI Yogyakarta', 
                'Jawa Timur',
                'Banten',
                'Bali', 
                'Nusa Tenggara Barat', 
                'Nusa Tenggara Timur',
                'Kalimantan Barat', 
                'Kalimantan Tengah', 
                'Kalimantan Selatan',
                'Kalimantan Timur', 
                'Kalimantan Utara',
                'Sulawesi Utara', 
                'Sulawesi Tengah', 
                'Sulawesi Selatan', 
                'Sulawesi Tenggara',
                'Gorontalo', 
                'Sulawesi Barat',
                'Maluku', 
                'Maluku Utara',
                'Papua', 
                'Papua Barat', 
                'Papua Selatan',
                'Papua Tengah',
                'Papua Pegunungan',
                'Papua Barat Daya'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamats');
    }
};