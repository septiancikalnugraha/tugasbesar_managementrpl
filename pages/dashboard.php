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
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 2rem;
            min-height: 56px;
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1976d2;
            letter-spacing: 1px;
            gap: 0.7rem;
        }
        .navbar-logo img {
            width: 38px;
            height: 38px;
            margin-right: 10px;
            border-radius: 10px;
            background: #e3f2fd;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.10);
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
    </style>
</head>
<body>
<nav class="main-navbar">
    <div class="navbar-content">
        <div class="navbar-logo">
            <!-- Logo image removed to prevent loading issues -->
            Bank FTI
        </div>
    </div>
</nav>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar"><?= strtoupper(substr($name,0,1)) ?></div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <ul>
            <li><a href="dashboard_profil.php"><i class="fa fa-user"></i> Profil</a></li>
            <li><a href="#" class="active"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-cog"></i> Pengaturan</a></li>
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
            <div class="account-info-card">
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
                            <span class="account-value"><?= htmlspecialchars($account_number) ?></span>
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
                    </div>
                    <div style="text-align:center;min-width:130px;display:flex;flex-direction:column;align-items:center;gap:0.3rem;">
                        <div id="rekening-qrcode" style="display:inline-block;width:130px;height:130px;background:#fff;border-radius:10px;border:2px solid #e3e7ed;cursor:pointer;padding:7px;box-sizing:border-box;" title="Klik untuk unduh QR"></div>
                        <div style="font-size:0.95rem;color:#1976d2;margin-top:0.15rem;font-weight:600;">QR Rekening</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-section">
            <div class="action-buttons">
                <button class="action-btn" onclick="showComingSoon()">
                    <div class="action-btn-icon">
                        <i class="fa fa-exchange-alt"></i>
                    </div>
                    <div class="action-btn-title">Transfer Saldo</div>
                    <div class="action-btn-desc">Kirim uang ke rekening lain dengan cepat dan aman</div>
                </button>
                <button class="action-btn" onclick="showEwalletPopup(event)">
                    <div class="action-btn-icon">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="action-btn-title">Top-up Saldo</div>
                    <div class="action-btn-desc">Tambah saldo ke rekening Anda dengan mudah</div>
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
        <div class="menu-section">
            <div class="menu-section-title">Top Up</div>
            <div class="menu-grid">
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon orange"><i class="fa fa-wallet"></i></div>
                    <div class="menu-label">Top Up E-Wallet</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon green"><i class="fa fa-mobile-alt"></i></div>
                    <div class="menu-label">Pulsa</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon green"><i class="fa fa-wifi"></i></div>
                    <div class="menu-label">Data</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon blue"><i class="fa fa-gamepad"></i></div>
                    <div class="menu-label">Top Up Game</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon green" style="display:flex;align-items:center;justify-content:center;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(25,118,210,0.10);padding:4px;">
                        <svg width="34" height="34" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                            <polygon points="6,4 40,24 6,44" fill="#43A047"/>
                            <polygon points="6,4 24,24 6,44" fill="#FFC107"/>
                            <polygon points="24,24 42,8 42,40" fill="#1976D2"/>
                            <polygon points="6,4 42,8 24,24" fill="#F44336"/>
                        </svg>
                    </div>
                    <div class="menu-label">Saldo Google Play</div>
                </div>
            </div>
        </div>
        <!-- Menu Tagihan -->
        <div class="menu-section">
            <div class="menu-section-title">Tagihan</div>
            <div class="menu-grid">
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon orange"><i class="fa fa-bolt"></i></div>
                    <div class="menu-label">PLN</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon orange"><i class="fa fa-credit-card"></i></div>
                    <div class="menu-label">Kartu Kredit</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon blue"><i class="fa fa-satellite-dish"></i></div>
                    <div class="menu-label">TV Kabel & Internet</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon orange"><i class="fa fa-id-card"></i></div>
                    <div class="menu-label">BPJS</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon blue"><i class="fa fa-tint"></i></div>
                    <div class="menu-label">PDAM</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon blue"><i class="fa fa-graduation-cap"></i></div>
                    <div class="menu-label">Edukasi</div>
                </div>
                <div class="menu-item" onclick="showComingSoon()">
                    <div class="menu-icon green"><i class="fa fa-hand-holding-heart"></i></div>
                    <div class="menu-label">Donasi & Zakat</div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- E-Wallet Popup -->
<div id="ewallet-popup" class="ewallet-popup-overlay" style="display:none;">
  <div class="ewallet-popup-modal">
    <div class="ewallet-popup-header">
      <span class="ewallet-popup-title">Pilih e-Wallet</span>
      <button class="ewallet-popup-close" onclick="closeEwalletPopup()">&times;</button>
    </div>
    <div class="ewallet-list">
      <div class="ewallet-item" onclick="selectEwallet('Bank FTI')">
        <img src="https://ui-avatars.com/api/?name=Bank+FTI&background=1976d2&color=fff&rounded=true&size=64" alt="Bank FTI">
        <span>Bank FTI</span>
      </div>
      <div class="ewallet-item" onclick="selectEwallet('DANA')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Logo_Dana.png" alt="DANA">
        <span>DANA</span>
      </div>
      <div class="ewallet-item" onclick="selectEwallet('GO-PAY')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/0a/Logo_Gopay.png" alt="GO-PAY">
        <span>GO-PAY</span>
      </div>
      <div class="ewallet-item" onclick="selectEwallet('LinkAja')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5a/Logo_LinkAja.png" alt="LinkAja">
        <span>LinkAja</span>
      </div>
      <div class="ewallet-item" onclick="selectEwallet('OVO')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Logo_OVO.png" alt="OVO">
        <span>OVO</span>
      </div>
      <div class="ewallet-item" onclick="selectEwallet('ShopeePay')">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Logo_ShopeePay.png" alt="ShopeePay">
        <span>ShopeePay</span>
      </div>
    </div>
  </div>
</div>
<!-- E-Wallet Detail Form Popup -->
<div id="ewallet-detail-popup" class="ewallet-popup-overlay" style="display:none;">
  <div class="ewallet-popup-modal">
    <div class="ewallet-popup-header">
      <span class="ewallet-popup-title">Masukkan Detail e-Wallet</span>
      <button class="ewallet-popup-close" onclick="closeEwalletDetailPopup()">&times;</button>
    </div>
    <form id="ewallet-detail-form" onsubmit="submitEwalletDetail(event)">
      <div style="color:#888;font-size:1rem;margin-bottom:1.2rem;">Pilih e-Wallet yang ingin kamu top up dan masukkan informasi akun.</div>
      <div style="margin-bottom:1.2rem;">
        <label style="font-weight:500;color:#222;">e-Wallet</label><br>
        <input id="ewallet-detail-name" name="ewallet" type="text" readonly style="width:100%;margin-top:0.3rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.2rem;">
        <label style="font-weight:500;color:#222;">No. Rekening e-Wallet</label><br>
        <input id="ewallet-detail-user" name="user" type="text" required placeholder="Masukkan nomor rekening e-wallet" style="width:100%;margin-top:0.3rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.2rem;">
        <label style="font-weight:500;color:#222;">Nominal Top Up</label><br>
        <input id="ewallet-detail-amount" name="amount" type="number" min="1000" step="1000" required placeholder="Masukkan nominal top up" style="width:100%;margin-top:0.3rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.2rem;display:flex;align-items:center;gap:0.7rem;">
        <input type="checkbox" id="ewallet-detail-save" name="save" style="width:1.2rem;height:1.2rem;">
        <label for="ewallet-detail-save" style="color:#555;font-size:1rem;">Simpan e-Wallet</label>
      </div>
      <button type="submit" style="width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#90caf9;color:#fff;font-weight:700;border:none;cursor:pointer;">Lanjut</button>
    </form>
  </div>
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
<div id="qrscan-popup" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:18px;max-width:370px;width:95vw;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:2rem 1.2rem 1.2rem 1.2rem;position:relative;text-align:center;">
    <div style="font-size:1.3rem;font-weight:700;color:#1976d2;margin-bottom:0.7rem;display:flex;align-items:center;justify-content:center;gap:0.5rem;"><i class='fa fa-qrcode'></i> Scan QR Code</div>
    <div id="qr-reader" style="width:100%;max-width:260px;margin:0 auto 1rem auto;"></div>
    <div id="qr-file-reader" style="display:none;"></div>
    <input id="qr-result" type="text" readonly placeholder="Hasil scan QR akan muncul di sini" style="width:100%;margin-bottom:1rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;">
    <label for="qr-file-input" style="display:inline-block;margin-bottom:1rem;cursor:pointer;color:#1976d2;font-weight:600;background:#e3f2fd;padding:0.5rem 1.2rem;border-radius:7px;transition:background 0.2s;">Ambil dari Galeri</label>
    <input type="file" id="qr-file-input" accept="image/*" style="display:none;">
    <button onclick="closeQrScanPopup()" style="width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Tutup</button>
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

<script src="../assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function showComingSoon() {
    // Create a more attractive modal-style alert
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        backdrop-filter: blur(4px);
    `;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        border-radius: 16px;
        padding: 2rem;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        transform: scale(0.9);
        transition: transform 0.3s ease;
    `;
    
    modalContent.innerHTML = `
        <div style="font-size: 3rem; margin-bottom: 1rem;">🚀</div>
        <h3 style="color: #1976d2; margin-bottom: 0.5rem; font-weight: 700;">Fitur Segera Hadir!</h3>
        <p style="color: #666; margin-bottom: 1.5rem; line-height: 1.5;">
            Kami sedang mengembangkan fitur ini untuk memberikan pengalaman banking yang lebih baik.
        </p>
        <button onclick="this.closest('.modal-overlay').remove()" style="
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            Mengerti
        </button>
    `;
    
    modal.className = 'modal-overlay';
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Animate in
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
    }, 10);
    
    // Close on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Auto refresh balance every 60 seconds
// setInterval(function() {
//     // In a real application, you would make an AJAX call to refresh the balance
//     console.log('Auto refresh - Ready for implementation');
// }, 60000);

// Welcome animation with staggered effects
window.addEventListener('load', function() {
    // Animate welcome header
    const welcomeHeader = document.querySelector('.welcome-header');
    if (welcomeHeader) {
        welcomeHeader.style.opacity = '0';
        welcomeHeader.style.transform = 'translateY(-30px)';
        setTimeout(() => {
            welcomeHeader.style.transition = 'all 0.8s ease';
            welcomeHeader.style.opacity = '1';
            welcomeHeader.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Animate stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    // Animate action buttons
    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateX(-30px)';
        setTimeout(() => {
            btn.style.transition = 'all 0.6s ease';
            btn.style.opacity = '1';
            btn.style.transform = 'translateX(0)';
        }, 400 + (index * 150));
    });
    
    // Animate other sections
    const sections = document.querySelectorAll('.account-info-card, .recent-activity');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(40px)';
        setTimeout(() => {
            section.style.transition = 'all 0.8s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 800 + (index * 200));
    });
    
    // Animate sidebar items
    const sidebarItems = document.querySelectorAll('.sidebar ul li');
    sidebarItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 100 + (index * 80));
    });
    
    // Animate profile section
    const profile = document.querySelector('.sidebar-profile');
    if (profile) {
        profile.style.opacity = '0';
        profile.style.transform = 'scale(0.9)';
        setTimeout(() => {
            profile.style.transition = 'all 0.5s ease';
            profile.style.opacity = '1';
            profile.style.transform = 'scale(1)';
        }, 50);
    }
});

// Add hover effects for interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(25, 118, 210, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});

document.addEventListener('DOMContentLoaded', function() {
  var logo = document.querySelector('.navbar-logo img');
  if (logo) {
    logo.onerror = function() {
      this.style.background = '#e3f2fd';
      this.src = 'https://via.placeholder.com/38x38?text=BF';
    };
  }
});

function showEwalletPopup(e) {
  e.preventDefault();
  document.getElementById('ewallet-popup').style.display = 'flex';
}
function closeEwalletPopup() {
  document.getElementById('ewallet-popup').style.display = 'none';
}
function selectEwallet(name) {
  closeEwalletPopup();
  // Tampilkan popup detail e-wallet
  document.getElementById('ewallet-detail-popup').style.display = 'flex';
  document.getElementById('ewallet-detail-name').value = name;
  document.getElementById('ewallet-detail-user').value = '';
  document.getElementById('ewallet-detail-save').checked = false;
}
function closeEwalletDetailPopup() {
  document.getElementById('ewallet-detail-popup').style.display = 'none';
}
function showHistoryTab(tab) {
  document.getElementById('tab-topup').style.background = tab === 'topup' ? '#1976d2' : '#e3e7ed';
  document.getElementById('tab-topup').style.color = tab === 'topup' ? '#fff' : '#1976d2';
  document.getElementById('tab-transfer').style.background = tab === 'transfer' ? '#1976d2' : '#e3e7ed';
  document.getElementById('tab-transfer').style.color = tab === 'transfer' ? '#fff' : '#1976d2';
  renderHistoryTable(tab);
}
function renderHistoryTable(tab) {
  var tbody = document.getElementById('history-table-body');
  var empty = document.getElementById('history-empty');
  var thead = tbody.parentElement.previousElementSibling;
  tbody.innerHTML = '';
  // Ubah header jika transfer
  if (tab === 'transfer') {
    thead.innerHTML = `<tr style="background:#f4f6f8;color:#1976d2;font-size:1rem;">
      <th style="padding:0.7rem 0.3rem;text-align:left;">Tanggal</th>
      <th style="padding:0.7rem 0.3rem;text-align:left;">e-Wallet</th>
      <th style="padding:0.7rem 0.3rem;text-align:left;">Rekening</th>
      <th style="padding:0.7rem 0.3rem;text-align:right;">Nominal</th>
    </tr>`;
  } else {
    thead.innerHTML = `<tr style="background:#f4f6f8;color:#1976d2;font-size:1rem;">
      <th style="padding:0.7rem 0.3rem;text-align:left;">Tanggal</th>
      <th style="padding:0.7rem 0.3rem;text-align:left;">e-Wallet</th>
      <th style="padding:0.7rem 0.3rem;text-align:left;">Rekening</th>
      <th style="padding:0.7rem 0.3rem;text-align:right;">Nominal</th>
    </tr>`;
  }
  var filtered = allHistoryData.filter(function(item) {
    return (tab === 'topup' ? item.kategori === 'topup' : item.kategori === 'transfer');
  });
  if (!Array.isArray(filtered) || filtered.length === 0) {
    empty.style.display = 'block';
    return;
  } else {
    empty.style.display = 'none';
  }
  filtered.forEach(function(item) {
    var tr = document.createElement('tr');
    if (tab === 'transfer') {
      tr.innerHTML = '<td style="padding:0.5rem 0.3rem;">' + item.tanggal + '</td>' +
                     '<td style="padding:0.5rem 0.3rem;">' + item.ewallet + '</td>' +
                     '<td style="padding:0.5rem 0.3rem;">' + item.rekening + '</td>' +
                     '<td style="padding:0.5rem 0.3rem;text-align:right;">Rp ' + parseInt(item.nominal).toLocaleString('id-ID') + '</td>';
    } else {
      tr.innerHTML = '<td style="padding:0.5rem 0.3rem;">' + item.tanggal + '</td>' +
                     '<td style="padding:0.5rem 0.3rem;">' + item.ewallet + '</td>' +
                     '<td style="padding:0.5rem 0.3rem;">' + item.rekening + '</td>' +
                     '<td style="padding:0.5rem 0.3rem;text-align:right;">Rp ' + parseInt(item.nominal).toLocaleString('id-ID') + '</td>';
    }
    tbody.appendChild(tr);
  });
}
function renderHistoryTableInit() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '../get_history.php', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      try {
        allHistoryData = JSON.parse(xhr.responseText);
        showHistoryTab('topup');
      } catch (e) {
        document.getElementById('history-empty').style.display = 'block';
      }
    } else {
      document.getElementById('history-empty').style.display = 'block';
    }
  };
  xhr.send();
}
function showHistoryPopup(e) {
  if (e) e.preventDefault();
  renderHistoryTableInit();
  document.getElementById('history-popup').style.display = 'flex';
}
function closeHistoryPopup() {
  document.getElementById('history-popup').style.display = 'none';
}
function printHistoryTable() {
  var popup = document.getElementById('history-popup');
  var table = popup.querySelector('table');
  var style = '<style>body{font-family:Segoe UI,Arial,sans-serif;}table{width:100%;border-collapse:collapse;}th,td{padding:8px 6px;}th{background:#f4f6f8;color:#1976d2;}td{text-align:left;}th:last-child,td:last-child{text-align:right;}</style>';
  var win = window.open('', '', 'width=700,height=600');
  win.document.write('<html><head><title>Cetak Riwayat Top Up</title>' + style + '</head><body>');
  win.document.write('<h2 style="color:#1976d2;">Riwayat Top Up</h2>');
  win.document.write(table.outerHTML);
  win.document.write('</body></html>');
  win.document.close();
  win.focus();
  setTimeout(function(){ win.print(); win.close(); }, 500);
}
function showTransferPopup() {
  document.getElementById('transfer-popup').style.display = 'flex';
  loadReceivers();
  document.getElementById('receiver-list-section').style.display = 'block';
  document.getElementById('add-receiver-section').style.display = 'none';
  document.getElementById('transfer-form-section').style.display = 'none';
}
function closeTransferPopup() {
  document.getElementById('transfer-popup').style.display = 'none';
}
function showAddReceiverForm() {
  document.getElementById('receiver-list-section').style.display = 'none';
  document.getElementById('add-receiver-section').style.display = 'block';
}
function hideAddReceiverForm() {
  document.getElementById('add-receiver-section').style.display = 'none';
  document.getElementById('receiver-list-section').style.display = 'block';
}
function submitAddReceiver(e) {
  e.preventDefault();
  var name = document.getElementById('receiver-name').value.trim();
  var account = document.getElementById('receiver-account').value.trim();
  if (!name || !account) { alert('Nama dan nomor rekening wajib diisi!'); return; }
  var btn = document.querySelector('#add-receiver-form button[type=submit]');
  var oldText = btn.innerHTML;
  btn.innerHTML = 'Menyimpan...'; btn.disabled = true;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '../add_receiver.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    btn.innerHTML = oldText; btn.disabled = false;
    try {
      var res = JSON.parse(xhr.responseText);
      if (res.success) {
        hideAddReceiverForm();
        loadReceivers();
      } else { alert(res.message || 'Gagal menambah penerima'); }
    } catch (e) { alert('Gagal menambah penerima'); }
  };
  xhr.onerror = function() { btn.innerHTML = oldText; btn.disabled = false; alert('Gagal terhubung ke server.'); };
  xhr.send('name=' + encodeURIComponent(name) + '&account_number=' + encodeURIComponent(account));
}
function loadReceivers() {
  var list = document.getElementById('receiver-list');
  list.innerHTML = 'Memuat...';
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '../get_receivers.php', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      try {
        var data = JSON.parse(xhr.responseText);
        if (!Array.isArray(data) || data.length === 0) {
          list.innerHTML = '<div style="color:#888;text-align:center;">Belum ada penerima.<br>Tambah penerima terlebih dahulu.</div>';
          return;
        }
        list.innerHTML = '';
        data.forEach(function(item) {
          var div = document.createElement('div');
          div.className = 'receiver-item';
          div.style = 'padding:0.7rem 0.5rem;border-bottom:1px solid #e3e7ed;cursor:pointer;display:flex;align-items:center;gap:1rem;';
          div.innerHTML = '<span style="font-weight:600;">' + item.name + '</span><span style="color:#1976d2;">' + item.account_number + '</span>';
          div.onclick = function() { showTransferForm(item); };
          list.appendChild(div);
        });
      } catch (e) { list.innerHTML = '<div style="color:#888;text-align:center;">Gagal memuat penerima.</div>'; }
    } else { list.innerHTML = '<div style="color:#888;text-align:center;">Gagal memuat penerima.</div>'; }
  };
  xhr.onerror = function() { list.innerHTML = '<div style="color:#888;text-align:center;">Gagal memuat penerima.</div>'; };
  xhr.send();
}
function showTransferForm(receiver) {
  document.getElementById('receiver-list-section').style.display = 'none';
  document.getElementById('add-receiver-section').style.display = 'none';
  var section = document.getElementById('transfer-form-section');
  section.style.display = 'block';
  section.innerHTML = `
    <div style="margin-bottom:1.2rem;"><b>Penerima:</b> ${receiver.name} (${receiver.account_number})</div>
    <form id="transfer-form" onsubmit="submitTransfer(event, ${receiver.id})">
      <div style="margin-bottom:1rem;">
        <label>Nominal Transfer</label><br>
        <input type="number" id="transfer-amount" name="amount" min="1000" step="1000" required style="width:100%;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;outline:none;transition:border 0.2s;">
      </div>
      <button type="submit" style="width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;">Transfer</button>
      <button type="button" onclick="backToReceiverList()" style="width:100%;margin-top:0.7rem;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;">Batal</button>
    </form>
  `;
}
function backToReceiverList() {
  document.getElementById('transfer-form-section').style.display = 'none';
  document.getElementById('receiver-list-section').style.display = 'block';
}
function submitTransfer(e, receiver_id) {
  e.preventDefault();
  var amount = parseInt(document.getElementById('transfer-amount').value);
  var btn = document.querySelector('#transfer-form button[type=submit]');
  var oldText = btn.innerHTML;
  if (isNaN(amount) || amount < 1000) { alert('Nominal transfer minimal 1.000!'); return; }
  btn.innerHTML = 'Memproses...'; btn.disabled = true;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '../transfer.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    btn.innerHTML = oldText; btn.disabled = false;
    if (xhr.status === 200) {
      try {
        var res = JSON.parse(xhr.responseText);
        if (res.success) {
          closeTransferPopup();
          // Update saldo aktif
          var saldoElem = document.querySelector('.account-value.balance');
          if (saldoElem) {
            saldoElem.textContent = 'Rp ' + parseInt(res.new_balance).toLocaleString('id-ID');
          }
          showTransferSuccessFeedback(res.transfer_id);
        } else {
          alert(res.message || 'Transfer gagal');
        }
      } catch (e) { alert('Terjadi kesalahan server.'); }
    } else {
      alert('Gagal terhubung ke server.');
    }
  };
  xhr.onerror = function() {
    btn.innerHTML = oldText; btn.disabled = false;
    alert('Gagal terhubung ke server.');
  };
  xhr.send('receiver_id=' + encodeURIComponent(receiver_id) + '&amount=' + amount);
}
function showTransferSuccessFeedback(transferId) {
  var modal = document.createElement('div');
  modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:30000;display:flex;align-items:center;justify-content:center;';
  var content = document.createElement('div');
  content.style.cssText = 'background:#fff;border-radius:18px;max-width:370px;width:95vw;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:2rem 1.2rem 1.2rem 1.2rem;position:relative;text-align:center;';
  content.innerHTML = `
    <div style='font-size:2.5rem;color:#1976d2;margin-bottom:0.7rem;'>✔️</div>
    <div style='font-size:1.25rem;font-weight:700;color:#1976d2;margin-bottom:0.5rem;'>Transfer Berhasil!</div>
    <div style='color:#444;margin-bottom:1.2rem;'>Saldo berhasil ditransfer ke penerima.<br>Terima kasih telah menggunakan layanan Bank FTI.</div>
    <div style='margin-bottom:1rem;'>
      <label for='ulasan-transfer' style='font-weight:600;color:#1976d2;'>Ulasan Anda</label><br>
      <textarea id='ulasan-transfer' rows='3' style='width:100%;margin-top:0.3rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;resize:none;'></textarea>
    </div>
    <button id='btn-kirim-ulasan' style='width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;'>Kirim Ulasan</button>
    <button onclick='this.closest(".modal-overlay-feedback").remove()' style='width:100%;margin-top:0.7rem;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;'>Tutup</button>
  `;
  modal.className = 'modal-overlay-feedback';
  modal.appendChild(content);
  document.body.appendChild(modal);
  // Close on outside click
  modal.addEventListener('click', function(e) {
    if (e.target === modal) modal.remove();
  });
  // Kirim ulasan ke backend
  content.querySelector('#btn-kirim-ulasan').onclick = function() {
    var review = content.querySelector('#ulasan-transfer').value.trim();
    if (!review) { alert('Ulasan tidak boleh kosong!'); return; }
    this.disabled = true;
    this.innerHTML = 'Mengirim...';
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../transfer.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      content.querySelector('#btn-kirim-ulasan').disabled = false;
      content.querySelector('#btn-kirim-ulasan').innerHTML = 'Kirim Ulasan';
      if (xhr.status === 200) {
        try {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            alert('Ulasan berhasil dikirim!');
            modal.remove();
          } else {
            alert(res.message || 'Gagal mengirim ulasan');
          }
        } catch (e) { alert('Gagal mengirim ulasan'); }
      } else { alert('Gagal terhubung ke server.'); }
    };
    xhr.onerror = function() { alert('Gagal terhubung ke server.'); };
    xhr.send('review=' + encodeURIComponent(review) + '&transfer_id=' + encodeURIComponent(transferId));
  };
}
// Ganti onclick tombol Transfer Saldo agar buka popup transfer
var transferBtn = document.querySelectorAll('.action-btn-title');
transferBtn.forEach(function(btn) {
  if (btn.textContent.trim() === 'Transfer Saldo') {
    btn.parentElement.onclick = function() { showTransferPopup(); };
  }
});

// Add this function after showTransferSuccessFeedback
function showTopupSuccessFeedback(topupId) {
  var modal = document.createElement('div');
  modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:30000;display:flex;align-items:center;justify-content:center;';
  var content = document.createElement('div');
  content.style.cssText = 'background:#fff;border-radius:18px;max-width:370px;width:95vw;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:2rem 1.2rem 1.2rem 1.2rem;position:relative;text-align:center;';
  content.innerHTML = `
    <div style='font-size:2.5rem;color:#1976d2;margin-bottom:0.7rem;'>✔️</div>
    <div style='font-size:1.25rem;font-weight:700;color:#1976d2;margin-bottom:0.5rem;'>Top Up Berhasil!</div>
    <div style='color:#444;margin-bottom:1.2rem;'>Saldo berhasil ditambahkan ke rekening Anda.<br>Terima kasih telah menggunakan layanan Bank FTI.</div>
    <div style='margin-bottom:1rem;'>
      <label for='ulasan-topup' style='font-weight:600;color:#1976d2;'>Ulasan Anda</label><br>
      <textarea id='ulasan-topup' rows='3' style='width:100%;margin-top:0.3rem;padding:0.7rem 1rem;border-radius:8px;border:1.5px solid #e3e7ed;background:#f8fafc;font-size:1rem;resize:none;'></textarea>
    </div>
    <button id='btn-kirim-ulasan-topup' style='width:100%;padding:0.9rem 0;font-size:1.1rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;'>Kirim Ulasan</button>
    <button onclick='this.closest(".modal-overlay-feedback-topup").remove()' style='width:100%;margin-top:0.7rem;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;'>Tutup</button>
  `;
  modal.className = 'modal-overlay-feedback-topup';
  modal.appendChild(content);
  document.body.appendChild(modal);
  // Close on outside click
  modal.addEventListener('click', function(e) {
    if (e.target === modal) modal.remove();
  });
  // Kirim ulasan ke backend
  content.querySelector('#btn-kirim-ulasan-topup').onclick = function() {
    var review = content.querySelector('#ulasan-topup').value.trim();
    if (!review) { alert('Ulasan tidak boleh kosong!'); return; }
    this.disabled = true;
    this.innerHTML = 'Mengirim...';
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../topup.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      content.querySelector('#btn-kirim-ulasan-topup').disabled = false;
      content.querySelector('#btn-kirim-ulasan-topup').innerHTML = 'Kirim Ulasan';
      if (xhr.status === 200) {
        try {
          var res = JSON.parse(xhr.responseText);
          if (res.success) {
            alert('Ulasan berhasil dikirim!');
            modal.remove();
          } else {
            alert(res.message || 'Gagal mengirim ulasan');
          }
        } catch (e) { alert('Gagal mengirim ulasan'); }
      } else { alert('Gagal terhubung ke server.'); }
    };
    xhr.onerror = function() { alert('Gagal terhubung ke server.'); };
    xhr.send('topup_id=' + encodeURIComponent(topupId) + '&review=' + encodeURIComponent(review));
  };
}

document.addEventListener('DOMContentLoaded', function() {
  var ewalletForm = document.getElementById('ewallet-detail-form');
  if (ewalletForm) {
    ewalletForm.onsubmit = function(e) {
      e.preventDefault();
      var btn = ewalletForm.querySelector('button[type=submit]');
      var oldText = btn.innerHTML;
      btn.innerHTML = 'Memproses...'; btn.disabled = true;
      var ewallet = document.getElementById('ewallet-detail-name').value.trim();
      var rekening = document.getElementById('ewallet-detail-user').value.trim();
      var amount = document.getElementById('ewallet-detail-amount').value.trim();
      if (!ewallet || !rekening || !amount) {
        alert('Semua field wajib diisi!');
        btn.innerHTML = oldText; btn.disabled = false;
        return;
      }
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '../topup.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        btn.innerHTML = oldText; btn.disabled = false;
        if (xhr.status === 200) {
          try {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
              closeEwalletDetailPopup();
              // Update saldo aktif
              var saldoElem = document.querySelector('.account-value.balance');
              if (saldoElem && res.new_balance) {
                saldoElem.textContent = 'Rp ' + parseInt(res.new_balance).toLocaleString('id-ID');
              }
              // Langsung gunakan id dari response
              showTopupSuccessFeedback(res.topup_id || null);
            } else {
              alert(res.message || 'Top up gagal');
            }
          } catch (e) { alert('Terjadi kesalahan server.'); }
        } else {
          alert('Gagal terhubung ke server.');
        }
      };
      xhr.onerror = function() {
        btn.innerHTML = oldText; btn.disabled = false;
        alert('Gagal terhubung ke server.');
      };
      xhr.send('ewallet=' + encodeURIComponent(ewallet) + '&rekening=' + encodeURIComponent(rekening) + '&amount=' + encodeURIComponent(amount));
    };
  }
});

document.addEventListener('DOMContentLoaded', function() {
    var rekening = <?= json_encode($account_number) ?>;
    var qrContainer = document.getElementById('rekening-qrcode');
    qrContainer.innerHTML = '';
    if (rekening && rekening !== 'Tidak tersedia') {
        new QRCode(qrContainer, {
            text: rekening,
            width: 116,
            height: 116,
            colorDark : "#1976d2",
            colorLight : "#fff",
            correctLevel : QRCode.CorrectLevel.H
        });
    } else {
        qrContainer.innerHTML = '<div style="width:116px;height:116px;display:flex;align-items:center;justify-content:center;background:#f4f6f8;border-radius:10px;border:2px dashed #e3e7ed;color:#bbb;">QR</div>';
    }
});

function showQrScanPopup() {
  document.getElementById('qrscan-popup').style.display = 'flex';
  const qrReader = new Html5Qrcode("qr-reader");
  qrReader.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 200 },
    qrCodeMessage => {
      document.getElementById('qr-result').value = qrCodeMessage;
      qrReader.stop();
    },
    errorMessage => {}
  ).catch(err => {
    document.getElementById('qr-result').value = 'Tidak dapat mengakses kamera.';
  });
  window._qrReader = qrReader;
}
function closeQrScanPopup() {
  document.getElementById('qrscan-popup').style.display = 'none';
  if (window._qrReader) {
    window._qrReader.stop().catch(()=>{});
    window._qrReader.clear();
    window._qrReader = null;
  }
  document.getElementById('qr-result').value = '';
  document.getElementById('qr-reader').innerHTML = '';
  document.getElementById('qr-file-input').value = '';
}
document.querySelectorAll('.action-btn').forEach(btn => {
  if (btn.querySelector('.action-btn-title') && btn.querySelector('.action-btn-title').textContent.trim() === 'Scan QR Code') {
    btn.onclick = showQrScanPopup;
  }
});
document.getElementById('qr-file-input').addEventListener('change', async function(e) {
  const file = e.target.files[0];
  const qrResult = document.getElementById('qr-result');
  if (!file || !file.type.match(/^image\/(png|jpeg|jpg)$/)) {
    qrResult.value = 'File harus berupa gambar PNG/JPG.';
    this.value = '';
    return;
  }
  if (file.size > 2 * 1024 * 1024) {
    qrResult.value = 'Ukuran gambar terlalu besar (maks 2MB).';
    this.value = '';
    return;
  }
  qrResult.value = 'Memproses gambar...';
  let finished = false;
  let timeoutId;
  let newReader = null;
  let html5QrFileReader = null;
  const resetAll = (msg) => {
    if (finished) return;
    finished = true;
    qrResult.value = msg;
    try { html5QrFileReader && html5QrFileReader.clear(); } catch(e){}
    try { newReader && newReader.remove(); } catch(e){}
    document.getElementById('qr-file-input').value = '';
    if (timeoutId) clearTimeout(timeoutId);
    // Fallback: jika timeout, tutup popup scan QR
    if (msg && msg.includes('timeout')) {
      document.getElementById('qrscan-popup').style.display = 'none';
      alert('QR tidak ditemukan di gambar (timeout). Silakan coba gambar lain atau refresh halaman.');
    }
  };
  try {
    if (window._qrReader) {
      await window._qrReader.stop();
      await window._qrReader.clear();
      window._qrReader = null;
    }
    let oldReader = document.getElementById('qr-file-reader');
    if (oldReader) try { oldReader.remove(); } catch(e){}
    newReader = document.createElement('div');
    newReader.id = 'qr-file-reader';
    newReader.style.display = 'none';
    document.querySelector('#qrscan-popup > div').appendChild(newReader);
    html5QrFileReader = new Html5Qrcode('qr-file-reader');
    timeoutId = setTimeout(() => {
      resetAll('QR tidak ditemukan di gambar (timeout).');
    }, 10000);
    const decodedText = await html5QrFileReader.scanFile(file, true);
    resetAll('');
    if (/^\+?\d{8,}$/.test(decodedText)) {
      showQrActionPopup(decodedText);
    } else {
      qrResult.value = decodedText || 'QR tidak valid.';
    }
  } catch (err) {
    console.error('QR scan error:', err);
    resetAll('QR tidak ditemukan di gambar.');
  }
});
function showQrActionPopup(rekening) {
  document.getElementById('qr-action-rekening').textContent = rekening;
  document.getElementById('qr-action-popup').style.display = 'flex';
  window._qrActionRekening = rekening;
}
function closeQrActionPopup() {
  document.getElementById('qr-action-popup').style.display = 'none';
  window._qrActionRekening = null;
}
function handleQrAction(type) {
  const rek = window._qrActionRekening;
  closeQrActionPopup();
  if (type === 'transfer') showTransferPopup(rek);
  else if (type === 'topup') showEwalletPopup(null, rek);
  else if (type === 'tagihan') showTagihanPopup(rek);
}

document.getElementById('rekening-qrcode').onclick = function() {
    var qrDiv = document.getElementById('rekening-qrcode');
    var img = qrDiv.querySelector('img') || qrDiv.querySelector('canvas');
    if (!img) return alert('QR belum tersedia');
    let dataUrl = img.tagName === 'IMG' ? img.src : img.toDataURL('image/png');
    var a = document.createElement('a');
    a.href = dataUrl;
    a.download = 'qr_rekening.png';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
};

function showQrActionButtonsIfValid() {
  const qrResult = document.getElementById('qr-result');
  const qrActionButtons = document.getElementById('qr-action-buttons');
  if (/^\+?\d{8,}$/.test(qrResult.value.trim())) {
    qrActionButtons.style.display = 'flex';
  } else {
    qrActionButtons.style.display = 'none';
  }
}
document.getElementById('qr-result').addEventListener('input', showQrActionButtonsIfValid);
// Tampilkan juga setelah scan
const origQrResultSetter = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
Object.defineProperty(document.getElementById('qr-result'), 'value', {
  set: function(v) {
    origQrResultSetter.call(this, v);
    showQrActionButtonsIfValid();
  }
});
// Handler tombol aksi
function openTransferWithRek(rek) {
  // Implementasi: buka popup transfer, isi rekening tujuan
  showTransferPopup(rek);
}
function openTopupWithRek(rek) {
  // Implementasi: buka popup topup, isi rekening tujuan
  showEwalletPopup(null, rek);
}
function openTagihanWithRek(rek) {
  // Implementasi: buka popup tagihan, isi rekening tujuan
  showTagihanPopup(rek);
}
document.getElementById('btn-transfer-qr').onclick = function() {
  closeQrScanPopup();
  openTransferWithRek(document.getElementById('qr-result').value.trim());
};
document.getElementById('btn-topup-qr').onclick = function() {
  closeQrScanPopup();
  openTopupWithRek(document.getElementById('qr-result').value.trim());
};
document.getElementById('btn-tagihan-qr').onclick = function() {
  closeQrScanPopup();
  openTagihanWithRek(document.getElementById('qr-result').value.trim());
};
// Panggil showQrActionButtonsIfValid setiap kali hasil scan berubah
['input','change'].forEach(ev=>document.getElementById('qr-result').addEventListener(ev, showQrActionButtonsIfValid));
</script>

</body>
</html>