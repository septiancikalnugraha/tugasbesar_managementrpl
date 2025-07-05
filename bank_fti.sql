-- SQL untuk membuat database dan tabel users
CREATE DATABASE IF NOT EXISTS bank_fti;
USE bank_fti;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    account_number VARCHAR(25) UNIQUE NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    role VARCHAR(20) NOT NULL DEFAULT 'nasabah',
    gender ENUM('Laki-laki', 'Perempuan') NOT NULL,
    birth_date DATE NOT NULL,
    provinsi VARCHAR(50) DEFAULT NULL,
    kota VARCHAR(50) DEFAULT NULL,
    kategori VARCHAR(20) NOT NULL DEFAULT 'non-prioritas',
    profile_photo VARCHAR(255) NULL,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert owner dan teller dengan hash password valid hasil password_hash PHP
INSERT INTO users (full_name, email, phone, password, account_number, role, gender, birth_date, provinsi, kota, kategori) VALUES
('Owner Bank', 'owner@bank.com', '0811111111', '$2y$10$wH6Qw6Qw6Qw6Qw6Qw6Qw6uQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6', '+62 30100000001', 'owner', 'Laki-laki', '1990-01-01', 'DKI Jakarta', 'Jakarta Pusat', 'prioritas'),
('Teller Bank', 'teller@bank.com', '0822222222', '$2y$10$wH6Qw6Qw6Qw6Qw6Qw6Qw6uQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6', '+62 30100000002', 'teller', 'Perempuan', '1995-01-01', 'Jawa Barat', 'Bandung', 'prioritas');

-- Tabel untuk riwayat top up
CREATE TABLE IF NOT EXISTS topup_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ewallet VARCHAR(50) NOT NULL,
    rekening VARCHAR(30) NOT NULL,
    nominal INT NOT NULL,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    review TEXT,
    rating INT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel untuk daftar penerima transfer
CREATE TABLE IF NOT EXISTS receivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    account_number VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel untuk riwayat transfer
CREATE TABLE IF NOT EXISTS transfer_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_user INT NOT NULL,
    to_user INT NOT NULL,
    receiver_id INT NOT NULL,
    amount INT NOT NULL,
    note VARCHAR(100),
    transfer_date DATE,
    rating INT,
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES receivers(id) ON DELETE CASCADE
);

-- Tabel untuk menyimpan tagihan/topup/pembayaran
CREATE TABLE IF NOT EXISTS tagihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    jenis VARCHAR(32) NOT NULL, -- contoh: pulsa, data, game, PLN, BPJS, dll
    keterangan VARCHAR(128) NOT NULL, -- nomor HP, ID pelanggan, dsb
    nominal INT NOT NULL,
    status ENUM('Draft','Belum Lunas','Lunas') DEFAULT 'Draft',
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
); 