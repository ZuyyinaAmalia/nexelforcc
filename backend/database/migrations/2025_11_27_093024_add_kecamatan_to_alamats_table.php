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
        Schema::table('alamats', function (Blueprint $table) {
            // Menambahkan kolom kecamatan setelah desa
            // Kita gunakan tipe string (varchar) bukan enum, karena datanya ribuan
            $table->string('kecamatan')->after('desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alamats', function (Blueprint $table) {
            $table->dropColumn('kecamatan');
        });
    }
};