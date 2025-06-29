<?php
require_once 'config/database.php';
session_start();

echo "<h2>Informasi Saldo Teller</h2>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Ambil data teller
    $stmt = $pdo->prepare('SELECT id, full_name, email, balance, role FROM users WHERE role = "teller"');
    $stmt->execute();
    $tellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($tellers) > 0) {
        echo "<div style='margin-bottom: 2rem;'>";
        echo "<h3>Data Teller:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 1rem;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Saldo</th>";
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
        
        // Form untuk menambah saldo teller
        echo "<div style='background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;'>";
        echo "<h3>Tambah Saldo Teller:</h3>";
        echo "<form method='POST' style='display: flex; gap: 1rem; align-items: center;'>";
        echo "<input type='number' name='tambah_saldo' placeholder='Jumlah saldo' min='1000' step='1000' required style='padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;'>";
        echo "<button type='submit' style='background: #1976d2; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;'>Tambah Saldo</button>";
        echo "</form>";
        echo "</div>";
        
        // Proses tambah saldo jika form disubmit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_saldo'])) {
            $tambahSaldo = intval($_POST['tambah_saldo']);
            if ($tambahSaldo > 0) {
                $stmt = $pdo->prepare('UPDATE users SET balance = balance + :saldo WHERE role = "teller"');
                $stmt->execute([':saldo' => $tambahSaldo]);
                
                echo "<p style='color: green; font-weight: bold;'>✅ Berhasil menambahkan saldo Rp " . number_format($tambahSaldo, 0, ',', '.') . " ke rekening teller!</p>";
                
                // Refresh halaman untuk menampilkan saldo terbaru
                echo "<script>setTimeout(function(){ window.location.reload(); }, 1500);</script>";
            }
        }
        
    } else {
        echo "<p style='color: orange;'>Belum ada data teller</p>";
    }
    
    // Tampilkan riwayat tagihan yang sudah lunas
    echo "<div style='margin-top: 2rem;'>";
    echo "<h3>Riwayat Tagihan Lunas (Pendapatan Teller):</h3>";
    
    $stmt = $pdo->prepare('SELECT t.*, u.full_name as nama_user FROM tagihan t JOIN users u ON t.user_id = u.id WHERE t.status = "Lunas" ORDER BY t.waktu DESC LIMIT 10');
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
        echo "<p style='margin-top: 1rem; font-weight: bold; color: #2f855a;'>Total Pendapatan: Rp " . number_format($totalPendapatan, 0, ',', '.') . "</p>";
    } else {
        echo "<p style='color: #888;'>Belum ada tagihan yang lunas</p>";
    }
    echo "</div>";
    
    echo "<hr>";
    echo "<p><a href='pages/dashboard_transaksi.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Dashboard Transaksi</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 