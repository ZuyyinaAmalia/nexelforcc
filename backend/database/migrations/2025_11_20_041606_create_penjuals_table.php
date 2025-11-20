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
        Schema::create('penjuals', function (Blueprint $table) {
            $table->id();
            // Kolom dari Diagram Penjual
            $table->string('nik', 16) -> unique();
            $table->string('email') -> unique();
            $table->string('namaToko') -> unique();
            $table->text('deskripsiToko');
            $table->string('namaPenjual') -> unique();
            $table->string('noHp') -> unique();
            $table->string('foto');
            $table->string('fotoKtp');

            // Kolom Status (Mengimplementasikan StatusPenjual Enum)
            $table->enum('status', ['PENDING', 'ACTIVE', 'INACTIVE', 'REJECTED'])->default('PENDING');

            // Kolom Password
            $table->string('password'); // Password harus di-hash (bcrypt) sebelum disimpan

            // Kolom Timestamp
            $table->timestamps(); // Ini sudah mencakup created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjuals');
    }
};