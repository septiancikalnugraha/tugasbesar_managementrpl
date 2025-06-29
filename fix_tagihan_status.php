<?php
require_once 'config/database.php';

echo "<h2>Memperbaiki Status Tagihan</h2>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // 1. Perbaiki struktur tabel tagihan
    echo "<p>1. Memperbaiki struktur tabel tagihan...</p>";
    
    // Pastikan kolom status sudah benar
    $stmt = $pdo->prepare("ALTER TABLE tagihan MODIFY status ENUM('Draft','Belum Lunas','Lunas') DEFAULT 'Draft'");
    $stmt->execute();
    echo "<p style='color: green;'>✓ Struktur tabel tagihan berhasil diperbaiki</p>";
    
    // 2. Update data yang status-nya kosong atau NULL
    echo "<p>2. Memperbaiki data tagihan yang status-nya kosong...</p>";
    
    $stmt = $pdo->prepare("UPDATE tagihan SET status = 'Draft' WHERE status IS NULL OR status = '' OR status = 'null'");
    $stmt->execute();
    $affectedRows = $stmt->rowCount();
    
    echo "<p style='color: green;'>✓ Berhasil memperbaiki {$affectedRows} data tagihan</p>";
    
    // 3. Tampilkan data tagihan setelah perbaikan
    echo "<p>3. Data tagihan setelah perbaikan:</p>";
    
    $stmt = $pdo->prepare("SELECT * FROM tagihan ORDER BY waktu DESC LIMIT 10");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rows) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>User ID</th><th>Jenis</th><th>Keterangan</th><th>Nominal</th><th>Status</th><th>Waktu</th>";
        echo "</tr>";
        
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['user_id']}</td>";
            echo "<td>{$row['jenis']}</td>";
            echo "<td>{$row['keterangan']}</td>";
            echo "<td>Rp " . number_format($row['nominal'], 0, ',', '.') . "</td>";
            echo "<td style='background: " . ($row['status'] == 'Draft' ? '#f6ad55' : ($row['status'] == 'Belum Lunas' ? '#3182ce' : '#38a169')) . "; color: white; padding: 2px 8px; border-radius: 4px;'>{$row['status']}</td>";
            echo "<td>{$row['waktu']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>Belum ada data tagihan</p>";
    }
    
    // 4. Test query untuk memastikan data bisa diambil dengan benar
    echo "<p>4. Testing pengambilan data tagihan...</p>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tagihan");
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as draft FROM tagihan WHERE status = 'Draft'");
    $stmt->execute();
    $draft = $stmt->fetch(PDO::FETCH_ASSOC)['draft'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as belum_lunas FROM tagihan WHERE status = 'Belum Lunas'");
    $stmt->execute();
    $belumLunas = $stmt->fetch(PDO::FETCH_ASSOC)['belum_lunas'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as lunas FROM tagihan WHERE status = 'Lunas'");
    $stmt->execute();
    $lunas = $stmt->fetch(PDO::FETCH_ASSOC)['lunas'];
    
    echo "<p style='color: green;'>✓ Total tagihan: {$total}</p>";
    echo "<p style='color: blue;'>✓ Draft: {$draft}</p>";
    echo "<p style='color: orange;'>✓ Belum Lunas: {$belum_lunas}</p>";
    echo "<p style='color: green;'>✓ Lunas: {$lunas}</p>";
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>✅ Perbaikan selesai! Sekarang coba refresh halaman dashboard transaksi.</p>";
    echo "<p><a href='pages/dashboard_transaksi.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Dashboard Transaksi</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 