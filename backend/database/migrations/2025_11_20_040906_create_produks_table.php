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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('namaProduk');
            $table->double('harga');
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->enum('kondisi', ['Baru', 'Bekas'])->default('Baru');
            $table->string('fotoProduk')->nullable();
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            
            $table->foreignId('penjual_id')
              ->nullable() // Boleh null dulu kalau ada produk tanpa penjual
              ->constrained('penjuals')
              ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
