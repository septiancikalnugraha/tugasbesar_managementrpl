<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (!in_array($_SESSION['role'], ['owner', 'teller', 'admin'])) {
    // Redirect ke dashboard sesuai role
    if ($_SESSION['role'] === 'nasabah') {
        header('Location: dashboard.php');
    } else {
        header('Location: login.php');
    }
    exit;
}

$name = $_SESSION['user_name'];
$role = ucfirst($_SESSION['role']);
$user_data = $_SESSION['user_data'] ?? [];
$gender = $user_data['gender'] ?? ($_SESSION['gender'] ?? '');

require_once '../config/database.php';
$db = new Database();
$pdo = $db->getConnection();
$total_nasabah = 0;
$total_transaksi = 0;
$total_saldo = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'nasabah'");
    $total_nasabah = $stmt->fetchColumn();
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM topup_history");
    $count_topup = $stmt1->fetchColumn();
    $stmt2 = $pdo->query("SELECT COUNT(*) FROM transfer_history");
    $count_transfer = $stmt2->fetchColumn();
    $stmt3 = $pdo->query("SELECT COUNT(*) FROM tagihan");
    $count_tagihan = $stmt3->fetchColumn();
    $total_transaksi = $count_topup + $count_transfer + $count_tagihan;
    $stmt_saldo = $pdo->query("SELECT SUM(balance) FROM users WHERE role = 'nasabah'");
    $total_saldo = $stmt_saldo->fetchColumn();
    if ($total_saldo === null) $total_saldo = 0;
} catch (Exception $e) {
    $total_nasabah = 0;
    $total_transaksi = 0;
    $total_saldo = 0;
}

// Ambil data transaksi per hari untuk 7 hari terakhir
$transaksi_per_hari = [];
$labels_per_hari = [];
try {
    $dateMap = [];
    for ($i = 6; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i days"));
        $labels_per_hari[] = date('d M', strtotime($tgl));
        $dateMap[$tgl] = 0;
    }
    // Topup
    $stmt = $pdo->query("SELECT DATE(tanggal) as tgl, COUNT(*) as jml FROM topup_history WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY tgl");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($dateMap[$row['tgl']])) $dateMap[$row['tgl']] += $row['jml'];
    }
    // Transfer
    $stmt = $pdo->query("SELECT DATE(created_at) as tgl, COUNT(*) as jml FROM transfer_history WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY tgl");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($dateMap[$row['tgl']])) $dateMap[$row['tgl']] += $row['jml'];
    }
    // Tagihan
    $stmt = $pdo->query("SELECT DATE(waktu) as tgl, COUNT(*) as jml FROM tagihan WHERE waktu >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY tgl");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($dateMap[$row['tgl']])) $dateMap[$row['tgl']] += $row['jml'];
    }
    $transaksi_per_hari = array_values($dateMap);
} catch (Exception $e) {
    $labels_per_hari = [];
    $transaksi_per_hari = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: #f4f8fb;
        }
        .main-navbar {
            width: 100%;
            background: #fff;
            box-shadow: 0 2px 12px rgba(25, 118, 210, 0.07);
            padding: 0.7rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-content {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding-left: 0.7rem;
            justify-content: flex-start;
            margin: 0;
            width: 100%;
            max-width: unset;
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .navbar-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            margin-right: 12px;
            background: #fff;
        }
        .navbar-logo {
            font-size: 2rem;
            font-weight: 800;
            color: #1976d2;
            letter-spacing: 1px;
        }
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(180deg, #1976d2 0%, #2196f3 100%);
            min-width: 270px;
            max-width: 320px;
            padding: 2.2rem 1.2rem 1.2rem 1.2rem;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            box-shadow: 4px 0 20px rgba(25,118,210,0.07);
        }
        .sidebar-profile {
            background: #2583e6;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(25,118,210,0.10);
            padding: 1.2rem 1rem 1.1rem 1rem;
            margin-bottom: 2.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar-avatar img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            margin-bottom: 0.7rem;
        }
        .sidebar-avatar div {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1976d2;
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            border-radius: 50%;
            border: 3px solid #fff;
            margin-bottom: 0.7rem;
        }
        .sidebar-name {
            font-size: 1.18rem;
            font-weight: 700;
            color: #fff;
            text-align: center;
        }
        .sidebar-role {
            font-size: 1rem;
            color: #e3e7ed;
            text-align: center;
            margin-top: 0.2rem;
        }
        .sidebar-menu-btns {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }
        .sidebar-btn {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            background: transparent;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            text-decoration: none;
            font-size: 1.08rem;
            transition: background 0.2s, color 0.2s;
            box-shadow: none;
            margin: 0;
        }
        .sidebar-btn.active, .sidebar-btn:active, .sidebar-btn:focus {
            background: #fff;
            color: #1976d2 !important;
            box-shadow: 0 2px 8px rgba(25,118,210,0.10);
        }
        .sidebar-btn i {
            font-size: 1.25rem;
        }
        .sidebar-btn.sidebar-logout {
            background: #fff;
            color: #d32f2f !important;
            font-weight: 700;
            margin-top: auto;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 8px rgba(211,47,47,0.10);
        }
        .sidebar-btn.sidebar-logout:hover {
            background: #d32f2f;
            color: #fff !important;
        }
        @media (max-width: 900px) {
            .sidebar { min-width: 100px; padding: 1.2rem 0.3rem; }
            .sidebar-profile { padding: 0.7rem 0.3rem; }
            .sidebar-btn { padding: 0.7rem 0.7rem; font-size: 1rem; }
        }
        .petugas-stats-outer {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 2.5rem;
        }
        .petugas-stats-grid {
            display: flex;
            flex-direction: row;
            gap: 2.7rem;
            width: 100%;
            max-width: 1500px;
            justify-content: flex-start;
            flex-wrap: nowrap;
        }
        .petugas-stats-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(25,118,210,0.08);
            display: flex;
            align-items: flex-start;
            gap: 1.3rem;
            padding: 2.7rem 2.7rem 2.7rem 2.2rem;
            min-height: 160px;
            min-width: 370px;
            max-width: 500px;
            flex: 1 1 0;
            transition: box-shadow 0.2s;
        }
        .petugas-stats-card:hover {
            box-shadow: 0 8px 32px rgba(25,118,210,0.13);
        }
        .petugas-stats-iconwrap {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            margin-right: 0.2rem;
            padding-top: 2px;
        }
        .petugas-stats-icon {
            width: 44px;
            height: 44px;
            background: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #1976d2;
        }
        .petugas-stats-label {
            font-size: 1rem;
            color: #222;
            font-weight: 600;
            margin-bottom: 0.15rem;
            margin-top: 0.1rem;
        }
        .petugas-stats-value {
            font-size: 2rem;
            font-weight: 800;
            color: #222;
            letter-spacing: 1px;
            margin-top: 0.1rem;
            word-break: break-all;
            overflow-wrap: break-word;
            white-space: normal;
        }
        @media (max-width: 1200px) {
            .petugas-stats-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .petugas-stats-card { min-width: 0; max-width: 100%; padding: 1.5rem 1rem; border-radius: 14px; }
            .petugas-stats-value { font-size: 1.5rem; }
        }
        .petugas-welcome {
            margin-bottom: 2.2rem;
            margin-top: 0.5rem;
        }
        .petugas-welcome-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1976d2;
            margin-bottom: 0.3rem;
        }
        .petugas-welcome-sub {
            font-size: 1.08rem;
            color: #222;
            font-weight: 500;
        }
        @media (max-width: 700px) {
            .petugas-welcome-title { font-size: 1.1rem; }
            .petugas-welcome-sub { font-size: 0.98rem; }
        }
    </style>
</head>
<body>
<nav class="main-navbar">
    <div class="navbar-content">
        <div class="navbar-logo">
            <img src="../image/logo.jpeg" alt="Logo" style="width:60px;height:60px;object-fit:contain;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.10);margin-right:12px;background:#fff;" />
            FTI M-Banking
        </div>
    </div>
</nav>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                    <?php if (strtolower($gender) === 'laki-laki'): ?>
                        <img src="../image/prioritas_male.png" alt="Prioritas Laki-laki" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php elseif (strtolower($gender) === 'perempuan'): ?>
                        <img src="../image/prioritas_female.png" alt="Prioritas Perempuan" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php else: ?>
                        <img src="../image/default_avatar.png" alt="Prioritas" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!empty($user_data['profile_photo'])): ?>
                        <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php else: ?>
                        <?= strtoupper(substr($name,0,1)) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <div class="sidebar-menu-btns" style="display:flex;flex-direction:column;gap:1rem;margin-top:2rem;">
            <a href="dashboard_petugas.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='dashboard_petugas.php'?' active':'' ?>"><i class="fa fa-home"></i> Dashboard</a>
            <a href="teller_nasabah.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='teller_nasabah.php'?' active':'' ?>"><i class="fa fa-users"></i> Nasabah</a>
            <a href="teller_transaksi.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='teller_transaksi.php'?' active':'' ?>"><i class="fa fa-exchange-alt"></i> Transaksi</a>
            <a href="teller_pengaturan.php" class="sidebar-btn<?= basename($_SERVER['PHP_SELF'])=='teller_pengaturan.php'?' active':'' ?>"><i class="fa fa-cog"></i> Pengaturan</a>
            <a href="logout.php" class="sidebar-btn sidebar-logout" style="margin-top:2rem;"><i class="fa fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>
    <main class="main-content">
        <!-- Welcome Message -->
        <div class="petugas-welcome">
            <div class="petugas-welcome-title">Selamat Datang, <?= htmlspecialchars($name) ?>!</div>
            <div class="petugas-welcome-sub">Anda login sebagai <b><?= htmlspecialchars($role) ?></b> di sistem Bank FTI.</div>
        </div>
        <!-- Statistik Box -->
        <div class="petugas-stats-outer">
            <div class="petugas-stats-grid">
                <div class="petugas-stats-card">
                    <div class="petugas-stats-iconwrap"><span class="petugas-stats-icon"><i class="fa fa-user"></i></span></div>
                    <div>
                        <div class="petugas-stats-label">Total Nasabah</div>
                        <div class="petugas-stats-value"><?= number_format($total_nasabah, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="petugas-stats-card">
                    <div class="petugas-stats-iconwrap"><span class="petugas-stats-icon"><i class="fa fa-exchange-alt"></i></span></div>
                    <div>
                        <div class="petugas-stats-label">Total Transaksi</div>
                        <div class="petugas-stats-value"><?= number_format($total_transaksi, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="petugas-stats-card">
                    <div class="petugas-stats-iconwrap"><span class="petugas-stats-icon"><i class="fa fa-university"></i></span></div>
                    <div>
                        <div class="petugas-stats-label">Saldo Keseluruhan</div>
                        <div class="petugas-stats-value"><?= number_format($total_saldo, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: Statistik Box -->
        
        <!-- Grafik Statistik -->
        <div style="display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;margin:2.5rem 0 0 0;">
            <div style="flex:1 1 500px;max-width:900px;background:#fff;border-radius:24px;box-shadow:0 4px 24px rgba(25,118,210,0.08);padding:2rem 2rem 1.5rem 2rem;min-width:320px;">
                <canvas id="dashboardChart" style="width:100%;height:320px;"></canvas>
            </div>
            <div style="flex:1 1 500px;max-width:900px;background:#fff;border-radius:24px;box-shadow:0 4px 24px rgba(25,118,210,0.08);padding:2rem 2rem 1.5rem 2rem;min-width:320px;">
                <canvas id="lineChart" style="width:100%;height:320px;"></canvas>
            </div>
        </div>
        <!-- Tabel Angka Statistik Bar & Line Chart (Full Screen, Centered) -->
        <div style="display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;margin:2.2rem auto 1.5rem auto;max-width:1500px;">
            <div style="flex:1 1 350px;background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(25,118,210,0.08);padding:0.5rem 2rem 0.5rem 2rem;min-width:260px;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#e3f2fd;color:#1976d2;font-weight:700;">
                            <th style="padding:0.7rem 0.5rem;text-align:center;">Total Nasabah</th>
                            <th style="padding:0.7rem 0.5rem;text-align:center;">Total Transaksi</th>
                            <th style="padding:0.7rem 0.5rem;text-align:center;">Saldo Keseluruhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background:#f6fafd;">
                            <td style="font-weight:bold; font-size:1.3rem; text-align:center;"><?= number_format($total_nasabah, 0, ',', '.') ?></td>
                            <td style="font-weight:bold; font-size:1.3rem; text-align:center;"><?= number_format($total_transaksi, 0, ',', '.') ?></td>
                            <td style="font-weight:bold; font-size:1.3rem; text-align:center;">Rp <?= number_format($total_saldo, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="flex:1 1 350px;background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(25,118,210,0.08);padding:0.5rem 2rem 0.5rem 2rem;min-width:260px;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#e3f2fd;color:#1976d2;font-weight:700;">
                            <th style="padding:0.7rem 0.5rem;text-align:center;">Tanggal</th>
                            <th style="padding:0.7rem 0.5rem;text-align:center;">Jumlah Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($labels_per_hari as $i => $tgl): ?>
                        <tr<?= $i%2==1 ? ' style=\"background:#f6fafd;\"' : '' ?>>
                            <td style="font-size:1.1rem; text-align:center;"><?= htmlspecialchars($tgl) ?></td>
                            <td style="font-weight:bold; font-size:1.1rem; text-align:center;"><?= $transaksi_per_hari[$i] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        const dashboardChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Nasabah', 'Total Transaksi', 'Saldo Keseluruhan'],
                datasets: [{
                    label: 'Statistik Bank FTI',
                    data: [<?= $total_nasabah ?>, <?= $total_transaksi ?>, <?= $total_saldo ?>],
                    backgroundColor: [
                        'rgba(25, 118, 210, 0.7)',
                        'rgba(67, 160, 71, 0.7)',
                        'rgba(255, 193, 7, 0.7)'
                    ],
                    borderColor: [
                        'rgba(25, 118, 210, 1)',
                        'rgba(67, 160, 71, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 60,
                    minBarLength: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Statistik Bank FTI', font: { size: 18 } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(<?= $total_nasabah ?>, <?= $total_transaksi ?>, <?= $total_saldo ?>) < 100 ? 100 : undefined,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            },
                            font: { size: 14 }
                        }
                    },
                    x: {
                        ticks: { font: { size: 14 } }
                    }
                }
            }
        });

        // Line Chart: Transaksi per Hari
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels_per_hari) ?>,
                datasets: [{
                    label: 'Transaksi per Hari',
                    data: <?= json_encode($transaksi_per_hari) ?>,
                    fill: false,
                    borderColor: 'rgba(25, 118, 210, 1)',
                    backgroundColor: 'rgba(25, 118, 210, 0.2)',
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(25, 118, 210, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    title: { display: true, text: 'Jumlah Transaksi per Hari (7 Hari Terakhir)', font: { size: 18 } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            callback: function(value) { return value; },
                            font: { size: 14 }
                        }
                    },
                    x: {
                        ticks: { font: { size: 14 } }
                    }
                }
            }
        });
        </script>
    </main>
</div>
<footer class="footer dashboard-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Bank FTI. Semua hak dilindungi undang-undang.</p>
        <p class="footer-note">
            Dibuat dengan <i class="fas fa-heart"></i> untuk Fakultas Teknologi Informasi
        </p>
    </div>
</footer>
<script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html>