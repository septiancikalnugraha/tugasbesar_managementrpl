-- Script final untuk mengupdate password teller
USE bank_fti;

-- Update password teller dengan hash yang valid
-- Password: teller123
UPDATE users SET password = '$2y$10$74ScAv1HV8xL6cyLIko6ke5F8ZpZajfcT.6qeG4Qw6Ar.ZpeLKf4u' WHERE email = 'teller@bank.com';

-- Pastikan role dan kategori sudah benar
UPDATE users SET 
    role = 'teller',
    kategori = 'prioritas'
WHERE email = 'teller@bank.com'; 