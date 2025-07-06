<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teller', 'owner', 'admin'])) {
    header('Location: login.php');
    exit;
}
$name = $_SESSION['user_name'];
$role = ucfirst($_SESSION['role']);
$user_data = $_SESSION['user_data'] ?? [];
$gender = $user_data['gender'] ?? ($_SESSION['gender'] ?? '');

require_once '../config/database.php';
$db = new Database();
$pdo = $db->getConnection();
$transaksi_list = [];
$tagihan_list = [];
try {
    // Top Up
    $stmt = $pdo->prepare("SELECT t.tanggal, u.full_name as nasabah, u.account_number as rekening_nasabah, '-' as rekening_tujuan, t.nominal, 'Sukses' as status FROM topup_history t JOIN users u ON t.user_id = u.id ORDER BY t.tanggal DESC");
    $stmt->execute();
    $topup = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Transfer Keluar
    $stmt2 = $pdo->prepare("SELECT th.created_at as tanggal, u_from.full_name as nasabah, u_from.account_number as rekening_nasabah, u_to.account_number as rekening_tujuan, th.amount as nominal, 'Sukses' as status FROM transfer_history th JOIN users u_from ON th.from_user = u_from.id JOIN users u_to ON th.to_user = u_to.id ORDER BY th.created_at DESC");
    $stmt2->execute();
    $transfer_keluar = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    // Transfer Masuk
    $stmt3 = $pdo->prepare("SELECT th.created_at as tanggal, u_to.full_name as nasabah, u_to.account_number as rekening_nasabah, u_from.account_number as rekening_tujuan, th.amount as nominal, 'Sukses' as status FROM transfer_history th JOIN users u_to ON th.to_user = u_to.id JOIN users u_from ON th.from_user = u_from.id ORDER BY th.created_at DESC");
    $stmt3->execute();
    $transfer_masuk = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    // Gabungkan semua transaksi
    $transaksi_list = array_merge($topup, $transfer_keluar, $transfer_masuk);
    // Urutkan berdasarkan tanggal terbaru
    usort($transaksi_list, function($a, $b) {
        return strtotime($b['tanggal']) - strtotime($a['tanggal']);
    });

    $stmt_tagihan = $pdo->prepare("SELECT t.jenis, t.keterangan, t.nominal, t.status, t.waktu, u.full_name as nasabah FROM tagihan t JOIN users u ON t.user_id = u.id WHERE t.status = 'Lunas' ORDER BY t.waktu DESC");
    $stmt_tagihan->execute();
    $tagihan_list = $stmt_tagihan->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $transaksi_list = [];
    $tagihan_list = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Teller</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f4f8fb; }
        .main-navbar { width: 100%; background: #fff; box-shadow: 0 2px 12px rgba(25, 118, 210, 0.07); padding: 0.7rem 0; position: sticky; top: 0; z-index: 100; }
        .navbar-content { display: flex; align-items: center; gap: 0.7rem; padding-left: 0.7rem; justify-content: flex-start; margin: 0; width: 100%; max-width: unset; }
        .navbar-logo { display: flex; align-items: center; gap: 0.7rem; font-size: 2rem; font-weight: 800; color: #1976d2; letter-spacing: 1px; }
        .navbar-logo img { width: 60px; height: 60px; object-fit: contain; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.10); margin-right: 12px; background: #fff; }
        .dashboard-layout { display: flex; min-height: 100vh; }
        .sidebar { background: linear-gradient(180deg, #1976d2 0%, #2196f3 100%); min-width: 270px; max-width: 320px; padding: 2.2rem 1.2rem 1.2rem 1.2rem; display: flex; flex-direction: column; align-items: stretch; box-shadow: 4px 0 20px rgba(25,118,210,0.07); }
        .sidebar-profile { background: #2583e6; border-radius: 18px; box-shadow: 0 2px 8px rgba(25,118,210,0.10); padding: 1.2rem 1rem 1.1rem 1rem; margin-bottom: 2.2rem; display: flex; flex-direction: column; align-items: center; }
        .sidebar-avatar img { width: 64px; height: 64px; object-fit: cover; border-radius: 50%; border: 3px solid #fff; margin-bottom: 0.7rem; }
        .sidebar-avatar div { width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; background: #1976d2; color: #fff; font-size: 2rem; font-weight: 700; border-radius: 50%; border: 3px solid #fff; margin-bottom: 0.7rem; }
        .sidebar-name { font-size: 1.18rem; font-weight: 700; color: #fff; text-align: center; }
        .sidebar-role { font-size: 1rem; color: #e3e7ed; text-align: center; margin-top: 0.2rem; }
        .sidebar-menu-btns { display: flex; flex-direction: column; gap: 0.7rem; }
        .sidebar-btn { display: flex; align-items: center; gap: 0.9rem; background: transparent; color: #fff; font-weight: 600; border: none; border-radius: 12px; padding: 1rem 1.2rem; text-decoration: none; font-size: 1.08rem; transition: background 0.2s, color 0.2s; box-shadow: none; margin: 0; }
        .sidebar-btn.active, .sidebar-btn:active, .sidebar-btn:focus { background: #fff; color: #1976d2 !important; box-shadow: 0 2px 8px rgba(25,118,210,0.10); }
        .sidebar-btn i { font-size: 1.25rem; }
        .sidebar-btn.sidebar-logout { background: #fff; color: #d32f2f !important; font-weight: 700; margin-top: auto; margin-bottom: 0.5rem; box-shadow: 0 2px 8px rgba(211,47,47,0.10); }
        .sidebar-btn.sidebar-logout:hover { background: #d32f2f; color: #fff !important; }
        @media (max-width: 900px) { .sidebar { min-width: 100px; padding: 1.2rem 0.3rem; } .sidebar-profile { padding: 0.7rem 0.3rem; } .sidebar-btn { padding: 0.7rem 0.7rem; font-size: 1rem; } }
        .main-content { flex: 1; padding: 2.5rem 2.5rem 1.5rem 2.5rem; }
        .teller-table-section { background: #fff; border-radius: 18px; box-shadow: 0 4px 20px rgba(25,118,210,0.07); padding: 2rem 1.5rem; margin-top: 2rem; }
        .teller-table-section h2 { color: #1976d2; font-size: 1.2rem; font-weight: 700; margin-bottom: 1.2rem; }
        table.teller-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        table.teller-table th, table.teller-table td { padding: 0.8rem 0.5rem; text-align: left; }
        table.teller-table th { background: #e3f2fd; color: #1976d2; font-weight: 700; }
        table.teller-table tr:nth-child(even) { background: #f6fafd; }
        table.teller-table tr:hover { background: #e3f2fd; }
        .status-success { color: #43a047; font-weight: 700; }
        .status-failed { color: #d32f2f; font-weight: 700; }
        .tab-btn { background:#e3e7ed; color:#1976d2; border:none; border-radius:8px; padding:0.6rem 1.5rem; font-weight:700; font-size:1.05rem; cursor:pointer; transition:background 0.2s,color 0.2s; }
        .tab-btn.active { background:#1976d2; color:#fff; }
        @media print {
            body * { visibility: hidden !important; }
            .teller-table-section, .teller-table-section * { visibility: visible !important; }
            .teller-table-section h2, .tab-btn, #btn-cetak-transaksi { display: none !important; }
            .sidebar, .main-navbar, .footer, .dashboard-footer { display: none !important; }
            .teller-table-section { position: absolute; left: 0; top: 0; width: 100vw; background: #fff !important; box-shadow: none !important; }
            .teller-table { font-size: 1rem; }
            .teller-table-section > div:not([style*='display: block']):not([style='']) { display: none !important; }
            .teller-table-section > div { display: none !important; }
            .teller-table-section > div[style*='display: block'],
            .teller-table-section > div[style=''] { display: block !important; }
        }
    </style>
</head>
<body>
<nav class="main-navbar">
    <div class="navbar-content">
        <div class="navbar-logo">
            <img src="../image/logo.jpeg" alt="Logo" />
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
                        <img src="../image/prioritas_male.png" alt="Prioritas Laki-laki">
                    <?php elseif (strtolower($gender) === 'perempuan'): ?>
                        <img src="../image/prioritas_female.png" alt="Prioritas Perempuan">
                    <?php else: ?>
                        <img src="../image/default_avatar.png" alt="Prioritas">
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!empty($user_data['profile_photo'])): ?>
                        <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil">
                    <?php else: ?>
                        <div><?= strtoupper(substr($name,0,1)) ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <?php include 'sidebar_teller.php'; ?>
    </aside>
    <main class="main-content">
        <div class="teller-table-section">
            <h2 style="margin:0 0 1.2rem 0;display:flex;align-items:center;gap:0.7rem;">
                <i class="fa fa-exchange-alt"></i> Data Transaksi
                <button id="btn-cetak-transaksi" style="display:flex;align-items:center;gap:0.5rem;background:transparent;border:none;color:#1976d2 !important;font-weight:700;font-size:1.15rem;cursor:pointer;padding:0.3rem 0.8rem;border-radius:8px;transition:background 0.2s;box-shadow:none;outline:none;margin-left:auto;">
                    <i class="fa fa-print" style="font-size:1.4em;"></i>
                    <span style="font-size:1em;">Cetak</span>
                </button>
            </h2>
            <div style="display:flex;gap:0.7rem;margin-bottom:1.5rem;">
                <button id="tab-topup" class="tab-btn active" onclick="showTab('topup')">Top Up</button>
                <button id="tab-transfer-keluar" class="tab-btn" onclick="showTab('transfer-keluar')">Transfer Keluar</button>
                <button id="tab-transfer-masuk" class="tab-btn" onclick="showTab('transfer-masuk')">Transfer Masuk</button>
                <button id="tab-tagihan" class="tab-btn" onclick="showTab('tagihan')">Tagihan</button>
            </div>
            <div id="tab-content-topup">
                <table class="teller-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nasabah</th>
                            <th>Rekening Nasabah</th>
                            <th>Rekening Tujuan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach ($topup as $trx): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($trx['tanggal']) ?></td>
                            <td><?= htmlspecialchars($trx['nasabah']) ?></td>
                            <td><?= htmlspecialchars($trx['rekening_nasabah']) ?></td>
                            <td><?= htmlspecialchars($trx['rekening_tujuan']) ?></td>
                            <td>Rp <?= number_format($trx['nominal'], 0, ',', '.') ?></td>
                            <td class="<?= strtolower($trx['status']) === 'sukses' ? 'status-success' : 'status-failed' ?>">
                                <?= htmlspecialchars($trx['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topup)): ?>
                        <tr><td colspan="7" style="text-align:center;color:#888;">Tidak ada data top up.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div id="tab-content-transfer-keluar" style="display:none;">
                <table class="teller-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nasabah</th>
                            <th>Rekening Nasabah</th>
                            <th>Rekening Tujuan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach ($transfer_keluar as $trx): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($trx['tanggal']) ?></td>
                            <td><?= htmlspecialchars($trx['nasabah']) ?></td>
                            <td><?= htmlspecialchars($trx['rekening_nasabah']) ?></td>
                            <td><?= htmlspecialchars($trx['rekening_tujuan']) ?></td>
                            <td>Rp <?= number_format($trx['nominal'], 0, ',', '.') ?></td>
                            <td class="<?= strtolower($trx['status']) === 'sukses' ? 'status-success' : 'status-failed' ?>">
                                <?= htmlspecialchars($trx['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transfer_keluar)): ?>
                        <tr><td colspan="7" style="text-align:center;color:#888;">Tidak ada data transfer keluar.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div id="tab-content-transfer-masuk" style="display:none;">
                <table class="teller-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nasabah</th>
                            <th>Rekening Nasabah</th>
                            <th>Rekening Tujuan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach ($transfer_masuk as $trx): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($trx['tanggal']) ?></td>
                            <td><?= htmlspecialchars($trx['nasabah']) ?></td>
                            <td><?= htmlspecialchars($trx['rekening_nasabah']) ?></td>
                            <td><?= htmlspecialchars($trx['rekening_tujuan']) ?></td>
                            <td>Rp <?= number_format($trx['nominal'], 0, ',', '.') ?></td>
                            <td class="<?= strtolower($trx['status']) === 'sukses' ? 'status-success' : 'status-failed' ?>">
                                <?= htmlspecialchars($trx['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transfer_masuk)): ?>
                        <tr><td colspan="7" style="text-align:center;color:#888;">Tidak ada data transfer masuk.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div id="tab-content-tagihan" style="display:none;">
                <table class="teller-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Nasabah</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach ($tagihan_list as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['waktu']) ?></td>
                            <td><?= htmlspecialchars($row['nasabah']) ?></td>
                            <td><?= htmlspecialchars($row['jenis']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                            <td><span style="background:#27ae60;color:#fff;padding:0.3rem 1rem;border-radius:16px;font-weight:600;">Lunas</span></td>
                            <td><span style="background:#1976d2;color:#fff;padding:0.3rem 1rem;border-radius:16px;font-weight:600;">Saldo</span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tagihan_list)): ?>
                        <tr><td colspan="8" style="text-align:center;color:#888;">Tidak ada riwayat tagihan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            function showTab(tab) {
                document.getElementById('tab-topup').classList.toggle('active', tab==='topup');
                document.getElementById('tab-transfer-keluar').classList.toggle('active', tab==='transfer-keluar');
                document.getElementById('tab-transfer-masuk').classList.toggle('active', tab==='transfer-masuk');
                document.getElementById('tab-tagihan').classList.toggle('active', tab==='tagihan');
                document.getElementById('tab-content-topup').style.display = (tab==='topup') ? '' : 'none';
                document.getElementById('tab-content-transfer-keluar').style.display = (tab==='transfer-keluar') ? '' : 'none';
                document.getElementById('tab-content-transfer-masuk').style.display = (tab==='transfer-masuk') ? '' : 'none';
                document.getElementById('tab-content-tagihan').style.display = (tab==='tagihan') ? '' : 'none';
            }

            // Fallback jika Font Awesome tidak tersedia
            window.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('btn-cetak-transaksi');
                if (btn && !btn.querySelector('i').offsetWidth) {
                    btn.querySelector('i').outerHTML = '<span style="font-size:1.4em;">🖨️</span>';
                }
                btn.onmouseover = function() { btn.style.background = '#e3f2fd'; };
                btn.onmouseout = function() { btn.style.background = 'transparent'; };
                btn.onclick = function() {
                    // Sembunyikan semua tab-content kecuali yang aktif
                    var allTabs = ['tab-content-topup','tab-content-transfer-keluar','tab-content-transfer-masuk','tab-content-tagihan'];
                    var shown = null;
                    allTabs.forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el && (el.style.display === '' || el.style.display === 'block')) {
                            shown = el;
                        } else if (el) {
                            el.setAttribute('data-print-hide','1');
                        }
                    });
                    if (shown) shown.setAttribute('data-print-hide','0');
                    // Print
                    window.print();
                    // Kembalikan atribut
                    allTabs.forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el) el.removeAttribute('data-print-hide');
                    });
                };
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
<script src="../assets/js/main.js"></script>
</body>
</html> 