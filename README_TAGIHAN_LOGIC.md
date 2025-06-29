# 📋 Logika Tagihan dengan Pengecekan Saldo dan Pengelolaan Teller

## 🔄 **Flow Transaksi Lengkap:**

### **1. Status Tagihan:**
- **Draft** → **Belum Lunas** (Order) → **Lunas** (Bayar)

### **2. Logika Order (PATCH):**
```
User Order Tagihan:
├── Cek saldo user
├── Jika saldo CUKUP:
│   ├── Kurangi saldo user
│   ├── Update status → "Belum Lunas"
│   └── Tampilkan pesan sukses
└── Jika saldo KURANG:
    └── Tampilkan pesan error
```

### **3. Logika Bayar (PUT):**
```
User Bayar Tagihan:
├── Ambil saldo teller
├── Tambahkan nominal ke saldo teller
├── Update status → "Lunas"
└── Tampilkan pesan sukses
```

## 💰 **Pengelolaan Saldo:**

### **User (Nasabah):**
- **Order**: Saldo berkurang sesuai nominal tagihan
- **Bayar**: Saldo tidak berubah (tetap berkurang dari order)

### **Teller:**
- **Order**: Saldo tidak berubah
- **Bayar**: Saldo bertambah sesuai nominal tagihan

## 📁 **File yang Diperbarui:**

### **1. `upload_upgrade_request.php`**
```php
// PATCH (Order) - Kurangi saldo user
if ($saldo < $nominal) {
    throw new Exception('Saldo tidak cukup...');
}
$saldoBaru = $saldo - $nominal;
UPDATE users SET balance = :balance WHERE id = :user_id

// PUT (Bayar) - Tambah saldo teller
$saldoTellerBaru = $saldoTeller + $nominal;
UPDATE users SET balance = :balance WHERE role = "teller"
```

### **2. `pages/dashboard_transaksi.php`**
- ✅ Tampilan saldo user real-time
- ✅ Pesan sukses dengan detail saldo
- ✅ Auto-refresh setelah transaksi

### **3. `get_user_saldo.php`** (Baru)
- ✅ API untuk mengambil saldo user

### **4. `view_teller_saldo.php`** (Baru)
- ✅ Monitoring saldo teller
- ✅ Form tambah saldo teller
- ✅ Riwayat pendapatan teller

### **5. `add_saldo_test.php`** (Baru)
- ✅ Script untuk menambah saldo test user

## 🧪 **Cara Testing:**

### **1. Setup Awal:**
```bash
# Perbaiki data tagihan
http://localhost/tugasbesar_managementrpl/fix_tagihan_status.php

# Tambah saldo user test
http://localhost/tugasbesar_managementrpl/add_saldo_test.php

# Lihat saldo teller
http://localhost/tugasbesar_managementrpl/view_teller_saldo.php
```

### **2. Test Flow:**
1. **Buat tagihan** → Status: Draft
2. **Order tagihan** → Saldo user berkurang, Status: Belum Lunas
3. **Bayar tagihan** → Saldo teller bertambah, Status: Lunas

### **3. Monitoring:**
- **User**: Lihat saldo di dashboard transaksi
- **Teller**: Lihat saldo dan pendapatan di `view_teller_saldo.php`

## 🔒 **Keamanan Data:**

### **Database Transaction:**
- ✅ Semua operasi saldo menggunakan transaction
- ✅ Rollback otomatis jika ada error
- ✅ Konsistensi data terjamin

### **Validasi:**
- ✅ Cek saldo sebelum order
- ✅ Validasi status tagihan
- ✅ Validasi user/teller exists

## 📊 **Pesan Informasi:**

### **Order Berhasil:**
```
Order berhasil! Saldo berkurang Rp 50.000
Saldo sebelum: Rp 1.000.000
Saldo setelah: Rp 950.000
```

### **Order Gagal (Saldo Kurang):**
```
Saldo tidak cukup. Saldo: Rp 10.000, Dibutuhkan: Rp 50.000
```

### **Bayar Berhasil:**
```
Pembayaran berhasil! Saldo ditambahkan ke rekening teller Rp 50.000
Saldo teller sebelum: Rp 500.000
Saldo teller setelah: Rp 550.000
```

## 🎯 **Fitur Utama:**

1. **Pengecekan Saldo Real-time**
2. **Pengelolaan Saldo Teller**
3. **Database Transaction**
4. **Monitoring Pendapatan**
5. **Pesan Informasi Detail**
6. **Auto-refresh Data**

## 🔧 **Troubleshooting:**

### **Jika saldo tidak muncul:**
1. Jalankan `fix_tagihan_status.php`
2. Cek koneksi database
3. Pastikan user sudah login

### **Jika order gagal:**
1. Cek saldo user di `add_saldo_test.php`
2. Pastikan tagihan status "Draft"
3. Cek log error di browser console

### **Jika bayar gagal:**
1. Cek data teller di database
2. Pastikan tagihan status "Belum Lunas"
3. Cek log error di browser console

---

**Sistem tagihan sekarang sudah berfungsi dengan logika yang benar dan aman!** 🎉 