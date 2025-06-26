-- SQL untuk membuat database dan tabel users
CREATE DATABASE IF NOT EXISTS bank_fti;
USE bank_fti;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    account_number VARCHAR(20) UNIQUE NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    role VARCHAR(20) NOT NULL DEFAULT 'nasabah',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert owner dan teller dengan hash password valid hasil password_hash PHP
INSERT INTO users (full_name, email, phone, password, account_number, role) VALUES
('Owner Bank', 'owner@bank.com', '0811111111', '$2y$10$wH6Qw6Qw6Qw6Qw6Qw6Qw6uQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6', 'FTI00000001', 'owner'),
('Teller Bank', 'teller@bank.com', '0822222222', '$2y$10$wH6Qw6Qw6Qw6Qw6Qw6Qw6uQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6', 'FTI00000002', 'teller'); 