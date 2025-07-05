-- SQL untuk menambahkan kolom kategori pada tabel users yang sudah ada
USE bank_fti;

-- Tambahkan kolom kategori jika belum ada
ALTER TABLE users ADD COLUMN IF NOT EXISTS kategori VARCHAR(20) NOT NULL DEFAULT 'non-prioritas';

-- Update kategori untuk owner dan teller yang sudah ada
UPDATE users SET kategori = 'prioritas' WHERE role IN ('owner', 'teller'); 