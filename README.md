# Bank FTI - Sistem Manajemen Transaksi

Aplikasi web untuk manajemen transaksi bank (top up, transfer, riwayat, profil, dsb) berbasis PHP dan MySQL.

---

## 📸 Screenshot

| Dashboard | Riwayat Top Up | Profil User |
|-----------|---------------|------------|
| ![Dashboard](image/ss_dashboard.png) | ![Riwayat](image/ss_riwayat.png) | ![Profil](image/ss_profil.png) |

---

## ✨ Fitur Utama
- **Autentikasi**: Login (tanpa pilih role, role otomatis terdeteksi), register (dengan gender, tanggal lahir, provinsi, kota/kabupaten), logout, session user.
- **Dashboard**: Info saldo, nomor rekening, menu transaksi, tagihan, dsb.
- **Top Up & Transfer**: Form top up e-wallet, transfer antar rekening, simpan riwayat.
- **Riwayat Transaksi**: Riwayat top up & transfer, filter tab, cetak/print riwayat.
- **Profil User**: Lihat & edit profil, upload foto profil, salin nomor rekening.
- **Manajemen Penerima**: Tambah/hapus daftar penerima transfer.
- **Responsive UI**: Tampilan modern, mobile friendly.
- **Cetak Riwayat**: Print riwayat transaksi dengan layout rapi.
- **Salin Nomor Rekening**: 1 klik copy.
- **Upload Foto Profil**: Mendukung JPG, PNG, GIF, max 5MB.

---

## 🔒 Fitur Keamanan
- Password di-hash (bcrypt) saat register/login.
- Session user aman, validasi login di setiap halaman.
- Validasi file upload (tipe & ukuran).
- SQL injection prevention (PDO prepared statement).

---

## 👤 Role User
- **Owner**: Akses penuh, kelola teller & nasabah.
- **Teller**: Kelola transaksi & data nasabah.
- **Nasabah**: Transaksi, cek saldo, riwayat, edit profil.

---

## 🗂️ Struktur Folder
```
├── assets/
│   ├── css/         # Style CSS
│   └── js/          # JS utama
├── config/          # Konfigurasi database
├── image/           # Gambar statis (logo, QR, avatar default, screenshot)
├── includes/        # Komponen PHP (auth, header, footer, navbar)
├── pages/           # Halaman utama (dashboard, profil, riwayat, login, register, dsb)
├── uploads/         # Foto profil user
├── bank_fti.sql     # Struktur & seed database
├── README.md        # Dokumentasi
├── ...
```

---

## 🚀 Cara Instalasi & Menjalankan
1. **Clone/download repo ini** ke folder XAMPP/htdocs atau server lokal Anda.
2. **Import database**:
   - Buka phpMyAdmin, buat database `bank_fti`.
   - Import file `bank_fti.sql`.
3. **Konfigurasi koneksi database** di `config/database.php` jika perlu.
4. **Jalankan XAMPP/Apache** dan akses di browser:
   - `http://localhost/nama_folder_anda/pages/login.php`
5. **Login/daftar** sebagai user baru, owner, atau teller.
   - Saat login, cukup masukkan email atau nomor rekening dan password. Sistem akan otomatis mendeteksi role user (owner/teller/nasabah) tanpa perlu memilih role secara manual.

---

## 📖 Instruksi Penggunaan
- **Top Up/Transfer**: Pilih menu/top up, isi form, submit. Riwayat otomatis tersimpan.
- **Cetak Riwayat**: Buka halaman riwayat, klik tombol "Cetak".
- **Ubah Profil**: Buka profil, klik "Ubah/Tambah Foto" untuk upload foto profil.
- **Salin Nomor Rekening**: Klik ikon copy di samping nomor rekening.
- **Tambah Penerima**: Di menu transfer, tambahkan penerima baru untuk transfer cepat.
- **Registrasi**: Saat daftar, wajib mengisi gender, tanggal lahir (dropdown hari/bulan/tahun), provinsi, dan kota/kabupaten sesuai KTP.

---

## 🛠️ Troubleshooting
- **Tidak bisa upload foto profil**: Pastikan folder `uploads/profile_photos/` writable (CHMOD 755/777).
- **Database error**: Cek koneksi di `config/database.php` dan pastikan database sudah di-import.
- **Tanggal/jam salah**: Pastikan timezone di PHP (`date_default_timezone_set('Asia/Jakarta');`).
- **Fitur tidak muncul**: Coba hard refresh (Ctrl+F5) atau clear cache browser.

---

## 🛣️ Roadmap Pengembangan
- [ ] Notifikasi transaksi (email/WA)
- [ ] Export riwayat ke PDF/Excel
- [ ] Fitur pembayaran tagihan (PLN, PDAM, dsb)
- [ ] Multi-user approval (untuk teller/owner)
- [ ] API mobile app
- [ ] Dark mode

---

## 🤝 Pengembangan & Kontribusi
- Pull request & issue sangat diterima!
- Ikuti struktur folder dan coding style yang ada.
- Untuk fitur baru, tambahkan dokumentasi singkat di README.

---

## Lisensi
MIT License 