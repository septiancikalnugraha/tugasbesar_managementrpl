<?php
// Script untuk memperbaiki data teller dan memastikan routing yang benar
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Update data teller untuk memastikan role dan kategori benar
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
        
        // Verifikasi data teller
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'teller@bank.com'");
        $stmt->execute();
        $teller = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($teller) {
            echo "📋 Data Teller:\n";
            echo "   - Nama: " . $teller['full_name'] . "\n";
            echo "   - Email: " . $teller['email'] . "\n";
            echo "   - Role: " . $teller['role'] . "\n";
            echo "   - Kategori: " . $teller['kategori'] . "\n";
            echo "   - Password: teller123\n";
        }
        
        echo "\n🚀 Sekarang Anda bisa login dengan:\n";
        echo "   Email: teller@bank.com\n";
        echo "   Password: teller123\n";
        echo "\n📍 Setelah login, teller akan otomatis diarahkan ke dashboard petugas.\n";
        
    } else {
        echo "❌ Gagal memperbarui data teller.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 