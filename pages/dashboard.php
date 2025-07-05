<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
session_start();
$base_url = '../';
$page_title = 'Dashboard';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/auth.php';

// Get updated user data
$auth = new Auth();
$user_data = $auth->getUserData($_SESSION['user_id']);

if ($user_data) {
    $_SESSION['balance'] = $user_data['balance'];
    $_SESSION['account_number'] = $user_data['account_number'];
}

$name = $_SESSION['user_name'];
$role = 'Nasabah';
$account_number = $_SESSION['account_number'] ?? 'Tidak tersedia';
$balance = $_SESSION['balance'] ?? 0;

// Format balance for display
$formatted_balance = number_format($balance, 0, ',', '.');

// Get current time for greeting
$current_hour = date('G');
if ($current_hour < 12) {
    $greeting = 'Selamat Pagi';
} elseif ($current_hour < 15) {
    $greeting = 'Selamat Siang';
} elseif ($current_hour < 18) {
    $greeting = 'Selamat Sore';
} else {
    $greeting = 'Selamat Malam';
}

$last_login = $user_data['last_login'] ?? ($_SESSION['last_login'] ?? null);
$formatted_last_login = $last_login ? date('d M Y, H:i', strtotime($last_login)) : '-';

$gender = $user_data['gender'] ?? ($_SESSION['gender'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* --- Dashboard Main Components Modern UI --- */
        .welcome-header {
            background: #fff;
            border-radius: 18px;
            padding: 1.5rem 2rem 1.2rem 2rem;
            margin-bottom: 2.2rem;
            box-shadow: 0 4px 20px rgba(25,118,210,0.07);
            border: 1px solid #e3f2fd;
            text-align: left;
        }
        .welcome-text {
            color: #1976d2;
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .welcome-subtitle {
            color: #666;
            font-size: 1.05rem;
            margin: 0.5rem 0 0 0;
            font-weight: 500;
        }
        .account-info-card {
            background: linear-gradient(180deg, #1976d2 0%, #1565c0 100%) !important;
            color: #fff !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            border-radius: 12px !important;
            padding: 1rem 1.2rem 0.8rem 1.2rem !important;
            box-shadow: 0 4px 18px rgba(25,118,210,0.10);
            margin-bottom: 2.2rem !important;
        }
        .account-header h3 {
            font-size: 1rem !important;
            margin-bottom: 0.7rem !important;
            gap: 0.3rem !important;
        }
        .account-details {
            background: rgba(255,255,255,0.10) !important;
            border: 1px solid rgba(255,255,255,0.18) !important;
        }
        .account-item {
            /* border-bottom: 1px solid rgba(255,255,255,0.13) !important; */
            padding: 0.7rem 0;
        }
        .account-item:last-child { border-bottom: none !important; }
        .account-label, .account-value {
            color: #fff !important;
        }
        .account-value.balance, .account-value.status-active {
            color: #4ade80 !important;
        }
        /* Balance Card */
        .dashboard-balance-card {
            max-width: 340px;
            margin: 1.5rem auto 2.2rem auto;
        }
        .dashboard-balance-card-inner {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 32px rgba(25,118,210,0.10);
            padding: 1.7rem 1.2rem 1.3rem 1.2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .dashboard-balance-card-icon {
            width: 56px;
            height: 56px;
            background: #1976d2;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.1rem;
        }
        .dashboard-balance-card-amount {
            font-size: 2rem;
            font-weight: 800;
            color: #1976d2;
            margin-bottom: 0.4rem;
            letter-spacing: 1px;
        }
        .dashboard-balance-card-label {
            color: #888;
            font-size: 1.02rem;
            letter-spacing: 0.2px;
        }
        /* Quick Actions */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.2rem;
            margin-top: 0.5rem !important;
        }
        .action-btn {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e3f2fd;
            box-shadow: 0 4px 16px rgba(25,118,210,0.07);
            padding: 1.3rem 1.1rem 1.1rem 1.1rem;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            min-height: 160px;
        }
        .action-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(25,118,210,0.13);
            background: #f8fafc;
        }
        .action-btn-icon {
            width: 44px;
            height: 44px;
            background: #1976d2;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: #fff;
            font-size: 1.25rem;
        }
        .action-btn-title, .action-btn-desc {
            text-align: left !important;
            align-items: flex-start !important;
            justify-content: flex-start !important;
        }
        .action-btn-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #1976d2;
            margin-bottom: 0.4rem;
        }
        .action-btn-desc {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        /* Recent Activity */
        .recent-activity {
            background: #fff;
            border-radius: 16px;
            padding: 1.3rem 1.2rem 1.1rem 1.2rem;
            box-shadow: 0 4px 20px rgba(25,118,210,0.07);
            border: 1px solid #e3f2fd;
            max-width: 540px;
            margin: 0 auto 2.2rem auto;
        }
        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e3f2fd;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
        }
        .activity-details {
            flex: 1;
        }
        .activity-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.2rem;
            font-size: 1.01rem;
        }
        .activity-time {
            color: #666;
            font-size: 0.89rem;
        }
        .activity-amount {
            font-weight: 700;
            color: #4caf50;
            font-size: 1.1rem;
        }
        @media (max-width: 700px) {
            .welcome-header, .account-info-card, .recent-activity {
                padding: 1.1rem 0.7rem 1rem 0.7rem;
                border-radius: 12px;
            }
            .dashboard-balance-card {
                max-width: 98vw;
            }
            .dashboard-balance-card-inner {
                padding: 1.2rem 0.5rem 1rem 0.5rem;
                border-radius: 10px;
            }
            .action-buttons {
                grid-template-columns: 1fr;
                gap: 0.7rem;
            }
            .action-btn {
                min-height: 120px;
                padding: 1rem 0.7rem 0.8rem 0.7rem;
                border-radius: 10px;
            }
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
        .menu-section {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(25,118,210,0.07);
            padding: 1.5rem 1.2rem 1.2rem 1.2rem;
            margin-bottom: 2.2rem;
            border: 1px solid #f1f5f9;
        }
        .menu-section-title {
            font-size: 1.18rem;
            font-weight: 700;
            margin-bottom: 1.1rem;
            color: #222;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 1.1rem 0.5rem;
            justify-items: center;
        }
        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-width: 90px;
            min-height: 90px;
            cursor: pointer;
            transition: background 0.2s;
            position: relative;
        }
        .menu-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            margin-bottom: 0.5rem;
            color: #fff;
        }
        .menu-label {
            font-size: 0.98rem;
            color: #222;
            text-align: center;
            margin-top: 0.1rem;
            font-weight: 500;
        }
        .menu-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ff6f00;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.1rem 0.5rem;
            letter-spacing: 0.5px;
        }
        .menu-icon.orange { background: #ff9800; }
        .menu-icon.green { background: #43a047; }
        .menu-icon.blue { background: #1976d2; }
        .menu-icon.red { background: #e53935; }
        .menu-icon.purple { background: #8e24aa; }
        .menu-icon.teal { background: #00897b; }
        .menu-icon.gray { background: #607d8b; }
        .menu-icon.olive { background: #689f38; }
        .menu-icon.pink { background: #d81b60; }
        .menu-icon.cyan { background: #00bcd4; }
        .menu-icon.lime { background: #cddc39; color: #333; }
        .menu-icon.gold { background: #ffc107; color: #333; }
        @media (max-width: 700px) {
            .menu-section { padding: 1rem 0.5rem 0.7rem 0.5rem; border-radius: 12px; }
            .menu-grid { grid-template-columns: repeat(4, 1fr); gap: 0.7rem 0.2rem; }
            .menu-item { min-width: 70px; }
            .menu-icon { width: 38px; height: 38px; font-size: 1.2rem; }
            .menu-label { font-size: 0.85rem; }
        }
        .ewallet-popup-overlay {
            position: fixed; left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.35); z-index: 20000; display: flex; align-items: center; justify-content: center;
        }
        .ewallet-popup-modal {
            background: #fff; border-radius: 18px; max-width: 370px; width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 1.5rem 1.2rem 1.2rem 1.2rem; position: relative; animation: popupIn 0.2s;
        }
        @keyframes popupIn { from { transform: translateY(40px); opacity: 0; } to { transform: none; opacity: 1; } }
        .ewallet-popup-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.2rem; }
        .ewallet-popup-title { font-size: 1.25rem; font-weight: 700; color: #1976d2; }
        .ewallet-popup-close { background: none; border: none; font-size: 1.7rem; color: #888; cursor: pointer; margin-left: 1rem; }
        .ewallet-list { display: flex; flex-direction: column; gap: 1.1rem; }
        .ewallet-item { display: flex; align-items: center; gap: 1rem; cursor: pointer; padding: 0.5rem 0.2rem; border-radius: 10px; transition: background 0.2s; }
        .ewallet-item:hover { background: #f4f6f8; }
        .ewallet-item img { width: 38px; height: 38px; border-radius: 50%; object-fit: contain; background: #fff; border: 1px solid #e3e7ed; }
        .ewallet-item span { font-size: 1.08rem; color: #222; font-weight: 500; }
        @media (max-width: 600px) { .ewallet-popup-modal { max-width: 98vw; padding: 1rem 0.5rem; } .ewallet-popup-title { font-size: 1.1rem; } .ewallet-item img { width: 32px; height: 32px; } .ewallet-item span { font-size: 0.98rem; } }
        .qr-action-btn {
          width: 100%;
          padding: 0.7rem 0;
          font-size: 1.05rem;
          border-radius: 8px;
          background: #1976d2;
          color: #fff;
          font-weight: 700;
          border: none;
          cursor: pointer;
          transition: background 0.2s;
          margin-bottom: 0.5rem;
        }
        .qr-action-btn:hover { background: #1565c0; }
        .action-btn.qr-scan-btn {
          display: flex;
          align-items: center;
          gap: 1.1rem;
          background: #fff;
          border-radius: 14px;
          border: 1px solid #e3f2fd;
          box-shadow: 0 4px 16px rgba(25,118,210,0.07);
          padding: 1.3rem 1.3rem 1.1rem 1.3rem;
          cursor: pointer;
          transition: all 0.3s ease;
          min-height: 110px;
          width: 100%;
          margin-bottom: 1.2rem;
        }
        .action-btn.qr-scan-btn:hover {
          background: #e3f2fd;
          box-shadow: 0 8px 24px rgba(25,118,210,0.13);
        }
        .qr-scan-icon {
          width: 48px;
          height: 48px;
          background: #1976d2;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #fff;
          font-size: 2rem;
        }
        .qr-scan-content {
          display: flex;
          flex-direction: column;
          align-items: flex-start;
          justify-content: center;
        }
        .qr-scan-title {
          font-size: 1.13rem;
          font-weight: 700;
          color: #1976d2;
          margin-bottom: 0.25rem;
        }
        .qr-scan-desc {
          color: #666;
          font-size: 1.01rem;
          font-weight: 400;
        }
        /* Popup aksi QR setelah scan */
        #qr-action-popup {
          display: none;
          position: fixed;
          z-index: 10000;
          top: 0;
          left: 0;
          width: 100vw;
          height: 100vh;
          background: rgba(0,0,0,0.35);
          align-items: center;
          justify-content: center;
        }
        #qr-action-popup div {
          background: #fff;
          border-radius: 18px;
          max-width: 370px;
          width: 95vw;
          box-shadow: 0 8px 32px rgba(0,0,0,0.18);
          padding: 2rem 1.2rem 1.2rem 1.2rem;
          position: relative;
          text-align: center;
        }
        #qr-action-rekening {
          font-size: 1.08rem;
          color: #1976d2;
          margin-bottom: 1.2rem;
        }
        #qr-action-popup button {
          width: 100%;
          margin-top: 1rem;
          padding: 0.7rem 0;
          font-size: 1.05rem;
          border-radius: 8px;
          background: #eee;
          color: #1976d2;
          font-weight: 700;
          border: none;
          cursor: pointer;
        }
        #qr-action-popup button:hover { background: #1565c0; }
        .action-btn-horizontal {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            text-align: left !important;
            gap: 1.2rem;
        }
        .action-btn-horizontal .action-btn-icon {
            margin-bottom: 0 !important;
        }
        .action-btn-horizontal .action-btn-text-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .account-info-card-prioritas {
            background: linear-gradient(120deg,#1976d2 0%,#42a5f5 100%)!important;
            color: #fff!important;
            border: 2.5px solid #FFD700!important;
            box-shadow: 0 8px 32px rgba(25,118,210,0.13),0 4px 18px rgba(66,165,245,0.13)!important;
            position: relative;
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
        <ul>
            <li><a href="dashboard_profil.php"><i class="fa fa-user"></i> Profil</a></li>
            <li><a href="#" class="active"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard_transaksi.php"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="dashboard_pengaturan.php"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <!-- Welcome Header -->      
        <h1 class="welcome-text"><?= $greeting ?>, <?= htmlspecialchars($name) ?>!</h1>
        
        <!-- Account Information Card -->
        <div class="dashboard-section">
            <h2 class="section-title" style="color:#1976d2;font-size:1.15rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                <i class="fa fa-info-circle"></i>
                Informasi Rekening
            </h2>
            <div class="account-info-card<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?> account-info-card-prioritas<?php endif; ?>">
                <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                <div style="position:absolute;top:-18px;right:18px;z-index:2;background:#FFD700;padding:4px 18px;border-radius:18px;font-size:0.98rem;font-weight:700;box-shadow:0 2px 8px rgba(255,215,0,0.25);color:#222;display:flex;align-items:center;gap:7px;"><i class='fa fa-crown' style='color:#fff700;font-size:1.1rem;'></i> PRIORITAS</div>
                <?php endif; ?>
                <div class="account-header">
                    <h3><i class="fa fa-credit-card"></i> Detail Rekening Anda</h3>
                </div>
                <div class="account-details" style="display:flex;align-items:flex-start;gap:2.5rem;">
                    <div style="flex:1;min-width:180px;">
                        <div class="account-item">
                            <span class="account-label">Nama Pemegang:</span>
                            <span class="account-value"><?= htmlspecialchars($name) ?></span>
                        </div>
                        <div class="account-item">
                            <span class="account-label">Nomor Rekening:</span>
                            <span class="account-value" id="account-number-value" style="user-select:all;"><?= htmlspecialchars($account_number) ?></span>
                            <button id="copy-account-btn" title="Salin Nomor Rekening" style="background:none;border:none;cursor:pointer;margin-left:8px;vertical-align:middle;outline:none;"><i class="fa fa-copy" id="copy-account-icon" style="color:#555;font-size:1.15em;"></i></button>
                            <span id="copy-toast" style="display:none;margin-left:10px;color:#ff9800;font-size:0.98em;font-weight:600;vertical-align:middle;">Nomor rekening disalin!</span>
                        </div>
                        <div class="account-item">
                            <span class="account-label">Saldo Aktif:</span>
                            <span class="account-value balance">Rp <?= $formatted_balance ?></span>
                        </div>
                        <div class="account-item">
                            <span class="account-label">Status Rekening:</span>
                            <span class="account-value status-active">Aktif & Terverifikasi</span>
                        </div>
                        <div class="account-item">
                            <span class="account-label">Terakhir Login:</span>
                            <span class="account-value"><?= $formatted_last_login ?> WIB</span>
                        </div>
                        <div class="account-item" style="display:flex;align-items:center;gap:10px;">
                            <span class="account-label">Kategori Nasabah:</span>
                            <span class="account-value" style="display:flex;align-items:center;gap:7px;">
                                <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'non-prioritas'): ?>
                                    <i class="fa fa-star" style="color:#ffc107;cursor:pointer;" title="Upgrade ke Prioritas" onclick="document.getElementById('btn-upgrade-prioritas')?.click()"></i>
                                    Non-prioritas
                                <?php elseif (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                                    <i class="fa fa-star" style="color:#43A047;"></i>
                                    Prioritas
                                <?php else: ?>
                                    Non-prioritas
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div style="text-align:center;min-width:130px;display:flex;flex-direction:column;align-items:center;gap:0.3rem;">
                        <div id="rekening-qrcode" style="display:inline-block;width:130px;height:130px;background:#fff;border-radius:10px;border:2px solid #e3e7ed;cursor:pointer;padding:7px;box-sizing:border-box;display:flex;align-items:center;justify-content:center;" title="Klik untuk unduh QR"></div>
                        <div style="font-size:0.95rem;color:#fff;margin-top:0.15rem;font-weight:600;">QR Rekening</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-section">
            <div class="action-buttons">
                <button class="action-btn action-btn-horizontal" onclick="showTransferPopup()">
                    <div class="action-btn-icon">
                        <i class="fa fa-exchange-alt"></i>
                    </div>
                    <div class="action-btn-text-group">
                        <div class="action-btn-title">Transfer Saldo</div>
                        <div class="action-btn-desc">Kirim uang ke rekening lain dengan cepat dan aman</div>
                    </div>
                </button>
                <button class="action-btn action-btn-horizontal" onclick="showTopupSaldoPopup()">
                    <div class="action-btn-icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="action-btn-text-group">
                        <div class="action-btn-title">Top-up Saldo</div>
                        <div class="action-btn-desc">Tambah saldo ke rekening Anda dengan mudah</div>
                    </div>
                </button>
                <button class="action-btn qr-scan-btn" onclick="showQrScanPopup()">
                  <div class="qr-scan-icon"><i class="fa fa-qrcode"></i></div>
                  <div class="qr-scan-content">
                    <div class="qr-scan-title">Scan QR Code</div>
                    <div class="qr-scan-desc">Bayar dengan mudah menggunakan QR code</div>
                  </div>
                </button>
            </div>
        </div>
        
        <!-- Menu Top Up -->
        <div class="menu-section" style="background:linear-gradient(90deg,#1976d2 0%,#1976d2 30%,#2196f3 100%);color:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(25,118,210,0.07);padding:1.5rem 1.2rem 1.2rem 1.2rem;margin-bottom:2.2rem;border:1px solid #1565c0;">
            <div class="menu-section-title" style="color:#fff;">Top Up</div>
            <div class="menu-grid">
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=ewallet'" style="color:#fff;">
                    <div class="menu-icon orange" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-wallet"></i></div>
                    <div class="menu-label" style="color:#fff;">Top Up E-Wallet</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=pulsa'" style="color:#fff;">
                    <div class="menu-icon green" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-mobile-alt"></i></div>
                    <div class="menu-label" style="color:#fff;">Pulsa</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=data'" style="color:#fff;">
                    <div class="menu-icon blue" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-wifi"></i></div>
                    <div class="menu-label" style="color:#fff;">Data</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=game'" style="color:#fff;">
                    <div class="menu-icon purple" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-gamepad"></i></div>
                    <div class="menu-label" style="color:#fff;">Top Up Game</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=gplay'" style="color:#fff;">
                    <div class="menu-icon green" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-play"></i></div>
                    <div class="menu-label" style="color:#fff;">Saldo Google Play</div>
                </div>
            </div>
        </div>
        <!-- Menu Tagihan -->
        <div class="menu-section" style="background:linear-gradient(90deg,#1976d2 0%,#1976d2 30%,#2196f3 100%);color:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(25,118,210,0.07);padding:1.5rem 1.2rem 1.2rem 1.2rem;margin-bottom:2.2rem;border:1px solid #1565c0;">
            <div class="menu-section-title" style="color:#fff;">Tagihan</div>
            <div class="menu-grid">
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=pln'" style="color:#fff;">
                    <div class="menu-icon orange" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-bolt"></i></div>
                    <div class="menu-label" style="color:#fff;">PLN</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=tv'" style="color:#fff;">
                    <div class="menu-icon blue" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-satellite-dish"></i></div>
                    <div class="menu-label" style="color:#fff;">TV Kabel & Internet</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=bpjs'" style="color:#fff;">
                    <div class="menu-icon orange" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-id-card"></i></div>
                    <div class="menu-label" style="color:#fff;">BPJS</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=pdam'" style="color:#fff;">
                    <div class="menu-icon blue" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-tint"></i></div>
                    <div class="menu-label" style="color:#fff;">PDAM</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=edukasi'" style="color:#fff;">
                    <div class="menu-icon blue" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-graduation-cap"></i></div>
                    <div class="menu-label" style="color:#fff;">Edukasi</div>
                </div>
                <div class="menu-item" onclick="window.location.href='dashboard_transaksi.php?tab=donasi'" style="color:#fff;">
                    <div class="menu-icon green" style="background:#fff1;border:2px solid #fff;color:#fff;"><i class="fa fa-hand-holding-heart"></i></div>
                    <div class="menu-label" style="color:#fff;">Donasi & Zakat</div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- History Popup -->
<div id="history-popup" class="ewallet-popup-overlay" style="display:none;z-index:21000;">
  <div class="ewallet-popup-modal" style="max-width:580px;">
    <div class="ewallet-popup-header">
      <span class="ewallet-popup-title">Riwayat</span>
      <button class="ewallet-popup-close" onclick="closeHistoryPopup()">&times;</button>
    </div>
    <div style="display:flex;gap:1rem;margin-bottom:1.2rem;">
      <button id="tab-topup" onclick="showHistoryTab('topup')" style="flex:1;padding:0.7rem 0;font-size:1.05rem;border-radius:8px;border:none;cursor:pointer;font-weight:700;background:#1976d2;color:#fff;">Top Up</button>
      <button id="tab-transfer" onclick="showHistoryTab('transfer')" style="flex:1;padding:0.7rem 0;font-size:1.05rem;border-radius:8px;border:none;cursor:pointer;font-weight:700;background:#e3e7ed;color:#1976d2;">Transfer</button>
    </div>
    <div id="history-list">
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="background:#f4f6f8;color:#1976d2;font-size:1rem;">
            <th style="padding:0.7rem 0.3rem;text-align:left;">Tanggal</th>
            <th style="padding:0.7rem 0.3rem;text-align:left;">e-Wallet</th>
            <th style="padding:0.7rem 0.3rem;text-align:left;">Rekening</th>
            <th style="padding:0.7rem 0.3rem;text-align:right;">Nominal</th>
            <th style="padding:0.7rem 0.3rem;text-align:left;">Ulasan</th>
            <th style="padding:0.7rem 0.3rem;text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody id="history-table-body"></tbody>
      </table>
      <div id="history-empty" style="text-align:center;color:#888;margin-top:1.5rem;display:none;">Belum ada riwayat.</div>
      <button id="btn-cetak-history" onclick="printHistoryTable()" style="margin-top:1.5rem;width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Cetak</button>
    </div>
  </div>
</div>

<!-- Transfer Popup -->
<div id="transfer-popup" class="ewallet-popup-overlay" style="display:none;z-index:21000;">
  <div class="ewallet-popup-modal" style="max-width:420px;">
    <div class="ewallet-popup-header">
      <span class="ewallet-popup-title" style="color:#1976d2;">Transfer Saldo</span>
      <button class="ewallet-popup-close" onclick="closeTransferPopup()">&times;</button>
    </div>
    <div id="receiver-list-section">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <span style="font-weight:600;font-size:1.08rem;color:#1976d2;">Pilih Penerima</span>
        <button onclick="showAddReceiverForm()" style="background:none;border:none;color:#1976d2;font-size:1.1rem;display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-weight:600;"><span style="font-size:1.5rem;">&#43;</span> TAMBAH PENERIMA</button>
      </div>
      <div id="receiver-list"></div>
    </div>
    <div id="add-receiver-section" style="display:none;">
      <form id="add-receiver-form" onsubmit="submitAddReceiver(event)">
        <div style="margin-bottom:1rem;">
          <label style="color:#1976d2;font-weight:600;">Nama Penerima</label><br>
          <input type="text" id="receiver-name" name="name" required style="width:100%;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;outline:none;transition:border 0.2s;">
        </div>
        <div style="margin-bottom:1rem;">
          <label style="color:#1976d2;font-weight:600;">Nomor Rekening</label><br>
          <input type="text" id="receiver-account" name="account_number" required style="width:100%;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;outline:none;transition:border 0.2s;">
        </div>
        <button type="submit" style="width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Simpan Penerima</button>
        <button type="button" onclick="hideAddReceiverForm()" style="width:100%;margin-top:0.7rem;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;">Batal</button>
      </form>
    </div>
    <div id="transfer-form-section" style="display:none;"></div>
  </div>
</div>

<!-- QR Scan Popup -->
<div id="qrscan-popup" style="display:none;flex-direction:column;align-items:center;z-index:99999;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);justify-content:center;">
  <div style="background:#fff;border-radius:16px;max-width:340px;width:95vw;padding:2rem 1.5rem;box-shadow:0 8px 32px rgba(25,118,210,0.18);text-align:center;position:relative;">
    <button onclick="closeQrScanPopup()" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;"><i class="fa fa-times"></i></button>
    <div style="font-size:1.3rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;"><i class="fa fa-qrcode"></i> Scan QR Code</div>
    <div id="qrscan-camera-preview" style="display:none;margin-bottom:1rem;"></div>
    <input id="qrscan-result" type="text" readonly value="Hasil scan QR akan muncul di sini" style="width:90%;margin-bottom:1rem;text-align:center;background:#f8fafc;border-radius:8px;border:1.5px solid #e3e7ed;padding:0.7rem 1rem;">
    <input id="qrscan-file" type="file" accept="image/*" style="display:none;">
    <div style="display:flex;gap:0.5rem;justify-content:center;margin-bottom:1rem;">
      <button onclick="document.getElementById('qrscan-file').click()" style="background:#e3f2fd;color:#1976d2;font-weight:700;padding:0.8rem 1.5rem;border-radius:10px;border:none;cursor:pointer;">Ambil dari Galeri</button>
      <button id="btn-scan-camera" onclick="startQrCameraScan()" style="background:#1976d2;color:#fff;font-weight:700;padding:0.8rem 1.5rem;border-radius:10px;border:none;cursor:pointer;">Scan dari Kamera</button>
      <button id="btn-stop-camera" onclick="stopQrCameraScan()" style="background:#e53e3e;color:#fff;font-weight:700;padding:0.8rem 1.5rem;border-radius:10px;border:none;cursor:pointer;display:none;">Stop Kamera</button>
    </div>
  </div>
</div>

<!-- Tambahkan tombol aksi di bawah hasil scan QR -->
<div id="qr-action-buttons" style="display:none;flex-direction:column;gap:0.7rem;margin-top:1rem;">
  <button id="btn-transfer-qr" class="qr-action-btn">Transfer Saldo ke Rekening Ini</button>
  <button id="btn-topup-qr" class="qr-action-btn">Top Up ke Rekening Ini</button>
  <button id="btn-tagihan-qr" class="qr-action-btn">Bayar Tagihan ke Rekening Ini</button>
</div>

<footer class="footer dashboard-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Bank FTI. Semua hak dilindungi undang-undang.</p>
        <p class="footer-note">
            Dibuat dengan <i class="fas fa-heart"></i> untuk Fakultas Teknologi Informasi
        </p>
    </div>
</footer>

<!-- Modal Rating Bintang -->
<div id="rating-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:99999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:16px;max-width:350px;width:90vw;padding:2rem 1.2rem;box-shadow:0 8px 32px rgba(0,0,0,0.18);position:relative;text-align:center;">
    <div style="font-size:1.25rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;">Beri Rating Transaksi</div>
    <div id="rating-stars" style="font-size:2.2rem;margin-bottom:1.2rem;">
      <span class="star" data-value="1">☆</span>
      <span class="star" data-value="2">☆</span>
      <span class="star" data-value="3">☆</span>
      <span class="star" data-value="4">☆</span>
      <span class="star" data-value="5">☆</span>
    </div>
    <textarea id="rating-review" placeholder="Tulis ulasan (opsional)" style="width:100%;min-height:60px;border-radius:8px;border:1.5px solid #e3e7ed;padding:0.7rem 1rem;margin-bottom:1.2rem;"></textarea>
    <button id="submit-rating-btn" style="width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Kirim</button>
    <button onclick="closeRatingModal()" style="width:100%;margin-top:0.7rem;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;">Batal</button>
  </div>
</div>

<div style="text-align:center;margin-top:1.2rem;">
<?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'non-prioritas'): ?>
    <button id="btn-upgrade-prioritas" style="padding:0.8rem 2.2rem;font-size:1.1rem;border-radius:8px;background:linear-gradient(90deg,#1976d2,#42a5f5);color:#fff;font-weight:700;border:none;box-shadow:0 2px 8px rgba(25,118,210,0.10);cursor:pointer;">Upgrade ke Prioritas</button>
    <div id="upgrade-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:99999;align-items:center;justify-content:center;">
      <div style="background:#fff;border-radius:16px;max-width:400px;width:90vw;padding:2.2rem 1.2rem;box-shadow:0 8px 32px rgba(0,0,0,0.18);text-align:center;position:relative;">
        <div style='font-size:2.1rem;color:#1976d2;margin-bottom:0.7rem;'><i class='fa fa-star'></i></div>
        <div style='font-size:1.25rem;font-weight:700;color:#1976d2;margin-bottom:0.5rem;'>Upgrade ke Nasabah Prioritas</div>
        <div style='color:#444;margin-bottom:1.2rem;'>Silakan transfer <b>Rp 25.000</b> ke rekening teller berikut untuk upgrade:<br><br><b>+62 30100000002 a.n. Teller Bank</b><br><br>Setelah transfer, klik tombol <b>Upgrade</b> di bawah untuk melanjutkan proses otomatis.</div>
        <button id="btn-upgrade-bayar" style="width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Upgrade</button>
        <button onclick="document.getElementById('upgrade-modal').style.display='none'" style="width:100%;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;margin-top:0.7rem;">Batal</button>
        <div id="upgrade-status" style="margin-top:1rem;font-size:1.05rem;color:#1976d2;"></div>
      </div>
    </div>
<?php endif; ?>
</div>

<div id="ewallet-popup-overlay" class="ewallet-popup-overlay" style="display:none;position:fixed;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:20000;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:18px;max-width:350px;width:90vw;padding:2rem 1.2rem;box-shadow:0 8px 32px rgba(25,118,210,0.18);position:relative;">
    <button onclick="document.getElementById('ewallet-popup-overlay').style.display='none'" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;"><i class="fa fa-times"></i></button>
    <div style="font-size:1.2rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;text-align:center;">Top Up E-Wallet</div>
    <form id="ewallet-topup-form" onsubmit="submitEwalletTopup(event)">
      <div style="margin-bottom:1rem;">
        <label for="ewallet-select" style="font-weight:600;color:#1976d2;">Pilih E-Wallet</label>
        <select id="ewallet-select" name="ewallet" required style="width:100%;padding:0.6rem 0.5rem;border-radius:8px;border:1px solid #b0bec5;font-size:1rem;" onchange="toggleEwalletRekInput()">
          <option value="">-- Pilih --</option>
          <option value="BANK FTI">Bank FTI</option>
          <option value="OVO">OVO</option>
          <option value="GOPAY">GOPAY</option>
          <option value="DANA">DANA</option>
          <option value="SHOPEEPAY">ShopeePay</option>
          <option value="LINKAJA">LinkAja</option>
        </select>
      </div>
      <div id="ewallet-rek-group" style="margin-bottom:1rem;display:none;">
        <label for="ewallet-rek" style="font-weight:600;color:#1976d2;">No. Rekening e-Wallet</label>
        <input type="text" id="ewallet-rek" name="rekening" placeholder="Masukkan nomor rekening e-wallet" style="width:100%;padding:0.6rem 0.5rem;border-radius:8px;border:1px solid #b0bec5;font-size:1rem;">
      </div>
      <div style="margin-bottom:1rem;">
        <label for="ewallet-nominal" style="font-weight:600;color:#1976d2;">Nominal</label>
        <input type="number" id="ewallet-nominal" name="nominal" min="10000" step="1000" required style="width:100%;padding:0.6rem 0.5rem;border-radius:8px;border:1px solid #b0bec5;font-size:1rem;">
      </div>
      <button type="submit" style="width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Top Up</button>
    </form>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="assets/js/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
(function() {
  try {
    var tema = localStorage.getItem('tema') || 'light';
    if (tema === 'dark') {
      document.documentElement.classList.add('dark-theme');
      document.body.classList.add('dark-theme');
    }
  } catch(e) {}
})();

let ratingTargetId = null;
let ratingTargetType = null;
let selectedRating = 0;

function showRatingModal(id, type) {
  ratingTargetId = id;
  ratingTargetType = type;
  selectedRating = 0;
  document.getElementById('rating-review').value = '';
  document.querySelectorAll('#rating-stars .star').forEach(star => {
    star.textContent = '☆';
    star.style.color = '#888';
    star.onclick = function() {
      selectedRating = parseInt(this.getAttribute('data-value'));
      document.querySelectorAll('#rating-stars .star').forEach((s, idx) => {
        s.textContent = idx < selectedRating ? '★' : '☆';
        s.style.color = idx < selectedRating ? '#FFD600' : '#888';
      });
    };
  });
  document.getElementById('rating-modal').style.display = 'flex';
}
function closeRatingModal() {
  document.getElementById('rating-modal').style.display = 'none';
}
document.getElementById('submit-rating-btn').onclick = function() {
  if (!selectedRating) {
    alert('Pilih rating bintang!');
    return;
  }
  const review = document.getElementById('rating-review').value;
  const url = ratingTargetType === 'topup' ? '../topup.php' : '../transfer.php';
  const idField = ratingTargetType === 'topup' ? 'topup_id' : 'transfer_id';
  const formData = `${idField}=${ratingTargetId}&rating=${selectedRating}&review=${encodeURIComponent(review)}`;
  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Terima kasih atas rating Anda!');
        closeRatingModal();
        showHistoryTab(ratingTargetType);
      } else {
        alert(data.message || 'Gagal menyimpan rating!');
      }
    })
    .catch(() => {
      alert('Terjadi kesalahan.');
    });
};

function showTransferPopup(rek) {
  document.getElementById('transfer-popup').style.display = 'flex';
  loadReceivers();
  document.getElementById('receiver-list-section').style.display = 'block';
  document.getElementById('add-receiver-section').style.display = 'none';
  document.getElementById('transfer-form-section').style.display = 'none';
  if (rek) {
    setTimeout(function() {
      var input = document.querySelector('#transfer-form input[name=account_number]');
      if (input) input.value = rek;
    }, 300);
  }
}
function submitTransfer(event) {
  event.preventDefault();
  const form = document.getElementById('transfer-form');
  const receiver_id = form.querySelector('[name=receiver_id]').value;
  const amount = form.querySelector('[name=amount]').value;
  const note = form.querySelector('[name=note]') ? form.querySelector('[name=note]').value : '';
  if (!receiver_id || !amount) {
    alert('Mohon lengkapi semua data!');
    return;
  }
  const btn = form.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.textContent = 'Memproses...';
  fetch('../transfer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `receiver_id=${encodeURIComponent(receiver_id)}&amount=${encodeURIComponent(amount)}&note=${encodeURIComponent(note)}`
  })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'Transfer';
      if (data.success) {
        document.getElementById('transfer-popup').style.display = 'none';
        showRatingModal(data.transfer_id, 'transfer');
      } else {
        alert(data.message || 'Transfer gagal!');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Transfer';
      alert('Terjadi kesalahan.');
    });
}
function loadReceivers() {
  fetch('../get_receivers.php')
    .then(res => res.json())
    .then(data => {
      const receiverList = document.getElementById('receiver-list');
      if (data.length === 0) {
        receiverList.innerHTML = '<div style="text-align:center;color:#888;padding:1rem;">Belum ada penerima. Silakan tambah penerima baru.</div>';
        return;
      }
      receiverList.innerHTML = data.map(receiver => `
        <div class="receiver-item" onclick="selectReceiver(${receiver.id}, '${receiver.name}', '${receiver.account_number}')" style="display:flex;align-items:center;justify-content:space-between;padding:0.8rem;border:1px solid #e3e7ed;border-radius:8px;margin-bottom:0.5rem;cursor:pointer;transition:background 0.2s;">
          <div>
            <div style="font-weight:600;color:#222;">${receiver.name}</div>
            <div style="color:#666;font-size:0.9rem;">${receiver.account_number}</div>
          </div>
          <button onclick="event.stopPropagation();deleteReceiver(${receiver.id})" style="background:none;border:none;color:#f44336;cursor:pointer;font-size:1.1rem;">×</button>
        </div>
      `).join('');
    })
    .catch(() => {
      document.getElementById('receiver-list').innerHTML = '<div style="text-align:center;color:#888;padding:1rem;">Gagal memuat daftar penerima.</div>';
    });
}
function closeTransferPopup() {
  document.getElementById('transfer-popup').style.display = 'none';
  document.getElementById('receiver-list-section').style.display = 'block';
  document.getElementById('add-receiver-section').style.display = 'none';
  document.getElementById('transfer-form-section').style.display = 'none';
}
function showHistoryTab(tab) {
  document.getElementById('tab-topup').style.background = tab === 'topup' ? '#1976d2' : '#e3e7ed';
  document.getElementById('tab-topup').style.color = tab === 'topup' ? '#fff' : '#1976d2';
  document.getElementById('tab-transfer').style.background = tab === 'transfer' ? '#1976d2' : '#e3e7ed';
  document.getElementById('tab-transfer').style.color = tab === 'transfer' ? '#fff' : '#1976d2';
  
  fetch(`../get_history.php?type=${tab}`)
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('history-table-body');
      const empty = document.getElementById('history-empty');
      tbody.innerHTML = '';
      
      if (data.length === 0) {
        empty.style.display = 'block';
        return;
      }
      
      empty.style.display = 'none';
      data.forEach(item => {
        const tr = document.createElement('tr');
        let stars = '';
        if (item.rating && item.rating > 0) {
          for (let i = 1; i <= 5; i++) {
            stars += `<span style='color:${i <= item.rating ? '#FFD600' : '#ccc'};font-size:1.1em;'>${i <= item.rating ? '★' : '☆'}</span>`;
          }
        }
        const reviewValue = (item.review && item.review.trim() !== "" && item.review !== "-") ? item.review : null;
        const reviewButton = reviewValue
          ? `<span style=\"color:#666;font-size:0.9rem;\">${reviewValue}</span>`
          : `<button onclick=\"showUlasanPopup(${item.id}, '${tab}')\" style=\"background:none;border:none;color:#1976d2;cursor:pointer;font-size:0.9rem;\">Tambah Ulasan</button>`;
        tr.innerHTML = `
          <td style=\"padding:0.7rem 0.3rem;\">${item.tanggal}</td>
          <td style=\"padding:0.7rem 0.3rem;\">${item.ewallet}</td>
          <td style=\"padding:0.7rem 0.3rem;\">${item.rekening}</td>
          <td style=\"padding:0.7rem 0.3rem;text-align:right;\">Rp ${parseInt(item.nominal).toLocaleString('id-ID')}</td>
          <td style=\"padding:0.7rem 0.3rem;\">${stars} ${reviewButton}</td>
          <td style=\"padding:0.7rem 0.3rem;text-align:center;\">
            <button onclick=\"showDetailModal(${item.id}, '${tab}')\" style=\"background:none;border:none;color:#1976d2;cursor:pointer;font-size:1.1rem;\" title=\"Lihat Detail\"><i class=\"fa fa-eye\"></i></button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(() => {
      document.getElementById('history-empty').style.display = 'block';
    });
}
function selectReceiver(receiverId, name, accountNumber) {
  const transferFormSection = document.getElementById('transfer-form-section');
  transferFormSection.innerHTML = `
    <form id="transfer-form" onsubmit="submitTransfer(event)">
      <div style="margin-bottom:1rem;">
        <label style="color:#1976d2;font-weight:600;">Penerima</label><br>
        <input type="text" value="${name} (${accountNumber})" readonly style="width:100%;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;">
        <input type="hidden" name="receiver_id" value="${receiverId}">
      </div>
      <div style="margin-bottom:1rem;">
        <label style="color:#1976d2;font-weight:600;">Nominal Transfer</label><br>
        <input type="number" name="amount" min="1000" step="1000" required placeholder="Masukkan nominal transfer" style="width:100%;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;outline:none;transition:border 0.2s;">
      </div>
      <div style="margin-bottom:1rem;">
        <label style="color:#1976d2;font-weight:600;">Catatan (Opsional)</label><br>
        <input type="text" name="note" placeholder="Catatan transfer" style="width:100%;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;outline:none;transition:border 0.2s;">
      </div>
      <button type="submit" style="width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Transfer</button>
      <button type="button" onclick="backToReceiverList()" style="width:100%;margin-top:0.7rem;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;">Kembali</button>
    </form>
  `;
  document.getElementById('receiver-list-section').style.display = 'none';
  transferFormSection.style.display = 'block';
}
function selectEwallet(ewallet) {
  document.getElementById('ewallet-detail-name').value = ewallet;
  document.getElementById('ewallet-popup').style.display = 'none';
  document.getElementById('ewallet-detail-popup').style.display = 'flex';
  setTimeout(function() {
    var rekInput = document.getElementById('ewallet-detail-user');
    if (rekInput) {
      if (ewallet === 'BANK FTI') {
        rekInput.value = '<?php echo htmlspecialchars($account_number); ?>';
        rekInput.readOnly = true;
      } else {
        rekInput.value = '';
        rekInput.readOnly = false;
      }
    }
    rekInput?.focus();
  }, 200);
}
document.querySelectorAll('.ewallet-item[data-ewallet]').forEach(function(item) {
  item.onclick = function() {
    selectEwallet('BANK FTI');
  };
});
function closeEwalletDetailPopup() {
  document.getElementById('ewallet-detail-popup').style.display = 'none';
}
function closeEwalletPopup() {
  document.getElementById('ewallet-popup').style.display = 'none';
}
function showAddReceiverForm() {
  document.getElementById('receiver-list-section').style.display = 'none';
  document.getElementById('add-receiver-section').style.display = 'block';
}

function hideAddReceiverForm() {
  document.getElementById('add-receiver-section').style.display = 'none';
  document.getElementById('receiver-list-section').style.display = 'block';
  // Reset form
  document.getElementById('add-receiver-form').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('btn-upgrade-prioritas');
    if (btn) {
        btn.onclick = function() {
            document.getElementById('upgrade-modal').style.display = 'flex';
        };
    }
    var star = document.querySelector('.fa-star[title="Upgrade ke Prioritas"]');
    if (star) {
        star.onclick = function() {
            document.getElementById('btn-upgrade-prioritas')?.click();
        };
    }
    var bayarBtn = document.getElementById('btn-upgrade-bayar');
    if (bayarBtn) {
        bayarBtn.onclick = function() {
            document.getElementById('upgrade-modal').style.display = 'none';
            // Buka popup transfer otomatis ke rekening +62 30100000002
            showTransferPopup('+62 30100000002');
            setTimeout(function() {
                // Isi nominal otomatis jika form sudah muncul
                var inputRek = document.querySelector('#transfer-form input[name=account_number]');
                var inputAmount = document.querySelector('#transfer-form input[name=amount]');
                if (inputRek) inputRek.value = '+62 30100000002';
                if (inputAmount) inputAmount.value = 25000;
            }, 400);
        };
    }
    var copyBtn = document.getElementById('copy-account-btn');
    var copyIcon = document.getElementById('copy-account-icon');
    var toast = document.getElementById('copy-toast');
    if (copyBtn) {
        copyBtn.onclick = function() {
            var accNum = document.getElementById('account-number-value').textContent.trim();
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

function deleteReceiver(id) {
  if (!confirm('Yakin ingin menghapus penerima ini?')) return;
  fetch('../delete_receiver.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + encodeURIComponent(id)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        loadReceivers();
      } else {
        alert(data.message || 'Gagal menghapus penerima!');
      }
    })
    .catch(() => {
      alert('Terjadi kesalahan saat menghapus penerima!');
    });
}

function submitAddReceiver(event) {
  event.preventDefault();
  const form = document.getElementById('add-receiver-form');
  const name = document.getElementById('receiver-name').value.trim();
  const account = document.getElementById('receiver-account').value.trim();
  if (!name || !account) {
    alert('Nama dan nomor rekening wajib diisi!');
    return;
  }
  const btn = form.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.textContent = 'Menyimpan...';
  fetch('../add_receiver.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `name=${encodeURIComponent(name)}&account_number=${encodeURIComponent(account)}`
  })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'Simpan Penerima';
      if (data.success) {
        document.getElementById('add-receiver-section').style.display = 'none';
        document.getElementById('receiver-list-section').style.display = 'block';
        loadReceivers();
        form.reset();
      } else {
        alert(data.message || 'Gagal menyimpan penerima!');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Simpan Penerima';
      alert('Terjadi kesalahan saat menyimpan penerima!');
    });
}

function showComingSoon() {
  alert('Fitur ini segera hadir!');
}

function showQrScanPopup() {
  var popup = document.getElementById('qrscan-popup');
  if (popup) popup.style.display = 'flex';
  var resultInput = document.getElementById('qrscan-result');
  if (resultInput) {
    resultInput.value = 'Hasil scan QR akan muncul di sini';
    setTimeout(function() { resultInput.focus(); }, 200);
  }
  var actionBtns = document.getElementById('qr-action-buttons');
  if (actionBtns) actionBtns.style.display = 'none';
}

// Event: jika hasil scan QR diinput/diubah
var qrResultInput = document.getElementById('qr-result');
if (qrResultInput) {
  qrResultInput.addEventListener('input', function() {
    var rekening = qrResultInput.value.trim();
    if (rekening.length >= 8) { // asumsi minimal 8 digit rekening
      document.getElementById('qr-action-buttons').style.display = 'flex';
      // Simpan rekening hasil scan ke data attribute tombol
      document.getElementById('btn-transfer-qr').setAttribute('data-rekening', rekening);
      document.getElementById('btn-topup-qr').setAttribute('data-rekening', rekening);
      document.getElementById('btn-tagihan-qr').setAttribute('data-rekening', rekening);
    } else {
      document.getElementById('qr-action-buttons').style.display = 'none';
    }
  });
}

// Event: klik Transfer Saldo ke Rekening Ini
var btnTransferQr = document.getElementById('btn-transfer-qr');
if (btnTransferQr) {
  btnTransferQr.onclick = function() {
    var rekening = this.getAttribute('data-rekening');
    if (rekening) {
      document.getElementById('qrscan-popup').style.display = 'none';
      showTransferPopup(rekening);
    }
  };
}
// Event: klik Top Up ke Rekening Ini (bisa diarahkan ke halaman topup dengan rekening tujuan)
var btnTopupQr = document.getElementById('btn-topup-qr');
if (btnTopupQr) {
  btnTopupQr.onclick = function() {
    var rekening = this.getAttribute('data-rekening');
    if (rekening) {
      window.location.href = 'dashboard_transaksi.php?tab=ewallet&rekening=' + encodeURIComponent(rekening);
    }
  };
}
// Event: klik Bayar Tagihan ke Rekening Ini (bisa diarahkan ke halaman tagihan dengan rekening tujuan)
var btnTagihanQr = document.getElementById('btn-tagihan-qr');
if (btnTagihanQr) {
  btnTagihanQr.onclick = function() {
    var rekening = this.getAttribute('data-rekening');
    if (rekening) {
      window.location.href = 'dashboard_transaksi.php?tab=tagihan&rekening=' + encodeURIComponent(rekening);
    }
  };
}

function toggleEwalletRekInput() {
  var ewallet = document.getElementById('ewallet-select').value;
  var rekGroup = document.getElementById('ewallet-rek-group');
  if (ewallet && ewallet !== 'BANK FTI') {
    rekGroup.style.display = 'block';
  } else {
    rekGroup.style.display = 'none';
    document.getElementById('ewallet-rek').value = '';
  }
}

function submitEwalletTopup(event) {
  event.preventDefault();
  var ewallet = document.getElementById('ewallet-select').value;
  var rekening = document.getElementById('ewallet-rek').value;
  var nominal = document.getElementById('ewallet-nominal').value;
  if (!ewallet || !nominal || (ewallet !== 'BANK FTI' && !rekening)) {
    alert('Mohon lengkapi semua data!');
    return;
  }
  var btn = event.target.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.textContent = 'Memproses...';
  fetch('../topup.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `ewallet=${encodeURIComponent(ewallet)}&rekening=${encodeURIComponent(rekening)}&amount=${encodeURIComponent(nominal)}`
  })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'Top Up';
      if (data.success) {
        document.getElementById('ewallet-popup-overlay').style.display = 'none';
        if (data.new_balance !== undefined) {
          var saldoEls = document.querySelectorAll('.balance');
          saldoEls.forEach(function(el) { el.textContent = 'Rp ' + Number(data.new_balance).toLocaleString('id-ID'); });
        }
        showRatingModal(data.topup_id, 'topup');
      } else {
        alert(data.message || 'Top up gagal!');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Top Up';
      alert('Terjadi kesalahan.');
    });
}

document.getElementById('ewallet-select').addEventListener('change', toggleEwalletRekInput);

function showTopupReviewModal(topup_id) {
  var modal = document.createElement('div');
  modal.id = 'topup-review-modal';
  modal.style = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:99999;display:flex;align-items:center;justify-content:center;';
  modal.innerHTML = `
    <div style="background:#fff;border-radius:16px;max-width:350px;width:90vw;padding:2.2rem 1.2rem;box-shadow:0 8px 32px rgba(25,118,210,0.18);text-align:center;position:relative;">
      <button onclick="document.body.removeChild(document.getElementById('topup-review-modal'))" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;"><i class='fa fa-times'></i></button>
      <div style='font-size:1.2rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;'>Beri Ulasan Top Up</div>
      <form id='topup-review-form'>
        <div style='margin-bottom:1rem;'>
          <label style='font-weight:600;color:#1976d2;'>Rating</label><br>
          <span id='topup-rating-stars'>
            <i class='fa fa-star-o' data-val='1'></i>
            <i class='fa fa-star-o' data-val='2'></i>
            <i class='fa fa-star-o' data-val='3'></i>
            <i class='fa fa-star-o' data-val='4'></i>
            <i class='fa fa-star-o' data-val='5'></i>
          </span>
        </div>
        <div style='margin-bottom:1rem;'>
          <label style='font-weight:600;color:#1976d2;'>Ulasan</label>
          <textarea id='topup-review-text' style='width:100%;min-height:60px;border-radius:8px;border:1px solid #b0bec5;padding:0.5rem;font-size:1rem;'></textarea>
        </div>
        <button type='submit' style='width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;'>Kirim Ulasan</button>
      </form>
    </div>
  `;
  document.body.appendChild(modal);
  // Bintang rating interaktif
  var stars = modal.querySelectorAll('#topup-rating-stars i');
  var rating = 0;
  stars.forEach(function(star) {
    star.onclick = function() {
      rating = parseInt(this.getAttribute('data-val'));
      stars.forEach(function(s, idx) {
        s.className = idx < rating ? 'fa fa-star' : 'fa fa-star-o';
      });
    };
  });
  // Submit ulasan
  modal.querySelector('#topup-review-form').onsubmit = function(e) {
    e.preventDefault();
    var review = modal.querySelector('#topup-review-text').value;
    fetch('../topup.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'topup_id=' + encodeURIComponent(topup_id) + '&review=' + encodeURIComponent(review) + '&rating=' + encodeURIComponent(rating)
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Terima kasih atas ulasan Anda!');
          document.body.removeChild(modal);
        } else {
          alert(data.message || 'Gagal menyimpan ulasan!');
        }
      });
  };
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.ewallet-item[data-ewallet]').forEach(function(item) {
    item.onclick = function() {
      selectEwallet(item.getAttribute('data-ewallet'));
    };
  });
});

function showTopupSaldoPopup() {
  document.getElementById('topup-saldo-popup').style.display = 'flex';
}
function closeTopupSaldoPopup() {
  document.getElementById('topup-saldo-popup').style.display = 'none';
}
function submitTopupSaldo(event) {
  event.preventDefault();
  var nominal = document.getElementById('topup-saldo-nominal').value;
  var ewallet = document.getElementById('topup-saldo-ewallet').value;
  var rekening = document.getElementById('topup-saldo-rekening').value;
  var btn = event.target.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.textContent = 'Memproses...';
  fetch('../topup.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `ewallet=${encodeURIComponent(ewallet)}&rekening=${encodeURIComponent(rekening)}&amount=${encodeURIComponent(nominal)}`
  })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'Top Up';
      var result = document.getElementById('topup-saldo-result');
      if (data.success) {
        result.innerHTML = '<span style="color:#2f855a;">Top up berhasil!</span>';
        event.target.reset();
        // Update saldo di UI jika ada elemen saldo
        if (data.new_balance !== undefined) {
          var saldoEls = document.querySelectorAll('.balance');
          saldoEls.forEach(function(el) { el.textContent = 'Rp ' + Number(data.new_balance).toLocaleString('id-ID'); });
        }
        setTimeout(closeTopupSaldoPopup, 1200);
        if (data.topup_id) {
          showRatingModal(data.topup_id, 'topup');
        }
      } else {
        result.innerHTML = '<span style="color:#e53e3e;">' + (data.message || 'Top up gagal!') + '</span>';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Top Up';
      document.getElementById('topup-saldo-result').innerHTML = '<span style="color:#e53e3e;">Terjadi kesalahan.</span>';
    });
}

function closeHistoryPopup() {
  document.getElementById('history-popup').style.display = 'none';
}

function backToReceiverList() {
  document.getElementById('transfer-form-section').style.display = 'none';
  document.getElementById('receiver-list-section').style.display = 'block';
}

function printHistoryTable() {
  var table = document.querySelector('#history-popup table');
  var win = window.open('', '', 'width=800,height=600');
  win.document.write('<html><head><title>Riwayat Transaksi</title></head><body>');
  win.document.write('<h2>Riwayat Transaksi</h2>');
  win.document.write(table.outerHTML);
  win.document.write('</body></html>');
  win.document.close();
  win.focus();
  setTimeout(function(){ win.print(); win.close(); }, 500);
}

function showUlasanPopup(id, type) {
  showRatingModal(id, type);
}

function showDetailModal(id, type) {
  // Fetch detail data berdasarkan ID dan type
  fetch(`../get_history.php?type=${type}&id=${id}`)
    .then(res => res.json())
    .then(data => {
      if (data && data.length > 0) {
        const item = data[0];
        let detailHtml = `
          <div style="background:#fff;border-radius:16px;max-width:400px;width:90vw;padding:2rem 1.2rem;box-shadow:0 8px 32px rgba(25,118,210,0.18);text-align:center;position:relative;">
            <button onclick="this.closest('.modal-overlay').remove()" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;"><i class='fa fa-times'></i></button>
            <div style="font-size:1.2rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;">Detail Transaksi</div>
            <div style="text-align:left;margin-bottom:1rem;">
              <p><strong>Tanggal:</strong> ${item.tanggal}</p>
              <p><strong>e-Wallet:</strong> ${item.ewallet}</p>
              <p><strong>Rekening:</strong> ${item.rekening}</p>
              <p><strong>Nominal:</strong> Rp ${parseInt(item.nominal).toLocaleString('id-ID')}</p>
              ${item.review ? `<p><strong>Ulasan:</strong> ${item.review}</p>` : ''}
              ${item.rating ? `<p><strong>Rating:</strong> ${item.rating}/5 ⭐</p>` : ''}
            </div>
          </div>
        `;
        
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:99999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = detailHtml;
        document.body.appendChild(modal);
        
        modal.addEventListener('click', (e) => {
          if (e.target === modal) {
            modal.remove();
          }
        });
      } else {
        alert('Data tidak ditemukan');
      }
    })
    .catch(() => {
      alert('Gagal memuat detail transaksi');
    });
}

function closeQrScanPopup() {
  document.getElementById('qrscan-popup').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
  var rekening = "<?php echo isset($account_number) ? htmlspecialchars($account_number) : (isset($user_data['account_number']) ? htmlspecialchars($user_data['account_number']) : ''); ?>";
  if (rekening && typeof QRCode !== 'undefined') {
    new QRCode(document.getElementById('rekening-qrcode'), {
      text: rekening,
      width: 110,
      height: 110,
      colorDark: "#1976d2",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
  }
  
  // QR Download functionality
  var rekeningQrCode = document.getElementById('rekening-qrcode');
  if (rekeningQrCode) {
    rekeningQrCode.onclick = function() {
      var qrDiv = document.getElementById('rekening-qrcode');
      var qrCanvas = qrDiv.querySelector('canvas');
      var preview = document.getElementById('qr-download-preview');
      preview.innerHTML = '';
      if (qrCanvas) {
        var clone = qrCanvas.cloneNode(true);
        preview.appendChild(clone);
      }
      document.getElementById('qr-download-popup').style.display = 'flex';
    };
  }
});

function closeQrDownloadPopup() {
  document.getElementById('qr-download-popup').style.display = 'none';
}

function downloadQrPng() {
  var qrUi = document.getElementById('qr-download-ui');
  if (qrUi) {
    html2canvas(qrUi).then(function(canvas) {
      var link = document.createElement('a');
      link.href = canvas.toDataURL('image/png');
      link.download = 'qr_rekening.png';
      link.click();
    });
  }
}

document.getElementById('qrscan-file').onchange = function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(ev) {
    const img = new Image();
    img.onload = function() {
      const canvas = document.createElement('canvas');
      canvas.width = img.width;
      canvas.height = img.height;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, img.width, img.height);
      const imageData = ctx.getImageData(0, 0, img.width, img.height);
      const code = jsQR(imageData.data, img.width, img.height);
      if (code) {
        document.getElementById('qrscan-result').value = code.data;
        handleQrResult(code.data);
      } else {
        // Fallback: coba html5-qrcode
        html5QrDecodeFromImage(ev.target.result, function(qrText) {
          if (qrText) {
            document.getElementById('qrscan-result').value = qrText;
            handleQrResult(qrText);
          } else {
            document.getElementById('qrscan-result').value = 'QR tidak terdeteksi!';
          }
        });
      }
    };
    img.src = ev.target.result;
  };
  reader.readAsDataURL(file);
};

function html5QrDecodeFromImage(dataUrl, callback) {
  // Buat elemen img sementara
  const img = document.createElement('img');
  img.onload = function() {
    const width = img.naturalWidth;
    const height = img.naturalHeight;
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0, width, height);
    const imageData = ctx.getImageData(0, 0, width, height);
    // html5-qrcode decode
    try {
      const qrCodeResult = window.Html5QrcodeScanner ? Html5QrcodeScanner.qrcodeSuccessCallback(imageData) : null;
      if (qrCodeResult && typeof qrCodeResult === 'string') {
        callback(qrCodeResult);
        return;
      }
    } catch (e) {}
    // Fallback: gunakan Html5Qrcode API
    try {
      const html5Qr = new Html5Qrcode(/* dummy id */ 'html5qr-temp');
      html5Qr.decodeImage(canvas)
        .then(decodedText => {
          callback(decodedText);
        })
        .catch(() => {
          callback(null);
        });
    } catch (e) {
      callback(null);
    }
  };
  img.onerror = function() { callback(null); };
  img.src = dataUrl;
}

function handleQrResult(data) {
  // Contoh logika: jika data adalah rekening (10 digit angka), munculkan popup transfer
  if (/^\d{10,}$/.test(data)) {
    showTransferPopup(data);
  } else if (data.startsWith('TAGIHAN:')) {
    // logika tagihan jika ada
  } else {
    // tampilkan pesan atau aksi lain
  }
}

let html5QrCamera = null;
let isCameraScanning = false;
function startQrCameraScan() {
  var preview = document.getElementById('qrscan-camera-preview');
  var btnScan = document.getElementById('btn-scan-camera');
  var btnStop = document.getElementById('btn-stop-camera');
  if (!preview) return;
  preview.style.display = 'block';
  btnScan.style.display = 'none';
  btnStop.style.display = 'inline-block';
  if (!html5QrCamera) {
    html5QrCamera = new Html5Qrcode('qrscan-camera-preview');
  }
  html5QrCamera.start(
    { facingMode: 'environment' },
    { fps: 10, qrbox: 220 },
    qrCodeMessage => {
      document.getElementById('qrscan-result').value = qrCodeMessage;
      handleQrResult(qrCodeMessage);
      stopQrCameraScan();
    },
    errorMessage => {
      // Optional: tampilkan error scanning
    }
  ).then(() => {
    isCameraScanning = true;
  }).catch(err => {
    document.getElementById('qrscan-result').value = 'Kamera tidak tersedia atau akses ditolak!';
    stopQrCameraScan();
  });
}
function stopQrCameraScan() {
  var preview = document.getElementById('qrscan-camera-preview');
  var btnScan = document.getElementById('btn-scan-camera');
  var btnStop = document.getElementById('btn-stop-camera');
  if (preview) preview.style.display = 'none';
  if (btnScan) btnScan.style.display = 'inline-block';
  if (btnStop) btnStop.style.display = 'none';
  if (html5QrCamera && isCameraScanning) {
    html5QrCamera.stop().then(() => {
      html5QrCamera.clear();
      isCameraScanning = false;
    }).catch(() => { isCameraScanning = false; });
  }
}
function closeQrScanPopup() {
  stopQrCameraScan();
  var popup = document.getElementById('qrscan-popup');
  if (popup) popup.style.display = 'none';
}

</script>

<!-- QR Download Popup -->
<div id="qr-download-popup" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:99999;background:rgba(0,0,0,0.35);align-items:center;justify-content:center;">
  <div id="qr-download-ui" style="background:#fff;border-radius:16px;padding:2rem 1.5rem;max-width:340px;width:95vw;box-shadow:0 8px 32px rgba(25,118,210,0.18);text-align:center;position:relative;">
    <button onclick="closeQrDownloadPopup()" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;"><i class="fa fa-times"></i></button>
    <div style="font-size:1.1rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;">QR Rekening Anda</div>
    <div id="qr-download-preview" style="margin-bottom:1.2rem;"></div>
    <button onclick="downloadQrPng()" style="width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Unduh QR</button>
  </div>
</div>

<!-- Top Up Saldo Popup -->
<div id="topup-saldo-popup" class="ewallet-popup-overlay" style="display:none;z-index:21000;align-items:center;justify-content:center;">
  <div class="ewallet-popup-modal" style="max-width:350px;width:90vw;">
    <div class="ewallet-popup-header">
      <span class="ewallet-popup-title">Top Up Saldo</span>
      <button class="ewallet-popup-close" onclick="closeTopupSaldoPopup()">&times;</button>
    </div>
    <form id="topup-saldo-form" onsubmit="submitTopupSaldo(event)">
      <input type="hidden" id="topup-saldo-ewallet" name="ewallet" value="BANK FTI">
      <input type="hidden" id="topup-saldo-rekening" name="rekening" value="<?= htmlspecialchars($account_number) ?>">
      <div style="margin-bottom:1.2rem;">
        <label style="font-weight:500;color:#222;">Nominal Top Up</label><br>
        <input id="topup-saldo-nominal" name="amount" type="number" min="10000" step="1000" required placeholder="Masukkan nominal top up" style="width:100%;margin-top:0.3rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;">
      </div>
      <button type="submit" style="width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Top Up</button>
    </form>
    <div id="topup-saldo-result" style="margin-top:1rem;"></div>
  </div>
</div>

</body>
</html>
