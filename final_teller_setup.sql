-- Script final untuk setup teller
USE bank_fti;

-- Pastikan data teller sudah benar
UPDATE users SET 
    role = 'teller',
    kategori = 'prioritas',
    gender = 'Perempuan',
    birth_date = '1995-01-01',
    provinsi = 'Jawa Barat',
    kota = 'Bandung',
    password = '$2y$10$74ScAv1HV8xL6cyLIko6ke5F8ZpZajfcT.6qeG4Qw6Ar.ZpeLKf4u'
WHERE email = 'teller@bank.com';

-- Verifikasi data teller
SELECT 
    id,
    full_name,
    email,
    role,
    kategori,
    gender,
    birth_date,
    provinsi,
    kota,
    SUBSTRING(password, 1, 20) as password_preview
FROM users 
WHERE email = 'teller@bank.com'; 