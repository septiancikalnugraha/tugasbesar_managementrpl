<?php
// Script untuk debug dan memperbaiki login teller
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "🔍 Debug Data Teller:\n";
    echo "=====================\n";
    
    // Cek data teller di database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'teller@bank.com'");
    $stmt->execute();
    $teller = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($teller) {
        echo "✅ Teller ditemukan di database:\n";
        echo "   - ID: " . $teller['id'] . "\n";
        echo "   - Nama: " . $teller['full_name'] . "\n";
        echo "   - Email: " . $teller['email'] . "\n";
        echo "   - Role: " . $teller['role'] . "\n";
        echo "   - Kategori: " . $teller['kategori'] . "\n";
        echo "   - Password Hash: " . substr($teller['password'], 0, 20) . "...\n";
    } else {
        echo "❌ Teller tidak ditemukan di database!\n";
    }
    
    echo "\n🔧 Memperbaiki Data Teller...\n";
    echo "==============================\n";
    
    // Update data teller dengan role yang benar
    $stmt = $pdo->prepare("UPDATE users SET 
        role = 'teller',
        kategori = 'prioritas',
        gender = 'Perempuan',
        birth_date = '1995-01-01',
        provinsi = 'Jawa Barat',
        kota = 'Bandung'
        WHERE email = 'teller@bank.com'");
    
    $result = $stmt->execute();
    
    if ($result) {
        echo "✅ Data teller berhasil diperbarui!\n";
        
        // Verifikasi setelah update
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'teller@bank.com'");
        $stmt->execute();
        $teller_updated = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($teller_updated) {
            echo "\n📋 Data Teller Setelah Update:\n";
            echo "   - ID: " . $teller_updated['id'] . "\n";
            echo "   - Nama: " . $teller_updated['full_name'] . "\n";
            echo "   - Email: " . $teller_updated['email'] . "\n";
            echo "   - Role: " . $teller_updated['role'] . "\n";
            echo "   - Kategori: " . $teller_updated['kategori'] . "\n";
        }
        
        echo "\n🚀 Sekarang coba login dengan:\n";
        echo "   Email: teller@bank.com\n";
        echo "   Password: teller123\n";
        echo "\n📍 Seharusnya akan diarahkan ke dashboard petugas.\n";
        
    } else {
        echo "❌ Gagal memperbarui data teller.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 