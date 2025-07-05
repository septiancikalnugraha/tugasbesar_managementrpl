-- Script untuk mengupdate password teller dan owner
USE bank_fti;

-- Update password teller dengan password yang bisa digunakan
-- Password: teller123
UPDATE users SET password = '$2y$10$74ScAv1HV8xL6cyLIko6ke5F8ZpZajfcT.6qeG4Qw6Ar.ZpeLKf4u' WHERE email = 'teller@bank.com';

-- Update password owner dengan password yang berbeda
-- Password: owner123
UPDATE users SET password = '$2y$10$cFK4oigS1oW164qYGEvl2eTHKNW0ID6ovQH2ew4oX9eTXdCrlJzt2' WHERE email = 'owner@bank.com'; 