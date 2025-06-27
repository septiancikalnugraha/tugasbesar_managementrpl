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
$page_title = 'Riwayat Transaksi';

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
    <title>Riwayat Transaksi - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            margin-bottom: 1.5rem;
        }
        .history-table th, .history-table td {
            padding: 1rem 0.7rem;
            border-bottom: 1px solid #e3e7ed;
            text-align: left;
            font-size: 1rem;
        }
        .history-table th {
            background: #f4f6f8;
            color: #1976d2;
            font-weight: 700;
        }
        .history-table td.amount {
            text-align: right;
            font-weight: 700;
            color: #1976d2;
        }
        .history-empty {
            text-align: center;
            color: #888;
            margin: 2rem 0 1rem 0;
        }
        @media (max-width: 700px) {
            .history-section { padding: 1.2rem 0.5rem 1.2rem 0.5rem; border-radius: 12px; }
            .history-header h1 { font-size: 1.1rem; }
            .history-tabs { gap: 0.5rem; }
            .history-tab { font-size: 0.95rem; padding: 0.7rem 0; }
            .history-table th, .history-table td { font-size: 0.92rem; padding: 0.7rem 0.3rem; }
        }
    </style>
</head>
<body>
<nav class="main-navbar">
    <div class="navbar-content">
        <div class="navbar-logo">
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
            <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="#" class="active"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="history-section">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;">
                <h1 style="margin:0;font-size:1.35rem;font-weight:700;color:#222;display:flex;align-items:center;gap:0.6rem;"><i class="fa fa-history"></i> Riwayat Transaksi</h1>
                <button id="btn-cetak-history" title="Cetak" style="display:flex;align-items:center;gap:0.5rem;background:none;border:none;color:#1976d2;font-size:1.1rem;font-weight:600;cursor:pointer;padding:0.35rem 1.1rem 0.35rem 0.8rem;border-radius:6px;transition:background 0.2s;">
                    <i class="fa fa-print" style="font-size:1.3rem;"></i>
                    <span style="font-size:1.05rem;">Cetak</span>
                </button>
            </div>
            <div class="history-tabs">
                <button class="history-tab active" id="tab-topup" onclick="showHistoryTab('topup')">Top Up</button>
                <button class="history-tab" id="tab-transfer" onclick="showHistoryTab('transfer')">Transfer</button>
            </div>
            <div id="history-table-section">
                <table class="history-table" id="history-table">
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <div class="history-empty" id="history-empty" style="display:none;">Belum ada riwayat.</div>
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
let allHistoryData = [];
let currentTab = 'topup';

function fetchAndRenderHistory(tab) {
    fetch(`../get_history.php?type=${tab}`)
        .then(res => res.json())
        .then(data => {
            allHistoryData = Array.isArray(data) ? data.map(item => ({...item, kategori: tab})) : [];
            renderHistoryTable(tab);
        })
        .catch(() => {
            allHistoryData = [];
            renderHistoryTable(tab);
        });
}

function showHistoryTab(tab) {
    currentTab = tab;
    document.getElementById('tab-topup').classList.toggle('active', tab === 'topup');
    document.getElementById('tab-transfer').classList.toggle('active', tab === 'transfer');
    fetchAndRenderHistory(tab);
}

document.addEventListener('DOMContentLoaded', function() {
    fetchAndRenderHistory('topup');
});

function renderHistoryTable(tab) {
    const table = document.getElementById('history-table');
    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    const empty = document.getElementById('history-empty');
    tbody.innerHTML = '';
    let filtered = allHistoryData;
    if (tab === 'topup') {
        thead.innerHTML = `<tr><th>Tanggal</th><th>e-Wallet</th><th>Rekening</th><th style='text-align:right;'>Nominal</th><th>Ulasan</th></tr>`;
    } else {
        thead.innerHTML = `<tr><th>Tanggal</th><th>Rekening Tujuan</th><th style='text-align:right;'>Nominal</th><th>Ulasan</th></tr>`;
    }
    if (!Array.isArray(filtered) || filtered.length === 0) {
        empty.style.display = 'block';
        return;
    }
    empty.style.display = 'none';
    filtered.forEach(item => {
        const tr = document.createElement('tr');
        if (tab === 'topup') {
            tr.innerHTML = `<td>${item.tanggal}</td><td>${item.ewallet}</td><td>${item.rekening}</td><td class='amount'>Rp ${parseInt(item.nominal).toLocaleString('id-ID')}</td><td>${item.review ? item.review : '-'}</td>`;
        } else {
            tr.innerHTML = `<td>${item.tanggal}</td><td>${item.rekening}</td><td class='amount'>Rp ${parseInt(item.nominal).toLocaleString('id-ID')}</td><td>${item.review ? item.review : '-'}</td>`;
        }
        tbody.appendChild(tr);
    });
}
document.getElementById('btn-cetak-history').onclick = function() {
    var table = document.getElementById('history-table');
    var tab = currentTab;
    var title = tab === 'topup' ? 'Riwayat Top Up' : 'Riwayat Transfer';
    var style = '<style>body{font-family:Segoe UI,Arial,sans-serif;}table{width:100%;border-collapse:collapse;}th,td{padding:12px 8px;}th{background:#f4f6f8;color:#1976d2;}td{text-align:left;}th:last-child,td:last-child{text-align:right;}</style>';
    var win = window.open('', '', 'width=800,height=600');
    win.document.write('<html><head><title>'+title+'</title>' + style + '</head><body>');
    win.document.write('<h2 style="color:#1976d2;">'+title+'</h2>');
    win.document.write(table.outerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); win.close(); }, 500);
};
</script>
</body>
</html> 