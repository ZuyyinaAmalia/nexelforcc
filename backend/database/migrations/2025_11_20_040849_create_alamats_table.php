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
            
            $table->foreignId('penjual_id')->constrained('penjuals')->onDelete('cascade');
            
            $table->string('jalan');
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('desa');
            $table->string('kota');
            $table->enum('provinsi', [
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
                
                // Papua (6)
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
