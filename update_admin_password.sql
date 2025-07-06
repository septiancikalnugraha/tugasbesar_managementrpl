-- Hapus data admin ganda, sisakan satu (id terkecil)
DELETE FROM users WHERE email = 'admin@bank.com' AND id NOT IN (
    SELECT MIN(id) FROM (SELECT id FROM users WHERE email = 'admin@bank.com') AS t
);

-- Update password admin ke 'admin123'
UPDATE users
SET password = '$2y$10$8gk2vH0xx8xnOKeA772YHu.LosnjrBmksGGO5v4aC8iG2DDA6mP.C'
WHERE email = 'admin@bank.com';

-- Hash di atas adalah hasil dari password_hash('admin123', PASSWORD_DEFAULT) di server Anda