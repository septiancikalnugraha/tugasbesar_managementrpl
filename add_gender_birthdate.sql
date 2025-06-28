-- Script untuk menambahkan kolom gender dan birth_date ke tabel users yang sudah ada
USE bank_fti;

-- Tambahkan kolom gender
ALTER TABLE users ADD COLUMN gender ENUM('Laki-laki', 'Perempuan') NOT NULL DEFAULT 'Laki-laki' AFTER role;

-- Tambahkan kolom birth_date
ALTER TABLE users ADD COLUMN birth_date DATE NOT NULL DEFAULT '1990-01-01' AFTER gender;

-- Update data existing untuk owner dan teller
UPDATE users SET gender = 'Laki-laki', birth_date = '1990-01-01' WHERE role = 'owner';
UPDATE users SET gender = 'Perempuan', birth_date = '1995-01-01' WHERE role = 'teller'; 