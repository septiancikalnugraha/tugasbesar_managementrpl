-- SQL untuk menambahkan kolom last_login pada tabel users yang sudah ada
USE bank_fti;

-- Tambahkan kolom last_login jika belum ada
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL DEFAULT NULL AFTER profile_photo; 