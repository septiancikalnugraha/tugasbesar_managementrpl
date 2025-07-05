-- Script untuk memperbarui struktur database bank_fti
USE bank_fti;

-- 1. Tambahkan kolom gender dan birth_date jika belum ada
ALTER TABLE users ADD COLUMN IF NOT EXISTS gender ENUM('Laki-laki', 'Perempuan') NOT NULL DEFAULT 'Laki-laki' AFTER role;
ALTER TABLE users ADD COLUMN IF NOT EXISTS birth_date DATE NOT NULL DEFAULT '1990-01-01' AFTER gender;

-- 2. Tambahkan kolom provinsi dan kota jika belum ada
ALTER TABLE users ADD COLUMN IF NOT EXISTS provinsi VARCHAR(50) DEFAULT NULL AFTER birth_date;
ALTER TABLE users ADD COLUMN IF NOT EXISTS kota VARCHAR(50) DEFAULT NULL AFTER provinsi;

-- 3. Tambahkan kolom kategori jika belum ada
ALTER TABLE users ADD COLUMN IF NOT EXISTS kategori VARCHAR(20) NOT NULL DEFAULT 'non-prioritas' AFTER kota;

-- 4. Tambahkan kolom last_login jika belum ada
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL DEFAULT NULL AFTER profile_photo;

-- 5. Update data existing untuk owner dan teller
UPDATE users SET 
    gender = 'Laki-laki', 
    birth_date = '1990-01-01',
    provinsi = 'DKI Jakarta',
    kota = 'Jakarta Pusat',
    kategori = 'prioritas'
WHERE role = 'owner';

UPDATE users SET 
    gender = 'Perempuan', 
    birth_date = '1995-01-01',
    provinsi = 'Jawa Barat',
    kota = 'Bandung',
    kategori = 'prioritas'
WHERE role = 'teller';

-- 6. Buat tabel chat_messages jika belum ada
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender ENUM('user','ai','admin') NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    faq_id INT DEFAULT NULL,
    INDEX(user_id),
    INDEX(faq_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Buat tabel faq_bot jika belum ada
CREATE TABLE IF NOT EXISTS faq_bot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    keyword VARCHAR(100) NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Insert data FAQ jika tabel kosong
INSERT IGNORE INTO faq_bot (question, keyword, answer) VALUES
('Bagaimana cara registrasi?', 'registrasi', 'Untuk registrasi, klik menu Daftar, isi data diri Anda, lalu ikuti instruksi yang diberikan.'),
('Bagaimana cara login?', 'login', 'Masukkan email dan password Anda pada halaman login, lalu klik tombol Login.'),
('Bagaimana cara top up?', 'top up', 'Pilih menu "Top Up", pilih e-wallet, masukkan rekening & nominal, lalu klik "Top Up". Saldo Anda akan bertambah.'),
('Bagaimana cara transfer?', 'transfer', 'Pilih menu "Transfer", masukkan rekening tujuan dan nominal, lalu klik "Kirim".'),
('Bagaimana sistem tagihan?', 'tagihan', 'Menu tagihan digunakan untuk membayar berbagai tagihan seperti listrik, air, dan lainnya. Pilih tagihan, masukkan data, lalu bayar.'),
('Bagaimana cek saldo?', 'cek saldo', 'Saldo Anda dapat dilihat di halaman utama dashboard setelah login.'),
('Bagaimana melihat riwayat transaksi?', 'riwayat', 'Pilih menu "Riwayat Transaksi" untuk melihat semua transaksi yang pernah Anda lakukan.'),
('Bagaimana mengubah profil?', 'profil', 'Masuk ke menu Profil, lalu klik Edit untuk mengubah data diri Anda.'),
('Bagaimana logout?', 'logout', 'Klik menu "Logout" untuk keluar dari akun Anda dengan aman.'),
('Bagaimana cara upgrade prioritas?', 'upgrade', 'Pilih menu Upgrade, lengkapi data dan upload dokumen yang diperlukan, lalu tunggu verifikasi dari admin.'),
('Bagaimana cara undang teman?', 'undang', 'Gunakan fitur Undang Teman di menu utama, salin link referral Anda dan bagikan ke teman.'),
('Bagaimana upload bukti?', 'upload', 'Pada halaman transaksi, klik Upload Bukti, pilih file gambar, lalu simpan.'),
('Bagaimana jika saldo tidak cukup saat order tagihan?', 'saldo tidak cukup', 'Pastikan saldo Anda mencukupi sebelum melakukan pembayaran tagihan. Jika tidak cukup, lakukan top up terlebih dahulu.'),
('Bagaimana proses tagihan oleh teller?', 'teller', 'Teller akan memproses tagihan Anda setelah Anda melakukan pembayaran dan mengupload bukti.'),
('Bagaimana keamanan data saya?', 'keamanan', 'Data Anda dijamin aman dan terenkripsi sesuai standar keamanan perbankan.'); 