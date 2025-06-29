# 🏦 Bank FTI - Sistem Manajemen Transaksi Digital

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

Aplikasi web perbankan digital modern untuk manajemen transaksi bank (top up, transfer, tagihan, riwayat, profil) berbasis PHP dan MySQL dengan sistem role-based access control.

---

## 📸 Screenshot Aplikasi

| Dashboard Nasabah | Dashboard Petugas | Halaman Transaksi | Halaman Profil |
|-------------------|-------------------|-------------------|----------------|
| ![Dashboard](image/ss_dashboard.png) | ![Petugas](image/ss_petugas.png) | ![Transaksi](image/ss_transaksi.png) | ![Profil](image/ss_profil.png) |

---

## ✨ Fitur Utama

### 🔐 **Sistem Autentikasi & Keamanan**
- **Login Multi-Role**: Otomatis deteksi role (Owner/Teller/Nasabah) tanpa pilih manual
- **Registrasi Lengkap**: Gender, tanggal lahir, provinsi, kota/kabupaten sesuai KTP
- **Password Hashing**: Menggunakan bcrypt untuk keamanan maksimal
- **Session Management**: Validasi login di setiap halaman
- **File Upload Security**: Validasi tipe & ukuran file (JPG, PNG, GIF, max 5MB)

### 💰 **Manajemen Transaksi**
- **Top Up E-Wallet**: DANA, OVO, GoPay, ShopeePay, LinkAja
- **Transfer Antar Rekening**: Real-time dengan validasi saldo
- **Sistem Tagihan**: PLN, BPJS, Pulsa, Data, Game Voucher
- **Riwayat Transaksi**: Filter tab, cetak/print dengan layout rapi
- **Daftar Penerima**: Tambah/hapus penerima transfer untuk akses cepat

### 👥 **Sistem Role-Based Access Control**

#### **Owner** 👑
- Akses penuh ke semua fitur
- Kelola teller & nasabah
- Monitoring sistem secara keseluruhan
- Dashboard khusus dengan statistik lengkap

#### **Teller** 🏦
- Kelola transaksi nasabah
- Proses pembayaran tagihan
- Monitoring saldo dan pendapatan
- Dashboard petugas dengan fitur khusus

#### **Nasabah** 👤
- Transaksi top up & transfer
- Cek saldo real-time
- Riwayat transaksi lengkap
- Edit profil & upload foto
- Manajemen daftar penerima

### 📱 **User Experience**
- **Responsive Design**: Mobile-first approach, works on all devices
- **Modern UI/UX**: Material Design inspired interface
- **Real-time Updates**: Saldo dan data update otomatis
- **Copy to Clipboard**: 1 klik copy nomor rekening
- **Print Functionality**: Cetak riwayat dengan format rapi

---

## 🗂️ Struktur Project

```
tugasbesar_managementrpl/
├── 📁 assets/
│   ├── 📁 css/
│   │   └── style.css              # Main stylesheet
│   └── 📁 js/
│       ├── html5-qrcode.min.js    # QR Code scanner
│       └── main.js                # Main JavaScript
├── 📁 config/
│   └── database.php               # Database configuration
├── 📁 includes/
│   ├── auth.php                   # Authentication functions
│   ├── footer.php                 # Footer component
│   ├── header.php                 # Header component
│   └── navbar.php                 # Navigation component
├── 📁 pages/
│   ├── dashboard.php              # Main dashboard (nasabah)
│   ├── dashboard_petugas.php      # Staff dashboard
│   ├── dashboard_transaksi.php    # Transaction page
│   ├── dashboard_history.php      # Transaction history
│   ├── dashboard_profil.php       # User profile
│   ├── login.php                  # Login page
│   ├── register.php               # Registration page
│   └── logout.php                 # Logout handler
├── 📁 uploads/
│   └── 📁 profile_photos/         # User profile photos
├── 📁 image/                      # Static images
├── 📁 Bukti History/              # Transaction receipts
├── bank_fti.sql                   # Database structure
├── index.php                      # Landing page
├── topup.php                      # Top up API
├── transfer.php                   # Transfer API
├── save_qris_history.php          # QRIS transaction handler
├── get_history.php                # History API
├── get_receivers.php              # Receivers API
├── get_user_saldo.php             # Balance API
├── upload_profile_photo.php       # Profile photo upload
├── upload_upgrade_request.php     # Tagihan system
├── add_receiver.php               # Add receiver API
├── delete_receiver.php            # Delete receiver API
├── view_teller_saldo.php          # Teller balance monitoring
├── reset_teller_saldo.php         # Reset teller balance
├── add_saldo_test.php             # Add test balance
├── fix_tagihan_status.php         # Fix tagihan status
├── check_session.php              # Session validation
├── test_connection.php            # Database connection test
└── README.md                      # This file
```

---

## 🗂️ Struktur Database

### **Tabel Utama**
```sql
users                    # Data pengguna (owner, teller, nasabah)
├── id, full_name, email, phone, password
├── account_number, balance, role
├── gender, birth_date, provinsi, kota
└── profile_photo, created_at, updated_at

topup_history           # Riwayat top up e-wallet
├── user_id, ewallet, rekening, nominal
└── tanggal, review, rating

transfer_history        # Riwayat transfer antar rekening
├── from_user, to_user, receiver_id, amount
├── note, transfer_date, rating, review
└── created_at

receivers               # Daftar penerima transfer
├── user_id, name, account_number
└── created_at

tagihan                 # Sistem tagihan/pembayaran
├── user_id, jenis, keterangan, nominal
├── status (Draft/Belum Lunas/Lunas)
└── waktu
```

---

## 🔌 API Endpoints

### **Authentication**
- `POST /pages/login.php` - User login
- `POST /pages/register.php` - User registration
- `GET /pages/logout.php` - User logout

### **Transaction APIs**
- `POST /topup.php` - Top up e-wallet
- `POST /transfer.php` - Transfer money
- `POST /save_qris_history.php` - Save QRIS transaction
- `GET /get_history.php` - Get transaction history
- `GET /get_user_saldo.php` - Get user balance

### **Profile Management**
- `POST /upload_profile_photo.php` - Upload profile photo
- `POST /add_receiver.php` - Add transfer receiver
- `DELETE /delete_receiver.php` - Delete transfer receiver
- `GET /get_receivers.php` - Get user receivers

### **Tagihan System**
- `POST /upload_upgrade_request.php` - Create/pay tagihan
- `GET /view_teller_saldo.php` - View teller balance
- `POST /reset_teller_saldo.php` - Reset teller balance

### **Utility APIs**
- `GET /check_session.php` - Check user session
- `GET /test_connection.php` - Test database connection
- `GET /add_saldo_test.php` - Add test balance
- `GET /fix_tagihan_status.php` - Fix tagihan status

---

## 🚀 Cara Instalasi & Setup

### **Prerequisites**
- XAMPP/WAMP/LAMP Server
- PHP 7.4+ dengan ekstensi PDO MySQL
- MySQL 5.7+ atau MariaDB 10.2+
- Web browser modern

### **Step-by-Step Installation**

1. **Clone/Download Repository**
   ```bash
   # Clone ke folder htdocs XAMPP
   cd C:/xampp/htdocs/
   git clone https://github.com/yourusername/bank-fti.git
   # atau download ZIP dan extract
   ```

2. **Setup Database**
   ```bash
   # Buka phpMyAdmin: http://localhost/phpmyadmin
   # Buat database baru: bank_fti
   # Import file: bank_fti.sql
   ```

3. **Konfigurasi Database**
   ```php
   // Edit file: config/database.php
   $host = 'localhost';
   $dbname = 'bank_fti';
   $username = 'root';
   $password = ''; // sesuaikan dengan password MySQL Anda
   ```

4. **Setup Folder Permissions**
   ```bash
   # Pastikan folder uploads/ writable
   chmod 755 uploads/
   chmod 755 uploads/profile_photos/
   ```

5. **Jalankan Aplikasi**
   ```bash
   # Start XAMPP Apache & MySQL
   # Akses di browser:
   http://localhost/bank-fti/
   ```

### **Default Login Credentials**
```
Owner:
- Email: owner@bank.com
- Password: owner123

Teller:
- Email: teller@bank.com  
- Password: teller123

Nasabah:
- Register baru di halaman register
```

---

## 📖 Panduan Penggunaan

### **Untuk Nasabah**

#### **Registrasi Akun Baru**
1. Klik "Daftar" di halaman utama
2. Isi semua field wajib (termasuk gender & tanggal lahir)
3. Upload foto profil (opsional)
4. Klik "Daftar" untuk menyelesaikan

#### **Top Up E-Wallet**
1. Login ke dashboard
2. Pilih menu "Top Up"
3. Pilih e-wallet (DANA, OVO, dll)
4. Masukkan nomor rekening & nominal
5. Klik "Top Up" untuk konfirmasi

#### **Transfer Antar Rekening**
1. Pilih menu "Transfer"
2. Pilih penerima dari daftar atau tambah baru
3. Masukkan nominal & catatan
4. Klik "Transfer" untuk konfirmasi

#### **Sistem Tagihan**
1. Pilih menu "Tagihan"
2. Buat tagihan baru (PLN, BPJS, dll)
3. **Order**: Saldo berkurang, status jadi "Belum Lunas"
4. **Bayar**: Teller proses, status jadi "Lunas"

### **Untuk Teller**

#### **Dashboard Petugas**
1. Login dengan akun teller
2. Akses dashboard khusus petugas
3. Monitor transaksi nasabah
4. Proses pembayaran tagihan

#### **Proses Tagihan**
1. Lihat daftar tagihan "Belum Lunas"
2. Klik "Bayar" untuk proses pembayaran
3. Saldo teller otomatis bertambah
4. Status tagihan berubah jadi "Lunas"

### **Untuk Owner**

#### **Dashboard Owner**
1. Login dengan akun owner
2. Akses dashboard dengan statistik lengkap
3. Monitor semua transaksi sistem
4. Kelola user (teller & nasabah)

---

## 🔧 Fitur Teknis

### **Sistem Tagihan dengan Logika Saldo**
```
Flow Transaksi Tagihan:
1. Draft → User buat tagihan
2. Belum Lunas → User order (saldo berkurang)
3. Lunas → Teller bayar (saldo teller bertambah)
```

### **Keamanan Data**
- **SQL Injection Prevention**: Menggunakan PDO prepared statements
- **XSS Protection**: Input sanitization & output escaping
- **CSRF Protection**: Session-based token validation
- **File Upload Security**: Validasi tipe, ukuran, & scan virus

### **Performance Optimization**
- **Database Indexing**: Optimized queries dengan proper indexing
- **Caching**: Session-based caching untuk data user
- **Minified Assets**: CSS & JS minified untuk load time optimal

---

## 📋 Recent Updates

### **v2.1.0 - Tagihan System Enhancement**
- ✅ Implementasi sistem tagihan dengan logika saldo yang benar
- ✅ Pengecekan saldo real-time sebelum order tagihan
- ✅ Pengelolaan saldo teller otomatis
- ✅ Database transaction untuk konsistensi data
- ✅ Monitoring pendapatan teller

### **v2.0.0 - Registration Update**
- ✅ Tambah field gender dan tanggal lahir
- ✅ Validasi usia minimal 17 tahun
- ✅ Dropdown provinsi dan kota/kabupaten
- ✅ Backward compatibility dengan database existing

### **v1.5.0 - UI/UX Improvements**
- ✅ Responsive design untuk mobile
- ✅ Modern Material Design interface
- ✅ Real-time saldo updates
- ✅ Copy to clipboard functionality
- ✅ Print transaction history

---

## 🛠️ Troubleshooting

### **Masalah Umum**

#### **Database Connection Error**
```bash
# Cek file config/database.php
# Pastikan credentials benar
# Test koneksi: http://localhost/bank-fti/test_connection.php
```

#### **Upload Foto Gagal**
```bash
# Cek folder permissions
chmod 755 uploads/
chmod 755 uploads/profile_photos/

# Cek PHP upload settings di php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

#### **Saldo Tidak Muncul**
```bash
# Jalankan script perbaikan
http://localhost/bank-fti/fix_tagihan_status.php

# Tambah saldo test
http://localhost/bank-fti/add_saldo_test.php
```

#### **Session Error**
```bash
# Clear browser cache & cookies
# Restart Apache server
# Cek PHP session settings
```

### **Debug Tools**
- `debug_api.txt` - Log API calls
- `debug_session.txt` - Session debugging
- `debug_tagihan.txt` - Tagihan system logs
- `debug_topup.txt` - Top up transaction logs

---

## 🛣️ Roadmap Pengembangan

### **Fase 1 - Core Features** ✅
- [x] Sistem autentikasi multi-role
- [x] Top up e-wallet
- [x] Transfer antar rekening
- [x] Sistem tagihan dengan logika saldo
- [x] Riwayat transaksi & cetak
- [x] Upload foto profil

### **Fase 2 - Advanced Features** 🚧
- [ ] Notifikasi transaksi (email/SMS)
- [ ] Export riwayat ke PDF/Excel
- [ ] QR Code payment integration
- [ ] API untuk mobile app
- [ ] Multi-currency support

### **Fase 3 - Enterprise Features** 📋
- [ ] Multi-user approval system
- [ ] Advanced reporting & analytics
- [ ] Integration dengan payment gateway
- [ ] Blockchain-based transaction
- [ ] AI-powered fraud detection

### **Fase 4 - Modern UI/UX** 🎨
- [ ] Dark mode theme
- [ ] Progressive Web App (PWA)
- [ ] Real-time notifications
- [ ] Advanced dashboard widgets
- [ ] Mobile app (React Native/Flutter)

---

## 🤝 Kontribusi & Pengembangan

### **Cara Berkontribusi**
1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

### **Coding Standards**
- **PHP**: PSR-12 coding standards
- **HTML**: Semantic HTML5
- **CSS**: BEM methodology
- **JavaScript**: ES6+ with proper error handling
- **Database**: Proper indexing & normalized structure

### **Testing Guidelines**
- Test di multiple browsers (Chrome, Firefox, Safari, Edge)
- Test responsive design di berbagai device
- Test semua role user (Owner, Teller, Nasabah)
- Test edge cases & error scenarios

---

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

---

## 📞 Kontak & Support

- **Developer**: Tim Bank FTI
- **Email**: support@bankfti.com
- **Website**: https://bankfti.com
- **Documentation**: https://docs.bankfti.com

---

## 🙏 Acknowledgments

- **Icons**: Font Awesome
- **UI Framework**: Custom CSS with Material Design inspiration
- **Database**: MySQL with PDO
- **Server**: Apache with PHP

---

**⭐ Jika project ini membantu Anda, jangan lupa berikan star!**

*Bank FTI - Solusi Perbankan Digital Modern & Aman* 🏦✨ 