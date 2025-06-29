<?php
require_once 'config/database.php';
session_start();

echo "<h2>Reset Saldo Teller</h2>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Ambil data teller sebelum reset
    $stmt = $pdo->prepare('SELECT id, full_name, email, balance, role FROM users WHERE role = "teller"');
    $stmt->execute();
    $tellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($tellers) > 0) {
        echo "<div style='margin-bottom: 2rem;'>";
        echo "<h3>Data Teller Sebelum Reset:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 1rem;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Saldo Sebelum</th>";
        echo "</tr>";
        
        foreach ($tellers as $teller) {
            echo "<tr>";
            echo "<td>{$teller['id']}</td>";
            echo "<td>{$teller['full_name']}</td>";
            echo "<td>{$teller['email']}</td>";
            echo "<td>{$teller['role']}</td>";
            echo "<td style='font-weight: bold; color: #1976d2;'>Rp " . number_format($teller['balance'], 0, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        
        // Reset saldo teller menjadi 0
        $stmt = $pdo->prepare('UPDATE users SET balance = 0 WHERE role = "teller"');
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        
        echo "<p style='color: green; font-weight: bold;'>✅ Berhasil mereset saldo {$affectedRows} teller menjadi Rp 0</p>";
        
        // Tampilkan data setelah reset
        echo "<div style='margin-top: 2rem;'>";
        echo "<h3>Data Teller Setelah Reset:</h3>";
        
        $stmt = $pdo->prepare('SELECT id, full_name, email, balance, role FROM users WHERE role = "teller"');
        $stmt->execute();
        $tellers_after = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 1rem;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Saldo Setelah</th>";
        echo "</tr>";
        
        foreach ($tellers_after as $teller) {
            echo "<tr>";
            echo "<td>{$teller['id']}</td>";
            echo "<td>{$teller['full_name']}</td>";
            echo "<td>{$teller['email']}</td>";
            echo "<td>{$teller['role']}</td>";
            echo "<td style='font-weight: bold; color: #e53e3e;'>Rp " . number_format($teller['balance'], 0, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        
        // Tampilkan riwayat tagihan yang sudah lunas (pendapatan yang hilang)
        echo "<div style='margin-top: 2rem;'>";
        echo "<h3>Riwayat Tagihan Lunas (Pendapatan yang Hilang):</h3>";
        
        $stmt = $pdo->prepare('SELECT t.*, u.full_name as nama_user FROM tagihan t JOIN users u ON t.user_id = u.id WHERE t.status = "Lunas" ORDER BY t.waktu DESC');
        $stmt->execute();
        $tagihanLunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($tagihanLunas) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th>ID</th><th>User</th><th>Jenis</th><th>Keterangan</th><th>Nominal</th><th>Waktu Lunas</th>";
            echo "</tr>";
            
            $totalPendapatan = 0;
            foreach ($tagihanLunas as $tagihan) {
                $totalPendapatan += $tagihan['nominal'];
                echo "<tr>";
                echo "<td>{$tagihan['id']}</td>";
                echo "<td>{$tagihan['nama_user']}</td>";
                echo "<td>{$tagihan['jenis']}</td>";
                echo "<td>{$tagihan['keterangan']}</td>";
                echo "<td style='font-weight: bold; color: #2f855a;'>Rp " . number_format($tagihan['nominal'], 0, ',', '.') . "</td>";
                echo "<td>{$tagihan['waktu']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p style='margin-top: 1rem; font-weight: bold; color: #e53e3e;'>Total Pendapatan yang Hilang: Rp " . number_format($totalPendapatan, 0, ',', '.') . "</p>";
        } else {
            echo "<p style='color: #888;'>Belum ada tagihan yang lunas</p>";
        }
        echo "</div>";
        
    } else {
        echo "<p style='color: orange;'>Belum ada data teller</p>";
    }
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>✅ Reset saldo teller selesai!</p>";
    echo "<p><a href='view_teller_saldo.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Lihat Saldo Teller</a></p>";
    echo "<p><a href='pages/dashboard_transaksi.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 