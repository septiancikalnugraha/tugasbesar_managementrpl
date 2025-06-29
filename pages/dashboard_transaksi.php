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
$page_title = 'Dashboard Transaksi';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$name = $_SESSION['user_name'] ?? '';
$role = 'Nasabah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Transaksi - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        .history-section {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(25,118,210,0.07);
            border: 1px solid #e3f2fd;
            padding: 2.2rem 2rem 2rem 2rem;
            margin-bottom: 2.5rem;
        }
        .history-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .history-header h1 {
            color: #1976d2;
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
        }
        .history-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .history-tab {
            flex: 1;
            padding: 0.9rem 0;
            font-size: 1.08rem;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            background: #e3e7ed;
            color: #1976d2;
            transition: background 0.2s, color 0.2s;
        }
        .history-tab.active {
            background: #1976d2;
            color: #fff;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 0.98rem;
        }
        .history-table th, .history-table td {
            padding: 0.65rem 0.5rem;
            border-bottom: 1px solid #e3e7ed;
            text-align: left;
            vertical-align: middle;
            font-size: 0.98rem;
        }
        .history-table th {
            background: #f4f6f8;
            color: #1976d2;
            font-weight: 700;
            text-align: left;
        }
        .history-table td.amount, .history-table th.amount {
            text-align: right;
            font-weight: 700;
            color: #1976d2;
        }
        .history-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .history-table tr:hover {
            background: #e3f2fd;
        }
        .history-empty {
            text-align: center;
            color: #888;
            margin: 2rem 0 1rem 0;
        }
        .transaction-type {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .type-topup {
            background: rgba(72, 187, 120, 0.1);
            color: #2f855a;
        }
        .type-transfer {
            background: rgba(66, 153, 225, 0.1);
            color: #2c5aa0;
        }
        .type-payment {
            background: rgba(237, 137, 54, 0.1);
            color: #c05621;
        }
        .amount.positive {
            color: #2f855a;
        }
        .amount.negative {
            color: #e53e3e;
        }
        @media (max-width: 700px) {
            .history-section { padding: 1.2rem 0.5rem 1.2rem 0.5rem; border-radius: 12px; }
            .history-header h1 { font-size: 1.1rem; }
            .history-tabs { gap: 0.5rem; }
            .history-tab { font-size: 0.95rem; padding: 0.7rem 0; }
            .history-table th, .history-table td { font-size: 0.92rem; padding: 0.7rem 0.3rem; }
        }
        @media (max-width: 900px) {
            .history-table th, .history-table td { font-size: 0.92rem; padding: 0.5rem 0.2rem; }
        }
        .topup-flex-row {
            display: flex;
            gap: 2.5rem;
            align-items: flex-start;
            justify-content: flex-start;
            padding-top: 0;
        }
        .menu-bar, .tagihan-menu-bar {
            background: linear-gradient(90deg, #1976d2 0%, #228be6 100%);
            border-radius: 18px;
            padding: 1.2rem 1.2rem 1.2rem 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
        }
        .menu-title, .tagihan-menu-title {
            color: #fff;
            font-size: 1.08rem;
            font-weight: 800;
            margin-right: 1.2rem;
            min-width: 90px;
        }
        .menu-grid, .tagihan-menu-grid {
            display: flex;
            flex: 1;
            gap: 2.2rem;
            justify-content: space-between;
        }
        .menu-item, .tagihan-menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            color: #fff;
            font-weight: 600;
            font-size: 0.98rem;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .menu-item:hover, .tagihan-menu-item:hover {
            transform: translateY(-3px) scale(1.04);
            box-shadow: 0 4px 16px rgba(25,118,210,0.10);
        }
        .menu-icon, .tagihan-menu-icon {
            border: 2px solid #fff;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.08rem;
            margin-bottom: 0.4rem;
            background: transparent;
        }
        .menu-label, .tagihan-menu-label {
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
        }
        @media (max-width: 900px) {
            .menu-bar, .tagihan-menu-bar { flex-direction: column; gap: 0.7rem; padding: 0.7rem 0.4rem; }
            .menu-title, .tagihan-menu-title { margin-bottom: 0.7rem; margin-right: 0; }
            .menu-grid, .tagihan-menu-grid { gap: 0.7rem; flex-wrap: wrap; justify-content: flex-start; }
        }
        .status-badge {
            display: inline-block;
            padding: 0.28em 0.9em;
            border-radius: 12px;
            font-size: 0.93em;
            font-weight: 600;
            color: #fff;
            background: #bdbdbd;
            letter-spacing: 0.5px;
        }
        .status-draft { background: #f6ad55; color: #fff; }
        .status-belumlunas { background: #3182ce; color: #fff; }
        .status-lunas { background: #38a169; color: #fff; }
        .status-center { text-align: center !important; }
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
            <div class="sidebar-avatar"><?= strtoupper(substr($name,0,1)) ?></div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <ul>
            <li><a href="dashboard_profil.php"><i class="fa fa-user"></i> Profil</a></li>
            <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="#" class="active"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="history-section">
            <div class="history-header">
                <h1><i class="fa fa-exchange-alt"></i> Dashboard Transaksi</h1>
            </div>
            
            <!-- Info QRIS -->
            <div style="background:linear-gradient(90deg,#1976d2 0%,#2196f3 100%);color:#fff;padding:1rem;border-radius:12px;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
                <div style="font-size:2rem;">
                    <i class="fa fa-qrcode"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:1.1rem;margin-bottom:0.3rem;">Pembayaran QRIS Tersedia!</div>
                    <div style="font-size:0.9rem;opacity:0.9;">Bayar topup dan tagihan dengan mudah menggunakan QRIS. Dukungan e-wallet dan mobile banking.</div>
                </div>
            </div>
            
            <div class="history-tabs">
                <button class="history-tab" id="tab-topup" onclick="showTransaksiTab('topup')">Top Up</button>
                <button class="history-tab" id="tab-tagihan" onclick="showTransaksiTab('tagihan')">Tagihan</button>
                <button class="history-tab" id="tab-riwayat" onclick="showTransaksiTab('riwayat')">Riwayat</button>
            </div>
            <div id="transaksi-content-section">
                <!-- Konten tab akan diisi oleh JS -->
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
<script src="../assets/js/main.js"></script>
<script>
function showComingSoon() {
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
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
    }, 10);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Daftar fitur top up dan tagihan
const topupMenu = [
    { label: 'Top Up E-Wallet', icon: 'fa-wallet', jenis: 'ewallet' },
    { label: 'Pulsa', icon: 'fa-mobile-alt', jenis: 'pulsa' },
    { label: 'Data', icon: 'fa-wifi', jenis: 'data' },
    { label: 'Top Up Game', icon: 'fa-gamepad', jenis: 'game' },
    { label: 'Saldo Google Play', icon: 'fa-play-circle', jenis: 'gplay' }
];
const tagihanMenu = [
    { label: 'PLN', icon: 'fa-bolt', jenis: 'pln' },
    { label: 'TV Kabel & Internet', icon: 'fa-satellite-dish', jenis: 'tv' },
    { label: 'BPJS', icon: 'fa-id-card', jenis: 'bpjs' },
    { label: 'PDAM', icon: 'fa-tint', jenis: 'pdam' },
    { label: 'Edukasi', icon: 'fa-graduation-cap', jenis: 'edukasi' },
    { label: 'Donasi & Zakat', icon: 'fa-hands-helping', jenis: 'donasi' }
];

function showTransaksiTab(tab) {
    document.getElementById('tab-topup').classList.toggle('active', tab === 'topup');
    document.getElementById('tab-tagihan').classList.toggle('active', tab === 'tagihan');
    document.getElementById('tab-riwayat').classList.toggle('active', tab === 'riwayat');
    const section = document.getElementById('transaksi-content-section');
    if (tab === 'topup') {
        let html = `<div class='menu-bar'><div class='menu-title'>Top Up</div><div class='menu-grid'>`;
        topupMenu.forEach(menu => {
            html += `<div class='menu-item' onclick="openTopupForm('${menu.jenis}','${menu.label}')">
                <div class='menu-icon'><i class='fa ${menu.icon}'></i></div>
                <div class='menu-label'>${menu.label}</div>
            </div>`;
        });
        html += `</div></div>`;
        // Tambahkan menu tagihan di bawah menu topup
        html += `<div class='tagihan-menu-bar' style='margin-top:2.5rem;'><div class='tagihan-menu-title'>Tagihan</div><div class='tagihan-menu-grid'>`;
        tagihanMenu.forEach(menu => {
            html += `<div class='tagihan-menu-item' onclick="openTagihanForm('${menu.jenis}','${menu.label}')">
                <div class='tagihan-menu-icon'><i class='fa ${menu.icon}'></i></div>
                <div class='tagihan-menu-label'>${menu.label}</div>
            </div>`;
        });
        html += `</div></div>`;
        section.innerHTML = html;
    } else if (tab === 'tagihan') {
        section.innerHTML = `<div id='tagihan-table-section'>Memuat...</div>`;
        loadTagihanTable();
    } else if (tab === 'riwayat') {
        section.innerHTML = `<h3 style='color:#1976d2;margin-bottom:1.2rem;'>Riwayat Transaksi/Tagihan</h3><div id='riwayat-table-section'>Memuat...</div>`;
        loadRiwayatTable();
    }
}

// Pastikan library QRCode dimuat
function ensureQRCodeLibrary() {
  return new Promise((resolve, reject) => {
    if (typeof QRCode !== 'undefined') {
      resolve();
      return;
    }
    
    // Coba load library QRCode jika belum tersedia
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
    script.onload = () => resolve();
    script.onerror = () => {
      console.warn('Failed to load QRCode library, using fallback');
      resolve(); // Lanjut dengan fallback
    };
    document.head.appendChild(script);
  });
}

// Fungsi untuk menampilkan popup modal topup
function showTopupModal(formHtml) {
  document.getElementById('topup-modal-form').innerHTML = formHtml;
  document.getElementById('topup-modal-overlay').style.display = 'flex';
  
  // Tambahkan tombol QRIS setelah form
  const formContainer = document.getElementById('topup-modal-form');
  const qrisButton = document.createElement('div');
  qrisButton.style.cssText = 'margin-top:1rem;padding-top:1rem;border-top:1px solid #e3e7ed;';
  
  // Tentukan jenis form berdasarkan ID form
  const formId = formContainer.querySelector('form').id;
  const qrisType = formId === 'form-tagihan-dinamis' ? 'tagihan' : 'topup';
  
  qrisButton.innerHTML = `
    <button onclick="showQrisPayment('${qrisType}')" style="width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:linear-gradient(90deg,#1976d2 0%,#2196f3 100%);color:#fff;font-weight:700;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;">
      <i class="fa fa-qrcode"></i> Bayar dengan QRIS
    </button>
  `;
  formContainer.appendChild(qrisButton);
}
function closeTopupModal() {
  document.getElementById('topup-modal-overlay').style.display = 'none';
  document.getElementById('topup-modal-form').innerHTML = '';
}
// Ubah openTopupForm agar form muncul di popup
function openTopupForm(jenis, label) {
    let formHtml = `<form id='form-topup-dinamis' style='max-width:400px;margin:0 auto;'>
        <h4 style='color:#1976d2;margin-bottom:1.2rem;'>${label}</h4>`;
    if (jenis === 'ewallet') {
        formHtml += `<div style='margin-bottom:1rem;'><label>Pilih e-Wallet</label><select id='ewallet' name='keterangan' required style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'><option value=''>-- Pilih --</option><option value='OVO'>OVO</option><option value='DANA'>DANA</option><option value='GoPay'>GoPay</option><option value='ShopeePay'>ShopeePay</option></select></div>`;
        formHtml += `<div style='margin-bottom:1rem;'><label>Nomor HP</label><input type='text' name='nomor_hp' required placeholder='08xxxxxxxxxx' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    } else if (jenis === 'pulsa') {
        formHtml += `<div style='margin-bottom:1rem;'><label>Nomor HP</label><input type='text' name='keterangan' required placeholder='08xxxxxxxxxx' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
        formHtml += `<div style='margin-bottom:1rem;'><label>Provider</label><select name='provider' required style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'><option value=''>-- Pilih --</option><option value='Telkomsel'>Telkomsel</option><option value='Indosat'>Indosat</option><option value='XL'>XL</option><option value='Tri'>Tri</option><option value='Axis'>Axis</option></select></div>`;
    } else if (jenis === 'data') {
        formHtml += `<div style='margin-bottom:1rem;'><label>Nomor HP</label><input type='text' name='keterangan' required placeholder='08xxxxxxxxxx' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
        formHtml += `<div style='margin-bottom:1rem;'><label>Paket Data</label><select name='paket' required style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'><option value=''>-- Pilih --</option><option value='1GB'>1GB</option><option value='5GB'>5GB</option><option value='10GB'>10GB</option><option value='Unlimited'>Unlimited</option></select></div>`;
    } else if (jenis === 'game') {
        formHtml += `<div style='margin-bottom:1rem;'><label>ID Game</label><input type='text' name='keterangan' required placeholder='ID Game' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
        formHtml += `<div style='margin-bottom:1rem;'><label>Game</label><select name='game' required style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'><option value=''>-- Pilih --</option><option value='Mobile Legends'>Mobile Legends</option><option value='Free Fire'>Free Fire</option><option value='PUBG Mobile'>PUBG Mobile</option><option value='Genshin Impact'>Genshin Impact</option></select></div>`;
    } else if (jenis === 'gplay') {
        formHtml += `<div style='margin-bottom:1rem;'><label>Email Google</label><input type='email' name='keterangan' required placeholder='email@gmail.com' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    }
    formHtml += `<div style='margin-bottom:1rem;'><label>Nominal</label><input type='number' name='nominal' min='10000' step='1000' required placeholder='Masukkan nominal' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    formHtml += `<input type='hidden' name='jenis' value='${jenis}' />`;
    formHtml += `<button type='submit' class='btn btn-primary' style='width:100%;'>Kirim</button></form><div id='topup-result'></div>`;
    showTopupModal(formHtml);
}

document.addEventListener('DOMContentLoaded', function() {
    // Buka tab sesuai parameter URL jika ada
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
        showTransaksiTab(tab);
    } else {
        showTransaksiTab('topup');
    }
    document.body.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'form-topup-dinamis') {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            fetch('../upload_upgrade_request.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('topup-result').innerHTML = `<span style='color:#2f855a;'>Transaksi berhasil ditambahkan ke tagihan!</span>`;
                    form.reset();
                } else {
                    document.getElementById('topup-result').innerHTML = `<span style='color:#e53e3e;'>${data.error || 'Gagal menyimpan.'}</span>`;
                }
            })
            .catch(() => {
                document.getElementById('topup-result').innerHTML = `<span style='color:#e53e3e;'>Gagal koneksi server.</span>`;
            });
        }
        // Tambahkan handler untuk form tagihan
        if (e.target && e.target.id === 'form-tagihan-dinamis') {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            fetch('../upload_upgrade_request.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('tagihan-result').innerHTML = `<span style='color:#2f855a;'>Tagihan berhasil ditambahkan!</span>`;
                    form.reset();
                    closeTopupModal();
                    loadTagihanTable();
                } else {
                    document.getElementById('tagihan-result').innerHTML = `<span style='color:#e53e3e;'>${data.error || 'Gagal menyimpan.'}</span>`;
                }
            })
            .catch(() => {
                document.getElementById('tagihan-result').innerHTML = `<span style='color:#e53e3e;'>Gagal koneksi server.</span>`;
            });
        }
    });
});

function loadTagihanTable() {
    fetch('../upload_upgrade_request.php')
        .then(res => res.json())
        .then(rows => {
            if (rows.error) {
                var el = document.getElementById('tagihan-table-section');
                if (el) el.innerHTML = `<span style='color:#e53e3e;'>${rows.error}</span>`;
                return;
            }
            let html = `<table class='history-table'><thead><tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th class='amount'>Nominal</th>
                <th class='status-center'>Status</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr></thead><tbody>`;
            let no = 1;
            let adaData = false;
            if (!rows || rows.length === 0) {
                html += `<tr><td colspan='7' style='text-align:center;color:#888;'>Belum ada tagihan.</td></tr>`;
            } else {
                rows.forEach(row => {
                    if (row.status === 'Draft' || row.status === 'Belum Lunas') {
                        adaData = true;
                        html += `<tr>
                            <td>${no++}</td>
                            <td>${row.jenis}</td>
                            <td>${row.keterangan}</td>
                            <td class='amount'>Rp ${parseInt(row.nominal).toLocaleString('id-ID')}</td>
                            <td class='status-center'><span class='status-badge status-${row.status.replace(/\s/g, '').toLowerCase()}'>${row.status}</span></td>
                            <td>${row.waktu ? row.waktu : '-'}</td>
                            <td>`;
                        if (row.status === 'Draft') {
                            html += `<button class='btn btn-warning' onclick='orderTagihanDB(${row.id}, this)'>Order</button> `;
                            html += `<button class='btn btn-danger' onclick='hapusTagihanDB(${row.id}, this)'>Hapus</button>`;
                        } else if (row.status === 'Belum Lunas') {
                            html += `<button class='btn btn-primary' onclick='bayarTagihanDB(${row.id}, this)'>Bayar</button> `;
                            html += `<button class='btn btn-success' onclick='bayarTagihanQris(${row.id}, "${row.jenis}", "${row.keterangan}", ${row.nominal})'>QRIS</button>`;
                        }
                        html += `</td></tr>`;
                    }
                });
                if (!adaData) {
                    html += `<tr><td colspan='7' style='text-align:center;color:#888;'>Belum ada tagihan.</td></tr>`;
                }
            }
            html += `</tbody></table>`;
            var el = document.getElementById('tagihan-table-section');
            if (el) el.innerHTML = html;
        })
        .catch(() => {
            var el = document.getElementById('tagihan-table-section');
            if (el) el.innerHTML = `<span style='color:#e53e3e;'>Gagal memuat tagihan.</span>`;
        });
}

function bayarTagihanDB(id, btn) {
    btn.disabled = true;
    btn.textContent = 'Memproses...';
    fetch('../upload_upgrade_request.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.textContent = 'Lunas';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            loadTagihanTable();
        } else {
            btn.textContent = 'Bayar';
            btn.disabled = false;
            alert(data.error || 'Gagal update status.');
        }
    })
    .catch(() => {
        btn.textContent = 'Bayar';
        btn.disabled = false;
        alert('Gagal koneksi server.');
    });
}

function orderTagihanDB(id, btn) {
    btn.disabled = true;
    btn.textContent = 'Memproses...';
    fetch('../upload_upgrade_request.php', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.textContent = 'Ordered';
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-primary');
            loadTagihanTable();
        } else {
            btn.textContent = 'Order';
            btn.disabled = false;
            alert(data.error || 'Gagal order.');
        }
    })
    .catch(() => {
        btn.textContent = 'Order';
        btn.disabled = false;
        alert('Gagal koneksi server.');
    });
}

function hapusTagihanDB(id, btn) {
    if (!confirm('Yakin ingin menghapus tagihan draft ini?')) return;
    btn.disabled = true;
    btn.textContent = 'Menghapus...';
    fetch('../upload_upgrade_request.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadTagihanTable();
        } else {
            alert(data.error || 'Gagal menghapus tagihan.');
            btn.disabled = false;
            btn.textContent = 'Hapus';
        }
    })
    .catch(() => {
        alert('Gagal koneksi server.');
        btn.disabled = false;
        btn.textContent = 'Hapus';
    });
}

function loadRiwayatTable() {
    fetch('../upload_upgrade_request.php')
        .then(res => res.json())
        .then(rows => {
            let html = `<table class='history-table'><thead><tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th class='amount'>Nominal</th>
                <th class='status-center'>Status</th>
                <th>Metode</th>
                <th>Waktu</th>
            </tr></thead><tbody>`;
            let no = 1;
            if (!rows || rows.length === 0) {
                html += `<tr><td colspan='7' style='text-align:center;color:#888;'>Belum ada riwayat transaksi/tagihan.</td></tr>`;
            } else {
                rows.forEach(row => {
                    if (row.status === 'Lunas') {
                        // Tentukan metode pembayaran (default: Saldo, QRIS jika ada data)
                        const paymentMethod = row.payment_method || 'Saldo';
                        const methodBadge = paymentMethod === 'QRIS' ? 
                            '<span style="background:#00c853;color:#fff;padding:0.2rem 0.5rem;border-radius:4px;font-size:0.8rem;">QRIS</span>' :
                            '<span style="background:#1976d2;color:#fff;padding:0.2rem 0.5rem;border-radius:4px;font-size:0.8rem;">Saldo</span>';
                        
                        html += `<tr>
                            <td>${no++}</td>
                            <td>${row.jenis}</td>
                            <td>${row.keterangan}</td>
                            <td class='amount'>Rp ${parseInt(row.nominal).toLocaleString('id-ID')}</td>
                            <td class='status-center'><span class='status-badge status-lunas'>Lunas</span></td>
                            <td class='status-center'>${methodBadge}</td>
                            <td>${row.waktu ? row.waktu : '-'}</td>
                        </tr>`;
                    }
                });
                if (no === 1) {
                    html += `<tr><td colspan='7' style='text-align:center;color:#888;'>Belum ada riwayat transaksi/tagihan.</td></tr>`;
                }
            }
            html += `</tbody></table>`;
            var el = document.getElementById('riwayat-table-section');
            if (el) el.innerHTML = html;
        })
        .catch(() => {
            var el = document.getElementById('riwayat-table-section');
            if (el) el.innerHTML = `<span style='color:#e53e3e;'>Gagal memuat riwayat.</span>`;
        });
}

function openTagihanForm(jenis, label) {
    let formHtml = `<form id='form-tagihan-dinamis' style='max-width:400px;margin:0 auto;'>
        <h4 style='color:#1976d2;margin-bottom:1.2rem;'>${label}</h4>`;
    if (jenis === 'pln') {
        formHtml += `<div style='margin-bottom:1rem;'><label>ID Pelanggan PLN</label><input type='text' name='keterangan' required placeholder='ID Pelanggan PLN' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    } else if (jenis === 'tv') {
        formHtml += `<div style='margin-bottom:1rem;'><label>ID Pelanggan TV/Internet</label><input type='text' name='keterangan' required placeholder='ID Pelanggan TV/Internet' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    } else if (jenis === 'bpjs') {
        formHtml += `<div style='margin-bottom:1rem;'><label>No. VA BPJS</label><input type='text' name='keterangan' required placeholder='Nomor Virtual Account BPJS' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    } else if (jenis === 'pdam') {
        formHtml += `<div style='margin-bottom:1rem;'><label>ID Pelanggan PDAM</label><input type='text' name='keterangan' required placeholder='ID Pelanggan PDAM' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    } else if (jenis === 'edukasi') {
        formHtml += `<div style='margin-bottom:1rem;'><label>ID Siswa/NIM</label><input type='text' name='keterangan' required placeholder='ID Siswa/NIM' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    } else if (jenis === 'donasi') {
        formHtml += `<div style='margin-bottom:1rem;'><label>Keterangan Donasi/Zakat</label><input type='text' name='keterangan' required placeholder='Keterangan Donasi/Zakat' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    }
    formHtml += `<div style='margin-bottom:1rem;'><label>Nominal</label><input type='number' name='nominal' min='10000' step='1000' required placeholder='Masukkan nominal' style='width:100%;padding:0.7rem;border-radius:8px;border:1.5px solid #e3e7ed;'></div>`;
    formHtml += `<input type='hidden' name='jenis' value='${jenis}' />`;
    formHtml += `<button type='submit' class='btn btn-primary' style='width:100%;'>Kirim</button></form><div id='tagihan-result'></div>`;
    showTopupModal(formHtml);
}

// Fungsi-fungsi QRIS
let currentQrisData = null;
let qrisCheckInterval = null;

function showQrisPayment(type) {
  // Ambil data dari form yang sedang aktif
  const form = document.querySelector('#topup-modal-form form');
  if (!form) {
    alert('Form tidak ditemukan');
    return;
  }
  
  const formData = new FormData(form);
  const jenis = formData.get('jenis');
  const keterangan = formData.get('keterangan') || formData.get('nomor_hp') || formData.get('provider') || formData.get('paket') || formData.get('game') || formData.get('email');
  const nominal = formData.get('nominal');
  
  if (!jenis || !keterangan || !nominal) {
    alert('Mohon lengkapi semua data terlebih dahulu');
    return;
  }
  
  // Validasi nominal minimum
  if (parseInt(nominal) < 1000) {
    alert('Nominal minimum untuk pembayaran QRIS adalah Rp 1.000');
    return;
  }
  
  // Simpan data QRIS
  currentQrisData = {
    type: type,
    jenis: jenis,
    keterangan: keterangan,
    nominal: nominal,
    timestamp: new Date().getTime()
  };
  
  // Update modal QRIS
  document.getElementById('qris-jenis').textContent = jenis;
  document.getElementById('qris-keterangan').textContent = keterangan;
  document.getElementById('qris-nominal').textContent = `Rp ${parseInt(nominal).toLocaleString('id-ID')}`;
  
  // Tampilkan modal QRIS
  document.getElementById('qris-modal-overlay').style.display = 'flex';
  document.getElementById('topup-modal-overlay').style.display = 'none';
  
  // Pastikan library QRCode dimuat sebelum generate QR code
  ensureQRCodeLibrary().then(() => {
    generateQrisQrCode();
    startQrisPaymentPolling();
  });
}

function generateQrisQrCode() {
  const qrContainer = document.getElementById('qris-qr-code');
  
  // Data QRIS sesuai standar QRIS
  const qrisData = {
    merchant: 'Bank FTI',
    amount: currentQrisData.nominal,
    reference: `QRIS${Date.now()}`,
    description: `${currentQrisData.jenis} - ${currentQrisData.keterangan}`,
    merchantId: 'FTI001',
    terminalId: 'TERM001'
  };
  
  // Buat QR Code menggunakan library QR
  const qrText = JSON.stringify(qrisData);
  
  // Clear container
  qrContainer.innerHTML = '';
  
  // Cek apakah library QRCode tersedia
  if (typeof QRCode !== 'undefined') {
    // Generate QR Code menggunakan QRCode library
    new QRCode(qrContainer, {
      text: qrText,
      width: 200,
      height: 200,
      colorDark: '#000000',
      colorLight: '#FFFFFF',
      correctLevel: QRCode.CorrectLevel.H
    });
  } else {
    // Fallback: buat QR code sederhana dengan canvas
    console.warn('QRCode library not available, using fallback');
    createFallbackQrCode(qrContainer, qrText);
  }
}

function createFallbackQrCode(container, text) {
  // Buat QR code sederhana dengan canvas sebagai fallback
  const canvas = document.createElement('canvas');
  canvas.width = 200;
  canvas.height = 200;
  const ctx = canvas.getContext('2d');
  
  // Background putih
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, 200, 200);
  
  // Border biru
  ctx.strokeStyle = '#1976d2';
  ctx.lineWidth = 3;
  ctx.strokeRect(10, 10, 180, 180);
  
  // QR Code pattern sederhana (simulasi)
  ctx.fillStyle = '#000000';
  const patternSize = 8;
  const startX = 20;
  const startY = 20;
  
  // Buat pattern QR code sederhana
  for (let i = 0; i < 20; i++) {
    for (let j = 0; j < 20; j++) {
      // Gunakan hash sederhana dari text untuk menentukan pixel
      const hash = (text.charCodeAt((i * 20 + j) % text.length) + i + j) % 2;
      if (hash === 1) {
        ctx.fillRect(startX + i * patternSize, startY + j * patternSize, patternSize - 1, patternSize - 1);
      }
    }
  }
  
  // Tambahkan logo Bank FTI di tengah
  ctx.fillStyle = '#1976d2';
  ctx.font = 'bold 14px Arial';
  ctx.textAlign = 'center';
  ctx.fillText('Bank FTI', 100, 110);
  
  // Tambahkan nominal di bawah
  ctx.font = 'bold 12px Arial';
  ctx.fillText(`Rp ${parseInt(currentQrisData.nominal).toLocaleString('id-ID')}`, 100, 130);
  
  container.appendChild(canvas);
}

function startQrisPaymentPolling() {
  // Hentikan polling sebelumnya jika ada
  if (qrisCheckInterval) {
    clearInterval(qrisCheckInterval);
  }
  
  // Mulai polling setiap 3 detik
  qrisCheckInterval = setInterval(() => {
    checkQrisPayment();
  }, 3000);
  
  // Set timeout untuk pembayaran (5 menit)
  setTimeout(() => {
    if (qrisCheckInterval) {
      clearInterval(qrisCheckInterval);
      const statusElement = document.getElementById('qris-status');
      statusElement.innerHTML = `
        <div style="background:#ffebee;color:#c62828;padding:0.8rem;border-radius:8px;font-weight:600;">
          <i class="fa fa-exclamation-triangle"></i> Pembayaran Timeout
        </div>
      `;
    }
  }, 300000); // 5 menit
}

function checkQrisPayment() {
  if (!currentQrisData) return;
  
  // Simulasi cek status pembayaran
  const elapsed = Date.now() - currentQrisData.timestamp;
  const statusElement = document.getElementById('qris-status');
  
  // Simulasi: pembayaran berhasil setelah 10 detik
  if (elapsed > 10000) {
    // Pembayaran berhasil
    statusElement.innerHTML = `
      <div style="background:#e8f5e8;color:#2f855a;padding:0.8rem;border-radius:8px;font-weight:600;">
        <i class="fa fa-check-circle"></i> Pembayaran Berhasil!
      </div>
    `;
    
    // Hentikan polling
    clearInterval(qrisCheckInterval);
    
    // Proses pembayaran
    processQrisPayment();
    
    // Tutup modal setelah 2 detik
    setTimeout(() => {
      closeQrisModal();
      alert('Pembayaran QRIS berhasil! Transaksi telah diproses.');
    }, 2000);
  } else {
    // Masih menunggu
    const remaining = Math.ceil((10000 - elapsed) / 1000);
    statusElement.innerHTML = `
      <div style="background:#e3f2fd;color:#1976d2;padding:0.8rem;border-radius:8px;font-weight:600;">
        <i class="fa fa-clock"></i> Menunggu Pembayaran... (${remaining}s)
      </div>
    `;
  }
}

function processQrisPayment() {
  if (!currentQrisData) return;
  
  if (currentQrisData.type === 'tagihan' && currentQrisData.id) {
    // Pembayaran tagihan yang sudah ada
    fetch('../upload_upgrade_request.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'id=' + currentQrisData.id
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        console.log('Pembayaran QRIS tagihan berhasil diproses');
        // Simpan riwayat pembayaran QRIS
        saveQrisPaymentHistory('tagihan', currentQrisData.id, currentQrisData.nominal);
        // Refresh tabel
        if (typeof loadTagihanTable === 'function') {
          loadTagihanTable();
        }
        if (typeof loadRiwayatTable === 'function') {
          loadRiwayatTable();
        }
      }
    })
    .catch(error => {
      console.error('Error processing QRIS tagihan payment:', error);
    });
  } else {
    // Pembayaran topup baru
    const formData = new FormData();
    formData.append('jenis', currentQrisData.jenis);
    formData.append('keterangan', currentQrisData.keterangan);
    formData.append('nominal', currentQrisData.nominal);
    
    fetch('../upload_upgrade_request.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Jika berhasil, langsung order dan bayar
        if (data.id) {
          // Order tagihan
          fetch('../upload_upgrade_request.php', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + data.id
          })
          .then(res => res.json())
          .then(orderData => {
            if (orderData.success) {
              // Bayar tagihan
              fetch('../upload_upgrade_request.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + data.id
              })
              .then(res => res.json())
              .then(payData => {
                if (payData.success) {
                  console.log('Pembayaran QRIS berhasil diproses');
                  // Simpan riwayat pembayaran QRIS
                  saveQrisPaymentHistory('topup', data.id, currentQrisData.nominal);
                  // Refresh tabel jika ada
                  if (typeof loadTagihanTable === 'function') {
                    loadTagihanTable();
                  }
                  if (typeof loadRiwayatTable === 'function') {
                    loadRiwayatTable();
                  }
                }
              });
            }
          });
        }
      }
    })
    .catch(error => {
      console.error('Error processing QRIS payment:', error);
    });
  }
}

function saveQrisPaymentHistory(type, tagihanId, nominal) {
  // Simpan riwayat pembayaran QRIS ke database
  const paymentData = {
    type: type,
    tagihan_id: tagihanId,
    nominal: nominal,
    payment_method: 'QRIS',
    timestamp: new Date().toISOString(),
    status: 'success'
  };
  
  // Kirim ke endpoint untuk menyimpan riwayat (opsional)
  fetch('../save_qris_history.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(paymentData)
  })
  .then(res => res.json())
  .then(data => {
    console.log('Riwayat pembayaran QRIS tersimpan:', data);
  })
  .catch(error => {
    console.error('Error saving QRIS payment history:', error);
  });
}

function closeQrisModal() {
  document.getElementById('qris-modal-overlay').style.display = 'none';
  
  // Hentikan polling
  if (qrisCheckInterval) {
    clearInterval(qrisCheckInterval);
    qrisCheckInterval = null;
  }
  
  // Reset data
  currentQrisData = null;
  
  // Reset QR code
  const qrContainer = document.getElementById('qris-qr-code');
  qrContainer.innerHTML = `
    <div style="text-align:center;">
      <i class="fa fa-qrcode" style="font-size:3rem;margin-bottom:0.5rem;"></i>
      <div>QR Code akan muncul di sini</div>
    </div>
  `;
  
  // Reset status
  document.getElementById('qris-status').innerHTML = `
    <div style="background:#e3f2fd;color:#1976d2;padding:0.8rem;border-radius:8px;font-weight:600;">
      <i class="fa fa-clock"></i> Menunggu Pembayaran...
    </div>
  `;
}

function bayarTagihanQris(id, jenis, keterangan, nominal) {
  // Simpan data QRIS untuk tagihan yang sudah ada
  currentQrisData = {
    type: 'tagihan',
    id: id,
    jenis: jenis,
    keterangan: keterangan,
    nominal: nominal,
    timestamp: new Date().getTime()
  };
  
  // Update modal QRIS
  document.getElementById('qris-jenis').textContent = jenis;
  document.getElementById('qris-keterangan').textContent = keterangan;
  document.getElementById('qris-nominal').textContent = `Rp ${parseInt(nominal).toLocaleString('id-ID')}`;
  
  // Tampilkan modal QRIS
  document.getElementById('qris-modal-overlay').style.display = 'flex';
  
  // Pastikan library QRCode dimuat sebelum generate QR code
  ensureQRCodeLibrary().then(() => {
    generateQrisQrCode();
    startQrisPaymentPolling();
  });
}
</script>

<!-- Modal Topup -->
<div id="topup-modal-overlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:9999;justify-content:center;align-items:center;">
  <div id="topup-modal-content" style="background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:2.2rem 2.5rem;max-width:420px;width:95vw;position:relative;">
    <button onclick="closeTopupModal()" style="position:absolute;top:1.1rem;right:1.1rem;background:none;border:none;font-size:1.5rem;color:#888;cursor:pointer;">&times;</button>
    <div id="topup-modal-form"></div>
  </div>
</div>

<!-- Modal QRIS -->
<div id="qris-modal-overlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:10000;justify-content:center;align-items:center;">
  <div id="qris-modal-content" style="background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:2.2rem 2.5rem;max-width:400px;width:95vw;position:relative;text-align:center;">
    <button onclick="closeQrisModal()" style="position:absolute;top:1.1rem;right:1.1rem;background:none;border:none;font-size:1.5rem;color:#888;cursor:pointer;">&times;</button>
    <div style="font-size:1.3rem;font-weight:700;color:#1976d2;margin-bottom:1.2rem;">
      <i class="fa fa-qrcode"></i> Pembayaran QRIS
    </div>
    <div id="qris-payment-info" style="margin-bottom:1.5rem;text-align:left;">
      <div style="background:#f8fafc;padding:1rem;border-radius:8px;border:1px solid #e3e7ed;">
        <p style="margin:0 0 0.5rem 0;font-weight:600;color:#1976d2;">Detail Pembayaran:</p>
        <p style="margin:0 0 0.3rem 0;"><strong>Jenis:</strong> <span id="qris-jenis"></span></p>
        <p style="margin:0 0 0.3rem 0;"><strong>Keterangan:</strong> <span id="qris-keterangan"></span></p>
        <p style="margin:0 0 0.3rem 0;"><strong>Nominal:</strong> <span id="qris-nominal"></span></p>
        <p style="margin:0;"><strong>Merchant:</strong> Bank FTI</p>
      </div>
    </div>
    <div id="qris-qr-container" style="margin-bottom:1.5rem;">
      <div id="qris-qr-code" style="width:200px;height:200px;margin:0 auto;background:#f8fafc;border:2px dashed #e3e7ed;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#888;">
        <div style="text-align:center;">
          <i class="fa fa-qrcode" style="font-size:3rem;margin-bottom:0.5rem;"></i>
          <div>QR Code akan muncul di sini</div>
        </div>
      </div>
    </div>
    <div style="margin-bottom:1.5rem;">
      <p style="color:#666;font-size:0.9rem;margin-bottom:0.5rem;">Cara Pembayaran:</p>
      <ol style="text-align:left;color:#666;font-size:0.9rem;margin:0;padding-left:1.5rem;">
        <li>Buka aplikasi e-wallet atau mobile banking Anda</li>
        <li>Pilih fitur "Scan QR" atau "QRIS"</li>
        <li>Arahkan kamera ke QR code di atas</li>
        <li>Konfirmasi pembayaran sesuai nominal yang tertera</li>
        <li>Pembayaran akan diproses otomatis</li>
      </ol>
    </div>
    <div id="qris-status" style="margin-bottom:1rem;">
      <div style="background:#e3f2fd;color:#1976d2;padding:0.8rem;border-radius:8px;font-weight:600;">
        <i class="fa fa-clock"></i> Menunggu Pembayaran...
      </div>
    </div>
    <button onclick="checkQrisPayment()" style="width:100%;padding:0.8rem 0;font-size:1.05rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;margin-bottom:0.5rem;">
      <i class="fa fa-refresh"></i> Cek Status Pembayaran
    </button>
    <button onclick="closeQrisModal()" style="width:100%;padding:0.7rem 0;font-size:1rem;border-radius:8px;background:#eee;color:#1976d2;font-weight:600;border:none;cursor:pointer;">
      Tutup
    </button>
  </div>
</div>
</body>
</html> 