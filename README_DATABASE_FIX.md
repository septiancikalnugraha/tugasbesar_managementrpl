# Perbaikan Database Bank FTI

## Masalah yang Ditemukan
1. Error: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'kategori' in 'field list'`
2. Error: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'last_login' in 'field list'`

## Penyebab
Kolom `kategori` dan `last_login` belum ada di tabel `users` di database `bank_fti`.

## Solusi

### Langkah 1: Buat Database (jika belum ada)
```sql
CREATE DATABASE bank_fti;
```

### Langkah 2: Import Struktur Database Lengkap
Jika database masih kosong, gunakan file `bank_fti.sql` yang sudah diperbarui:
```bash
mysql -u root -p bank_fti < bank_fti.sql
```

### Langkah 3: Update Database yang Sudah Ada
Jika database sudah ada tapi belum lengkap, jalankan file `update_database_structure.sql`:
```bash
mysql -u root -p bank_fti < update_database_structure.sql
```

### Langkah 4: Verifikasi
Setelah menjalankan script di atas, kolom `kategori` dan `last_login` akan tersedia di tabel `users`.

## File yang Diperbarui
1. `bank_fti.sql` - Menambahkan kolom `kategori` dan `last_login` ke struktur tabel `users`
2. `update_database_structure.sql` - Script untuk memperbarui database yang sudah ada
3. `add_kategori_column.sql` - Script khusus untuk menambahkan kolom kategori
4. `add_last_login_column.sql` - Script khusus untuk menambahkan kolom last_login

## Struktur Tabel Users yang Diperbarui
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    account_number VARCHAR(25) UNIQUE NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    role VARCHAR(20) NOT NULL DEFAULT 'nasabah',
    gender ENUM('Laki-laki', 'Perempuan') NOT NULL,
    birth_date DATE NOT NULL,
    provinsi VARCHAR(50) DEFAULT NULL,
    kota VARCHAR(50) DEFAULT NULL,
    kategori VARCHAR(20) NOT NULL DEFAULT 'non-prioritas',  -- KOLOM BARU
    profile_photo VARCHAR(255) NULL,
    last_login TIMESTAMP NULL DEFAULT NULL,                 -- KOLOM BARU
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Catatan
- Owner dan Teller akan otomatis memiliki kategori `'prioritas'`
- User baru akan memiliki kategori default `'non-prioritas'`
- Kategori dapat diubah menjadi `'prioritas'` melalui fitur upgrade
- Kolom `last_login` akan otomatis terupdate setiap kali user login
- Kolom `last_login` akan menampilkan waktu login terakhir user 