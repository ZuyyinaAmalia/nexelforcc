-- Drop the existing reviews table
DROP TABLE IF EXISTS reviews CASCADE;

-- Recreate the reviews table with proper BIGSERIAL ID
CREATE TABLE reviews (
    id BIGSERIAL PRIMARY KEY,
    produk_id BIGINT NOT NULL,
    rating INTEGER NOT NULL,
    ulasan TEXT NOT NULL,
    "namaPengunjung" VARCHAR(255) NOT NULL,
    "emailPengunjung" VARCHAR(255) NOT NULL,
    "noHpPengunjung" VARCHAR(255) NOT NULL,
    "provinsiPengunjung" VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    CONSTRAINT reviews_produk_id_foreign FOREIGN KEY (produk_id) REFERENCES produks(id) ON DELETE CASCADE,
    CONSTRAINT reviews_produk_email_unique UNIQUE (produk_id, "emailPengunjung")
);
