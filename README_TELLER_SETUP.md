# Setup dan Penggunaan Teller Bank FTI

## 🎯 **Tujuan**
Mengatur user `teller@bank.com` sebagai teller dan mengarahkannya ke dashboard petugas.

## ✅ **Status Setup**
- ✅ Data teller sudah diperbaiki
- ✅ Role sudah diatur sebagai 'teller'
- ✅ Kategori sudah diatur sebagai 'prioritas'
- ✅ Password sudah diupdate dengan hash yang valid

## 🔐 **Kredensial Login Teller**
- **Email:** `teller@bank.com`
- **Password:** `teller123`

## 🚀 **Cara Login sebagai Teller**

### Langkah 1: Jalankan Script Update Password
```bash
mysql -u root -p bank_fti < update_teller_password_final.sql
```

### Langkah 2: Login melalui Web
1. Buka browser dan akses aplikasi
2. Klik "Login"
3. Masukkan kredensial:
   - Email: `teller@bank.com`
   - Password: `teller123`
4. Klik "Login"

### Langkah 3: Otomatis Diarahkan ke Dashboard Petugas
Setelah login berhasil, teller akan otomatis diarahkan ke `dashboard_petugas.php` karena:
- Role = 'teller'
- Routing di `index.php` mendeteksi role teller
- Mengarahkan ke dashboard petugas

## 📋 **Fitur Dashboard Petugas**

### Menu yang Tersedia:
- **Profil** - Lihat dan edit profil teller
- **Dashboard** - Halaman utama petugas
- **Data Nasabah** - Lihat dan kelola data nasabah
- **Transaksi** - Kelola transaksi bank
- **Riwayat** - Lihat riwayat transaksi
- **Pengaturan** - Konfigurasi sistem
- **Logout** - Keluar dari sistem

### Fitur Khusus Teller:
- Akses ke data nasabah
- Proses transaksi nasabah
- Kelola saldo nasabah
- Lihat riwayat transaksi

## 🔧 **Troubleshooting**

### Jika Login Gagal:
1. Pastikan database sudah diupdate dengan script SQL
2. Cek apakah kolom `kategori` dan `last_login` sudah ada
3. Jalankan script `fix_teller_data.php` untuk memperbaiki data

### Jika Tidak Diarahkan ke Dashboard Petugas:
1. Cek apakah role di database = 'teller'
2. Pastikan session menyimpan role dengan benar
3. Cek file `index.php` untuk routing

## 📁 **File yang Terlibat**
- `index.php` - Routing berdasarkan role
- `pages/dashboard_petugas.php` - Dashboard khusus petugas
- `includes/auth.php` - Autentikasi dan session
- `update_teller_password_final.sql` - Update password teller
- `fix_teller_data.php` - Perbaikan data teller

## 🎉 **Hasil Akhir**
Teller akan memiliki akses penuh ke dashboard petugas dengan fitur-fitur administratif untuk mengelola nasabah dan transaksi bank. 