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

            $table->string('nik', 16)->unique();
            $table->string('email')->unique();
            $table->string('namaToko');
            $table->text('deskripsiToko')->nullable();
            $table->string('namaPenjual');
            $table->string('noHp')->nullable();
            $table->string('foto')->nullable();
            $table->string('fotoKtp')->nullable();

            $table->enum('status', ['PENDING', 'ACTIVE', 'INACTIVE', 'REJECTED'])->default('PENDING');

            $table->string('password');
            $table->timestamps();
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