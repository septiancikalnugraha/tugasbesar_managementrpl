<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['owner', 'teller'])) {
    header('Location: login.php');
    exit;
}

$name = $_SESSION['user_name'];
$role = ucfirst($_SESSION['role']);
$user_data = $_SESSION['user_data'] ?? [];
$gender = $user_data['gender'] ?? ($_SESSION['gender'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <ul>
            <li><a href="#"><i class="fa fa-user"></i> Profil</a></li>
            <li><a href="#" class="active"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fa fa-users"></i> Data Nasabah</a></li>
            <li><a href="dashboard_transaksi.php"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="dashboard_pengaturan.php"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <?php if ($_SESSION['role'] === 'owner'): ?>
            <li><a href="#"><i class="fa fa-users-cog"></i> Manajemen Petugas</a></li>
            <?php endif; ?>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <h2>Selamat Datang, <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($role) ?>)</h2>
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Data Nasabah</h3>
                <p>Lihat dan kelola data nasabah bank.</p>
            </div>
            <div class="dashboard-card">
                <h3>Transaksi</h3>
                <p>Kelola transaksi bank (setor, tarik, transfer).</p>
            </div>
            <?php if ($_SESSION['role'] === 'owner'): ?>
            <div class="dashboard-card">
                <h3>Manajemen Petugas</h3>
                <p>Kelola data teller dan hak akses petugas bank.</p>
            </div>
            <?php endif; ?>
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
<script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html>