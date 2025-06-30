<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/auth.php';
$auth = new Auth();
$user_data = $auth->getUserData($_SESSION['user_id']);
$name = $user_data['full_name'] ?? '';
$email = $user_data['email'] ?? '';
$phone = $user_data['phone'] ?? '';
$account_number = $user_data['account_number'] ?? '';
$role = $user_data['role'] ?? '';
$balance = $user_data['balance'] ?? 0;
$gender = $user_data['gender'] ?? '';
$birth_date = $user_data['birth_date'] ?? '';
$provinsi = $user_data['provinsi'] ?? '';
$kota = $user_data['kota'] ?? '';
$formatted_balance = number_format($balance, 0, ',', '.');

// Format tanggal lahir
$formatted_birth_date = '';
if (!empty($birth_date)) {
    $date = new DateTime($birth_date);
    $formatted_birth_date = $date->format('d F Y');
}

$profile_photo_path = !empty($user_data['profile_photo'])
    ? (strpos($user_data['profile_photo'], 'uploads/profile_photos/') === 0 ? '../' . htmlspecialchars($user_data['profile_photo']) : '../image/default_avatar.png')
    : '../image/default_avatar.png';

function getInitials($name) {
    $words = preg_split('/\s+/', trim($name));
    if (count($words) >= 2) {
        return strtoupper(mb_substr($words[0],0,1) . mb_substr($words[1],0,1));
    } else {
        return strtoupper(mb_substr($name,0,2));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body, html {
            height: auto;
            margin: 0;
            padding: 0;
        }
        /* Hapus/override custom dashboard-layout dan main-content agar ikut global */
        .dashboard-layout {
            min-height: unset !important;
            display: flex !important;
            background: unset !important;
        }
        .main-content {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 100vh !important;
        }
        .sidebar-profile .sidebar-avatar {
            width: 50px !important;
            height: 50px !important;
            font-size: 1.8rem !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            background: linear-gradient(135deg, #fff 0%, #e3f2fd 100%) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden;
        }
        .sidebar-profile {
            flex-direction: row !important;
            gap: 1rem !important;
            align-items: center !important;
            justify-content: flex-start !important;
        }
        .sidebar-profile img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 50% !important;
        }
        body { background: #f7f9fb; }
        .mobile-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem 1.5rem 0.5rem 1.5rem;
            background: #fff;
            border-bottom: 1px solid #e3e7ed;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .mobile-header .back-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #1976d2;
            cursor: pointer;
            margin-right: 0.5rem;
        }
        .mobile-header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #222;
        }
        .profile-main-card {
            background: #fff;
            border-radius: 18px;
            margin: 2rem auto 1.5rem auto;
            max-width: 480px;
            box-shadow: 0 4px 24px rgba(25,118,210,0.08);
            padding: 0 0 1.5rem 0;
        }
        .profile-photo-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 2rem 1.2rem 2rem;
            border-bottom: 1px solid #f0f4f8;
        }
        .profile-photo {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 700;
            color: #1976d2;
            overflow: hidden;
        }
        .profile-photo img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .profile-photo-upload {
            margin-left: 1.2rem;
            font-size: 0.95rem;
            color: #1976d2;
            cursor: pointer;
            background: none;
            border: none;
            font-weight: 600;
        }
        .profile-photo-upload:hover { text-decoration: underline; }
        .profile-info-list {
            width: 100%;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .profile-info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 2rem;
            border-bottom: 1px solid #f0f4f8;
            background: #fff;
        }
        .profile-info-label {
            color: #888;
            font-size: 1.01rem;
            font-weight: 500;
        }
        .profile-info-value {
            color: #222;
            font-size: 1.05rem;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            overflow-wrap: break-word;
        }
        .profile-info-edit {
            color: #1976d2;
            font-size: 0.98rem;
            margin-left: 1.2rem;
            cursor: pointer;
            font-weight: 600;
            background: none;
            border: none;
        }
        .profile-info-edit:hover { text-decoration: underline; }
        .profile-section-title {
            font-size: 1.13rem;
            font-weight: 700;
            color: #1976d2;
            margin: 2.2rem 0 1.1rem 0;
            padding-left: 2rem;
        }
        .profile-section-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(25,118,210,0.06);
            max-width: 480px;
            margin: 0 auto 2.5rem auto;
            padding: 0 0 1.5rem 0;
        }
        .profile-section-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .profile-section-item {
            padding: 1.1rem 2rem;
            border-bottom: 1px solid #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .profile-section-label {
            color: #888;
            font-size: 1.01rem;
            font-weight: 500;
        }
        .profile-section-value {
            color: #222;
            font-size: 1.05rem;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            overflow-wrap: break-word;
        }
        .profile-section-edit {
            color: #1976d2;
            font-size: 0.98rem;
            margin-left: 1.2rem;
            cursor: pointer;
            font-weight: 600;
            background: none;
            border: none;
        }
        .profile-section-edit:hover { text-decoration: underline; }
        .profile-checkbox-row {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            padding: 1.2rem 2rem 0.5rem 2rem;
        }
        .profile-checkbox-row label {
            font-size: 0.97rem;
            color: #444;
        }
        .profile-section-btn {
            width: calc(100% - 4rem);
            margin: 1.2rem 2rem 0 2rem;
            padding: 1rem 0;
            border-radius: 10px;
            background: #1976d2;
            color: #fff;
            font-weight: 700;
            border: none;
            font-size: 1.08rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .profile-section-btn:disabled {
            background: #b0bec5;
            cursor: not-allowed;
        }
        @media (max-width: 600px) {
            .profile-main-card, .profile-section-card {
                max-width: 100vw;
                border-radius: 0;
                margin: 0 0 1.5rem 0;
                padding: 0 0 1.5rem 0;
            }
            .profile-info-item, .profile-section-item, .profile-checkbox-row {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .profile-section-title {
                padding-left: 1rem;
            }
        }
        
        /* Exclusive styling for prioritas users */
        .profile-card-exclusive {
            background: linear-gradient(135deg, #2196f3 0%, #0d47a1 100%) !important;
            border: 2px solid #FFD700 !important;
            box-shadow: 0 8px 32px rgba(255, 215, 0, 0.3), 0 4px 16px rgba(33, 150, 243, 0.18) !important;
            position: relative;
            overflow: hidden;
        }
        
        .profile-card-exclusive::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.13) 50%, transparent 70%);
            animation: shine 3s infinite;
            pointer-events: none;
        }
        
        .profile-badge-prioritas {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #FFD700;
            color: #222;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            transform: rotate(15deg);
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
            z-index: 10;
        }
        
        .profile-avatar-exclusive {
            border: 3px solid #FFD700 !important;
            box-shadow: 0 4px 16px rgba(255, 215, 0, 0.3) !important;
            background: #fff !important;
        }
        
        .profile-text-exclusive {
            color: #fff !important;
        }
        
        .profile-text-exclusive-secondary {
            color: #FFD700 !important;
        }
        
        .profile-text-exclusive-tertiary {
            color: #222 !important;
        }
        
        .profile-crown-icon {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #FFD700;
            margin-left: 1rem;
        }
        
        .profile-crown-icon i {
            color: #FFD700;
            font-size: 1.2rem;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        /* Tambahan agar logo dan judul navbar benar-benar rata kiri */
        .navbar-content {
            max-width: unset !important;
            margin: 0 !important;
            padding-left: 0.7rem !important;
            padding-right: 0 !important;
            justify-content: flex-start !important;
        }
        .navbar-logo {
            margin-left: 0 !important;
            gap: 0.7rem !important;
        }
        .navbar-logo img {
            margin-left: 0 !important;
            margin-right: 10px !important;
        }
        .dashboard-section.profile-card-exclusive, .dashboard-section {
            margin: 0 auto 2.5rem auto !important;
        }
        .profile-badge-prioritas {
            top: 10px !important;
            right: 18px !important;
            font-size: 0.9rem !important;
            padding: 5px 16px !important;
        }
        .profile-avatar-large {
            width: 100px !important;
            height: 100px !important;
            font-size: 2.1rem !important;
            border-width: 3px !important;
        }
        .dashboard-section form#upload-photo-form {
            margin-top: 0.5rem !important;
        }
        .dashboard-section .btn-lanjut {
            padding: 0.5rem 1.2rem !important;
            font-size: 1rem !important;
            border-radius: 7px !important;
        }
        .dashboard-section .sidebar-avatar img {
            width: 100% !important;
            height: 100% !important;
        }
        .dashboard-section .sidebar-avatar {
            margin-bottom: 0.5rem !important;
        }
        .dashboard-section .profil-info-row, .dashboard-section .profil-info-label, .dashboard-section .profil-info-value {
            font-size: 0.98rem !important;
        }
        .dashboard-section > div[style*='flex-shrink'] {
            margin-left: 0 !important;
            min-width: 120px !important;
        }
        .dashboard-section > div[style*='flex:1'] {
            padding-left: 1.5rem !important;
        }
        .dashboard-section .profil-info-row {
            margin-top: 0.7rem !important;
        }
        .dashboard-section .profil-info-label {
            min-width: 120px !important;
        }
        .dashboard-section .profil-info-value {
            min-width: 80px !important;
        }
        .dashboard-section .profil-info-row .fa-crown {
            font-size: 1rem !important;
        }
        .dashboard-section .profil-info-row {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        .dashboard-section .profil-info-label, .dashboard-section .profil-info-value {
            display: inline-block !important;
        }
        .dashboard-section .profil-info-label {
            font-weight: 600 !important;
        }
        .dashboard-section .profil-info-value {
            font-weight: 700 !important;
        }
        .dashboard-section .profil-info-row {
            margin-bottom: 0 !important;
        }
        @media (max-width: 900px) {
            .dashboard-section.profile-card-exclusive, .dashboard-section {
                flex-direction: column !important;
                align-items: center !important;
                padding: 1.2rem 0.5rem 1.2rem 0.5rem !important;
                max-width: 98vw !important;
            }
            .dashboard-section > div[style*='flex:1'] {
                padding-left: 0 !important;
            }
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
                        <!-- fallback jika gender tidak jelas -->
                        <img src="../image/default_avatar.png" alt="Prioritas" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!empty($user_data['profile_photo'])): ?>
                        <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil">
                    <?php else: ?>
                        <?= getInitials($name) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <ul>
            <li><a href="dashboard_profil.php" class="active"><i class="fa fa-user"></i> Profil</a></li>
            <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard_transaksi.php"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="dashboard_pengaturan.php"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="dashboard-section <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>profile-card-exclusive<?php endif; ?>" style="width:100%;max-width:700px;margin:0 0 2.5rem 0;box-shadow:0 4px 24px rgba(25,118,210,0.08);background:#fff;border-radius:18px;display:flex;align-items:center;justify-content:center;gap:2.5rem;padding:2.5rem 2.5rem 2.5rem 2.5rem;min-height:340px;position:relative;">
            <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                <div class="profile-badge-prioritas">PRIORITAS</div>
            <?php endif; ?>
            <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;min-width:140px;">
                <div class="sidebar-avatar profile-avatar-large <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>profile-avatar-exclusive<?php endif; ?>" id="profile-avatar-preview" style="width:120px;height:120px;font-size:2.1rem;box-shadow:0 4px 18px rgba(25,118,210,0.13);background:#f4f6f8;border:3px solid #1976d2;">
                    <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                        <?php if (strtolower($gender) === 'laki-laki'): ?>
                            <img src="../image/prioritas_male.png" alt="Prioritas Laki-laki" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php elseif (strtolower($gender) === 'perempuan'): ?>
                            <img src="../image/prioritas_female.png" alt="Prioritas Perempuan" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                            <!-- fallback jika gender tidak jelas -->
                            <img src="../image/default_avatar.png" alt="Prioritas" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!empty($user_data['profile_photo'])): ?>
                            <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                            <?= getInitials($name) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center;height:100%;padding-left:0;">
                <div style="font-size:1.3rem;font-weight:800;letter-spacing:0.5px;margin-bottom:1.2rem; text-align:left; <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php endif; ?>">Profil Saya</div>
                <div style="display:grid;grid-template-columns:160px 1fr;gap:0.7rem 1.5rem;align-items:center;justify-content:flex-start;max-width:420px;margin:0;">
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Nama Lengkap</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($name) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Email</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($email) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">No. Handphone</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($phone) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Jenis Kelamin</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($gender) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Tanggal Lahir</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($formatted_birth_date) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Provinsi</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($provinsi) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Kota/Kabupaten</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($kota) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Nomor Rekening</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>;display:flex;align-items:center;gap:8px;"><span id="profil-account-number" style="user-select:all;"><?= htmlspecialchars($account_number) ?></span><button id="profil-copy-btn" title="Salin Nomor Rekening" style="background:none;border:none;cursor:pointer;outline:none;padding:0;"><i class="fa fa-copy" id="profil-copy-icon" style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#555<?php endif; ?>;font-size:1.1em;"></i></button><span id="profil-copy-toast" style="display:none;margin-left:8px;color:#ff9800;font-size:0.98em;font-weight:600;vertical-align:middle;">Nomor rekening disalin!</span></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Role</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>"><?= htmlspecialchars($role) ?></div>
                    <div style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Saldo</div><div style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>">Rp <?= $formatted_balance ?></div>
                    <div class="profil-info-row">
                        <span class="profil-info-label" style="color:<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>rgba(255,255,255,0.8)<?php else: ?>#1976d2<?php endif; ?>;font-size:1rem;font-weight:600;">Kategori Nasabah</span>
                        <span class="profil-info-value" style="font-size:1.08rem;font-weight:700;<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>color:#fff;<?php else: ?>color:#222;<?php endif; ?>;display:flex;align-items:center;gap:8px;">
                            <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                                <i class="fa fa-crown" style="color:#FFD700;font-size:1.1rem;"></i>
                                <?= ucfirst($user_data['kategori']) ?>
                            <?php else: ?>
                                <?= ucfirst($user_data['kategori'] ?? 'Non-prioritas') ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
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
<script>
const input = document.getElementById('profile-photo-input');
const previewCard = document.getElementById('profile-avatar-preview');
const previewSidebar = document.querySelector('.sidebar-profile .sidebar-avatar');
const feedback = document.getElementById('upload-feedback');
input.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const formData = new FormData();
        formData.append('photo', this.files[0]);
        feedback.textContent = 'Mengunggah foto...';
        fetch('../upload_profile_photo.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.photo) {
                const imgTag = '<img src="' + data.photo + '?t=' + Date.now() + '" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
                if (previewCard) previewCard.innerHTML = imgTag;
                if (previewSidebar) previewSidebar.innerHTML = imgTag;
                feedback.style.color = '#388e3c';
                feedback.textContent = 'Foto profil berhasil diubah!';
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            } else {
                feedback.style.color = '#d32f2f';
                let msg = data.message || 'Gagal upload foto';
                if (data.errorInfo) {
                  msg += ' [errorInfo: ' + (Array.isArray(data.errorInfo) ? data.errorInfo.join(' | ') : data.errorInfo) + ']';
                }
                if (data.user_id !== undefined) {
                  msg += ' [user_id: ' + data.user_id + ']';
                }
                if (data.upload_path !== undefined) {
                  msg += ' [upload_path: ' + data.upload_path + ']';
                }
                if (data.db_path !== undefined) {
                  msg += ' [db_path: ' + data.db_path + ']';
                }
                if (data.unlink_error !== undefined && data.unlink_error) {
                  msg += ' [unlink_error: ' + data.unlink_error + ']';
                }
                feedback.textContent = msg;
            }
        })
        .catch((err) => {
            feedback.style.color = '#d32f2f';
            feedback.textContent = 'Gagal upload foto: ' + err;
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
  var copyBtn = document.getElementById('profil-copy-btn');
  var copyIcon = document.getElementById('profil-copy-icon');
  var toast = document.getElementById('profil-copy-toast');
  if (copyBtn) {
    copyBtn.onclick = function() {
      var accNum = document.getElementById('profil-account-number').textContent.trim();
      navigator.clipboard.writeText(accNum).then(function() {
        if (copyIcon) copyIcon.style.color = '#ff9800';
        if (toast) {
          toast.style.display = 'inline';
          setTimeout(function(){
            toast.style.display = 'none';
            if (copyIcon) copyIcon.style.color = '#555';
          }, 1500);
        }
      }, function() {
        alert('Gagal menyalin nomor rekening');
      });
    };
  }
});
</script>
</body>
</html>