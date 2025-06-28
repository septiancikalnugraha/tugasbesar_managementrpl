# Update Registrasi - Tambah Jenis Kelamin dan Tanggal Lahir

## Perubahan yang Telah Dilakukan

### 1. Database Schema
- **File**: `bank_fti.sql` - Updated dengan kolom baru
- **File**: `add_gender_birthdate.sql` - Script untuk menambah kolom ke database existing

### 2. Backend Changes
- **File**: `includes/auth.php` - Method `register()` diupdate untuk menerima parameter `gender` dan `birth_date`

### 3. Frontend Changes
- **File**: `pages/register.php` - Form registrasi ditambah field jenis kelamin dan tanggal lahir
- **File**: `pages/dashboard_profil.php` - Halaman profil ditambah display gender dan tanggal lahir
- **File**: `assets/css/style.css` - Styling untuk field baru

## Langkah Implementasi

### Step 1: Update Database
Jalankan script SQL berikut di MySQL/phpMyAdmin:

```sql
-- Script untuk menambahkan kolom gender dan birth_date ke tabel users yang sudah ada
USE bank_fti;

-- Tambahkan kolom gender
ALTER TABLE users ADD COLUMN gender ENUM('Laki-laki', 'Perempuan') NOT NULL DEFAULT 'Laki-laki' AFTER role;

-- Tambahkan kolom birth_date
ALTER TABLE users ADD COLUMN birth_date DATE NOT NULL DEFAULT '1990-01-01' AFTER gender;

-- Update data existing untuk owner dan teller
UPDATE users SET gender = 'Laki-laki', birth_date = '1990-01-01' WHERE role = 'owner';
UPDATE users SET gender = 'Perempuan', birth_date = '1995-01-01' WHERE role = 'teller';
```

### Step 2: Fitur yang Ditambahkan

#### Form Registrasi (`pages/register.php`)
- **Jenis Kelamin**: Radio button dengan pilihan "Laki-laki" dan "Perempuan"
- **Tanggal Lahir**: Tiga dropdown (Tanggal, Bulan, Tahun)
  - Tanggal: 1-31
  - Bulan: Januari-Desember
  - Tahun: 17 tahun ke belakang (minimal usia 17 tahun)

#### Validasi
- Jenis kelamin wajib dipilih
- Tanggal lahir wajib dilengkapi (tanggal, bulan, tahun)
- Password minimal 6 karakter
- Konfirmasi password harus sesuai

#### Halaman Profil (`pages/dashboard_profil.php`)
- Menampilkan jenis kelamin user
- Menampilkan tanggal lahir dalam format "dd Month yyyy" (contoh: "15 Januari 1990")

### Step 3: Styling
- Radio button untuk jenis kelamin dengan hover effect
- Dropdown tanggal lahir dengan 3 kolom responsive
- Responsive design untuk mobile (field akan stack vertikal)

## Testing

1. **Registrasi User Baru**:
   - Buka halaman registrasi
   - Isi semua field termasuk jenis kelamin dan tanggal lahir
   - Pastikan validasi berfungsi
   - Cek data tersimpan di database

2. **Halaman Profil**:
   - Login dengan user yang sudah ada
   - Buka halaman profil
   - Pastikan gender dan tanggal lahir ditampilkan dengan benar

3. **Responsive Design**:
   - Test di mobile device
   - Pastikan field gender dan tanggal lahir responsive

## Catatan Teknis

- **Database**: Menggunakan ENUM untuk gender dan DATE untuk birth_date
- **Validation**: Client-side dan server-side validation
- **Format Date**: Disimpan dalam format YYYY-MM-DD di database, ditampilkan dalam format Indonesia
- **Backward Compatibility**: Script SQL aman untuk database existing

## Troubleshooting

Jika ada error:
1. Pastikan script SQL sudah dijalankan
2. Cek apakah kolom `gender` dan `birth_date` sudah ada di tabel `users`
3. Pastikan semua file sudah diupdate dengan benar
 