<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teller', 'owner'])) {
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
    <title>Pengaturan - Teller</title>
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
        .teller-settings-section { background: #fff; border-radius: 18px; box-shadow: 0 4px 20px rgba(25,118,210,0.07); padding: 2rem 1.5rem; margin-top: 2rem; }
        .teller-settings-section h2 { color: #1976d2; font-size: 1.2rem; font-weight: 700; margin-bottom: 1.2rem; }
        .settings-list { list-style: none; padding: 0; margin: 0; }
        .settings-list li { margin-bottom: 1.2rem; }
        .settings-list a { color: #1976d2; font-weight: 600; text-decoration: none; font-size: 1.08rem; }
        .settings-list a:hover { text-decoration: underline; }
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
        <div class="teller-settings-section">
            <h2><i class="fa fa-cog"></i> Pengaturan</h2>
            <ul class="settings-list">
                <li><a href="#"><i class="fa fa-key"></i> Ubah Password</a></li>
                <li><a href="#"><i class="fa fa-user"></i> Ubah Profil</a></li>
                <li><a href="#"><i class="fa fa-bell"></i> Notifikasi</a></li>
                <li><a href="#"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
            </ul>
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
</body>
</html> 