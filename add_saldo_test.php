<?php
require_once 'config/database.php';
session_start();

echo "<h2>Menambahkan Saldo Test</h2>";

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ Silakan login terlebih dahulu</p>";
    echo "<p><a href='pages/login.php'>Login</a></p>";
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Ambil saldo saat ini
    $stmt = $pdo->prepare('SELECT balance, full_name FROM users WHERE id = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "<p style='color: red;'>❌ User tidak ditemukan</p>";
        exit;
    }
    
    $saldoSekarang = floatval($user['balance']);
    $namaUser = $user['full_name'];
    
    echo "<p><strong>User:</strong> {$namaUser}</p>";
    echo "<p><strong>Saldo saat ini:</strong> Rp " . number_format($saldoSekarang, 0, ',', '.') . "</p>";
    
    // Tambah saldo 1 juta
    $tambahSaldo = 1000000;
    $saldoBaru = $saldoSekarang + $tambahSaldo;
    
    $stmt = $pdo->prepare('UPDATE users SET balance = :balance WHERE id = :user_id');
    $stmt->execute([':balance' => $saldoBaru, ':user_id' => $user_id]);
    
    echo "<p style='color: green;'>✅ Berhasil menambahkan saldo Rp " . number_format($tambahSaldo, 0, ',', '.') . "</p>";
    echo "<p><strong>Saldo baru:</strong> Rp " . number_format($saldoBaru, 0, ',', '.') . "</p>";
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>✅ Sekarang Anda bisa testing fitur order dengan pengecekan saldo!</p>";
    echo "<p><a href='pages/dashboard_transaksi.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Dashboard Transaksi</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 