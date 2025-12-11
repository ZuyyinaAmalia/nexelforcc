<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Dropping reviews table...\n";
DB::statement('DROP TABLE IF EXISTS reviews CASCADE');

echo "Creating reviews table with proper BIGSERIAL ID...\n";
DB::statement("
    CREATE TABLE reviews (
        id BIGSERIAL PRIMARY KEY,
        produk_id BIGINT NOT NULL,
        rating INTEGER NOT NULL,
        ulasan TEXT NOT NULL,
        \"namaPengunjung\" VARCHAR(255) NOT NULL,
        \"emailPengunjung\" VARCHAR(255) NOT NULL,
        \"noHpPengunjung\" VARCHAR(255) NOT NULL,
        \"provinsiPengunjung\" VARCHAR(255) NOT NULL,
        created_at TIMESTAMP,
        updated_at TIMESTAMP,
        CONSTRAINT reviews_produk_id_foreign FOREIGN KEY (produk_id) REFERENCES produks(id) ON DELETE CASCADE
    )
");

echo "Creating unique constraint...\n";
DB::statement('CREATE UNIQUE INDEX reviews_produk_email_unique ON reviews (produk_id, "emailPengunjung")');

echo "✅ Reviews table successfully recreated with auto-incrementing ID!\n";
