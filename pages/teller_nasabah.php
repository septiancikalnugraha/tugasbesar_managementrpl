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
$nasabah_list = [];
try {
    $stmt = $pdo->prepare("SELECT id, full_name, account_number, balance, kategori, last_login, status FROM users WHERE role = 'nasabah' ORDER BY full_name ASC");
    $stmt->execute();
    $nasabah_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $nasabah_list = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nasabah - Teller</title>
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
        .status-active { color: #43a047; font-weight: 700; }
        .status-inactive { color: #d32f2f; font-weight: 700; }
        @media print {
            body * { visibility: hidden !important; }
            .teller-table-section, .teller-table-section * { visibility: visible !important; }
            .teller-table-section { position: absolute; left: 0; top: 0; width: 100vw; background: #fff !important; box-shadow: none !important; }
            .teller-table-section h2, #btn-cetak-nasabah { display: none !important; }
            .sidebar, .main-navbar, .footer, .dashboard-footer { display: none !important; }
            .teller-table { font-size: 1rem; }
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
            <h2 style="display:flex;align-items:center;gap:0.7rem;"><i class="fa fa-users"></i> Data Nasabah
                <button id="btn-cetak-nasabah" style="display:flex;align-items:center;gap:0.5rem;background:transparent;border:none;color:#1976d2 !important;font-weight:700;font-size:1.15rem;cursor:pointer;padding:0.3rem 0.8rem;border-radius:8px;transition:background 0.2s;box-shadow:none;outline:none;margin-left:auto;">
                    <i class="fa fa-print" style="font-size:1.4em;"></i>
                    <span style="font-size:1em;">Cetak</span>
                </button>
            </h2>
            <table class="teller-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>No. Rekening</th>
                        <th>Saldo</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Riwayat Login</th>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($nasabah_list as $nasabah): ?>
                    <tr>
                        <td><?= htmlspecialchars($nasabah['full_name']) ?></td>
                        <td><?= htmlspecialchars($nasabah['account_number']) ?></td>
                        <td>Rp <?= number_format($nasabah['balance'], 0, ',', '.') ?></td>
                        <td><?= ($nasabah['kategori'] === 'prioritas') ? '<span style="color:#1976d2;font-weight:700;">Prioritas</span>' : 'Non Prioritas' ?></td>
                        <td class="<?= $nasabah['status'] === 'Aktif' ? 'status-active' : 'status-inactive' ?>"><?= htmlspecialchars($nasabah['status']) ?></td>
                        <td><?= $nasabah['last_login'] ? date('d-m-Y H:i', strtotime($nasabah['last_login'])) : '-' ?></td>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <td>
                            <form method="POST" action="update_nasabah_status.php" style="display:inline;">
                                <input type="hidden" name="nasabah_id" value="<?= $nasabah['id'] ?>">
                                <input type="hidden" name="status" value="<?= $nasabah['status'] === 'Aktif' ? 'Nonaktif' : 'Aktif' ?>">
                                <button type="submit" style="background:<?= $nasabah['status'] === 'Aktif' ? '#d32f2f' : '#43a047' ?>;color:#fff;border:none;padding:0.4rem 0.8rem;border-radius:6px;cursor:pointer;font-weight:600;">
                                    <?= $nasabah['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($nasabah_list)): ?>
                    <tr><td colspan="5" style="text-align:center;color:#888;">Tidak ada data nasabah.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
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
<script>
// Fallback jika Font Awesome tidak tersedia
window.addEventListener('DOMContentLoaded', function() {
  var btn = document.getElementById('btn-cetak-nasabah');
  if (btn && !btn.querySelector('i').offsetWidth) {
    btn.querySelector('i').outerHTML = '<span style="font-size:1.4em;">🖨️</span>';
  }
  btn.onmouseover = function() { btn.style.background = '#e3f2fd'; };
  btn.onmouseout = function() { btn.style.background = 'transparent'; };
});
document.getElementById('btn-cetak-nasabah').onclick = function() {
    window.print();
};
</script>
</body>
</html> 