# Upgrade Prioritas - Bank FTI

## Perubahan Logika Upgrade Prioritas

### Sebelumnya
- Transfer ke rekening teller (FTI00000002) sebesar **Rp 50.000** untuk upgrade ke prioritas

### Sekarang  
- Transfer ke rekening teller (+62 30100000002) sebesar **Rp 25.000** untuk upgrade ke prioritas

## File yang Diperbarui

### 1. `transfer.php`
- **Baris 85-90**: Mengubah kondisi dari `$amount == 50000` menjadi `$amount == 25000`
- **Baris 102**: Mengubah rekening dari `FTI00000002` menjadi `+62 30100000002`
- Menambahkan logging untuk tracking upgrade prioritas

### 2. `topup.php` 
- **Baris 66-67**: Mengubah kondisi dari `$amount == 50000` menjadi `$amount == 25000`
- **Baris 67**: Mengubah rekening dari `FTI00000002` menjadi `+62 30100000002`
- Menambahkan logging untuk tracking upgrade prioritas

### 3. `pages/dashboard.php`
- **Baris 1164**: Mengubah nilai default dari `50000` menjadi `25000` pada form transfer
- **Baris 852**: Mengubah rekening dari `FTI00000002` menjadi `+62 30100000002`
- **Baris 1158**: Mengubah rekening dari `FTI00000002` menjadi `+62 30100000002`

### 4. `insert_owner_teller.php`
- **Baris 20**: Mengubah rekening teller dari `FTI00000002` menjadi `+62 30100000002`

## Cara Kerja

### Via Transfer
1. User memilih transfer ke rekening **+62 30100000002** (teller)
2. Masukkan nominal **Rp 25.000**
3. Sistem otomatis mengubah kategori user menjadi "prioritas"
4. Saldo user berkurang Rp 25.000
5. Saldo teller bertambah Rp 25.000

### Via Top Up
1. User memilih e-wallet/rekening **+62 30100000002** 
2. Masukkan nominal **Rp 25.000**
3. Sistem otomatis mengubah kategori user menjadi "prioritas"
4. Saldo user berkurang Rp 25.000
5. Saldo teller bertambah Rp 25.000

## Logging

Sistem akan mencatat log setiap kali ada upgrade prioritas:
```
User ID: [user_id] upgraded to prioritas via transfer/topup of 25000 to +62 30100000002
```

## Testing

Untuk testing, pastikan:
1. User memiliki saldo minimal Rp 25.000
2. Transfer/topup ke rekening +62 30100000002 dengan nominal tepat Rp 25.000
3. Cek database bahwa field `kategori` berubah dari `null` atau `non-prioritas` menjadi `prioritas`
4. Cek bahwa saldo user berkurang dan saldo teller bertambah

## Catatan

- Hanya transfer/topup dengan nominal **tepat Rp 25.000** yang akan memicu upgrade
- Rekening tujuan harus **+62 30100000002** (teller)
- Upgrade bersifat otomatis dan tidak dapat dibatalkan
- User yang sudah prioritas tidak akan terpengaruh jika melakukan transfer lagi 