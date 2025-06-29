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
            table-layout: fixed;
        }
        .history-table th, .history-table td {
            padding: 1rem 0.7rem;
            border-bottom: 1px solid #e3e7ed;
            text-align: center;
            font-size: 1rem;
            vertical-align: middle;
        }
        .history-table th:nth-child(1), .history-table td:nth-child(1) { width: 20%; }
        .history-table th:nth-child(2), .history-table td:nth-child(2) { width: 25%; }
        .history-table th:nth-child(3), .history-table td:nth-child(3) { width: 25%; }
        .history-table th:nth-child(4), .history-table td:nth-child(4) { width: 15%; }
        .history-table th:nth-child(5), .history-table td:nth-child(5) { width: 15%; }
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
        @media print {
            body, html {
                background: #fff !important;
                color: #000 !important;
            }
            .main-navbar, .sidebar, .sidebar-profile, .sidebar-logout, .sidebar ul, .sidebar li, .sidebar a {
                display: none !important;
            }
            .dashboard-layout, .main-content, .dashboard-section {
                box-shadow: none !important;
                background: #fff !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            .history-header {
                margin: 0 0 8px 0 !important;
                padding: 0 !important;
                text-align: center !important;
            }
            .history-section {
                padding: 0 !important;
                margin: 0 !important;
            }
            .history-table {
                width: 90% !important;
                margin: 0 auto !important;
                border-collapse: collapse !important;
                text-align: center !important;
                table-layout: fixed !important;
            }
            .history-table th, .history-table td {
                background: #fff !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                font-size: 12pt !important;
                padding: 4px 8px !important;
                text-align: center !important;
                vertical-align: middle !important;
            }
            .history-table th:nth-child(1), .history-table td:nth-child(1) { width: 18%; }
            .history-table th:nth-child(2), .history-table td:nth-child(2) { width: 18%; }
            .history-table th:nth-child(3), .history-table td:nth-child(3) { width: 22%; }
            .history-table th:nth-child(4), .history-table td:nth-child(4) { width: 18%; }
            .history-table th:nth-child(5), .history-table td:nth-child(5) { width: 24%; }
            .history-tabs, .history-tab {
                display: none !important;
            }
            h1, h2, h3, h4, h5, h6 {
                color: #000 !important;
                text-align: center !important;
            }
            .transfer-table, .transfer-table th, .transfer-table td {
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                background: #fff !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                font-size: 12pt !important;
                padding: 4px 8px !important;
                text-align: center !important;
                vertical-align: middle !important;
            }
            .transfer-table th:nth-child(1), .transfer-table td:nth-child(1) { width: 60%; }
            .transfer-table th:nth-child(2), .transfer-table td:nth-child(2) { width: 40%; }
        }
        .transfer-filter-btn {
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            background: #e3e7ed;
            color: #1976d2;
            transition: background 0.2s, color 0.2s;
        }
        .transfer-filter-btn.active {
            background: #1976d2;
            color: #fff;
        }
        .detail-col {
            text-align: center;
            vertical-align: middle !important;
            width: 48px;
            padding: 0 !important;
        }
        .view-detail-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 50%;
            transition: background 0.18s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .view-detail-btn:hover {
            background: #e3f2fd;
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
            <div class="sidebar-avatar"><?= strtoupper(substr($name,0,1)) ?></div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <ul>
            <li><a href="dashboard_profil.php"><i class="fa fa-user"></i> Profil</a></li>
            <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard_transaksi.php"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
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
            <div id="transfer-filter-section" style="display:none;justify-content:left;gap:0.7rem;margin-bottom:1.2rem;">
                <button class="transfer-filter-btn active" id="filter-keluar" onclick="setTransferFilter('keluar')">Saldo Keluar</button>
                <button class="transfer-filter-btn" id="filter-masuk" onclick="setTransferFilter('masuk')">Saldo Masuk</button>
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
    const filterSection = document.getElementById('transfer-filter-section');
    if (tab === 'transfer') {
        filterSection.style.display = 'flex';
        setTransferFilter('keluar'); // Default to saldo keluar
    } else {
        filterSection.style.display = 'none';
        fetchAndRenderHistory(tab);
    }
}

function setTransferFilter(type) {
    document.getElementById('filter-keluar').classList.toggle('active', type === 'keluar');
    document.getElementById('filter-masuk').classList.toggle('active', type === 'masuk');
    fetchAndRenderHistory(type);
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
        thead.innerHTML = `<tr><th>Tanggal</th><th>e-Wallet</th><th>Rekening</th><th>Nominal</th><th>Ulasan</th><th class='detail-col'>Detail</th></tr>`;
    } else if (tab === 'masuk') {
        thead.innerHTML = `<tr><th>Tanggal</th><th>Dari Rekening</th><th>Nominal</th><th>Ulasan</th><th class='detail-col'>Detail</th></tr>`;
    } else if (tab === 'keluar') {
        thead.innerHTML = `<tr><th>Tanggal</th><th>Ke Rekening</th><th>Nominal</th><th>Ulasan</th><th class='detail-col'>Detail</th></tr>`;
    } else {
        thead.innerHTML = `<tr><th>Tanggal</th><th>Rekening Tujuan</th><th>Nominal</th><th>Ulasan</th><th class='detail-col'>Detail</th></tr>`;
    }
    if (!Array.isArray(filtered) || filtered.length === 0) {
        empty.style.display = 'block';
        return;
    }
    empty.style.display = 'none';
    filtered.forEach((item, idx) => {
        const tr = document.createElement('tr');
        let detailBtn = `<button class='view-detail-btn' title='Lihat Detail' data-idx='${idx}'><i class='fa fa-eye' style='color:#1976d2;font-size:1.18em;'></i></button>`;
        let stars = '';
        if (item.rating && item.rating > 0) {
            for (let i = 1; i <= 5; i++) {
                stars += `<span style='color:${i <= item.rating ? '#FFD600' : '#ccc'};font-size:1.1em;'>${i <= item.rating ? '★' : '☆'}</span>`;
            }
        }
        let reviewText = (item.review && item.review.trim() !== "" && item.review !== "-") ? item.review : "-";
        let ulasanCol = `${stars} ${reviewText}`;
        if (tab === 'topup') {
            tr.innerHTML = `<td>${item.tanggal}</td><td>${item.ewallet}</td><td>${item.rekening}</td><td>Rp ${parseInt(item.nominal).toLocaleString('id-ID')}</td><td>${ulasanCol}</td><td class='detail-col'>${detailBtn}</td>`;
        } else {
            tr.innerHTML = `<td>${item.tanggal}</td><td>${item.rekening}</td><td>Rp ${parseInt(item.nominal).toLocaleString('id-ID')}</td><td>${ulasanCol}</td><td class='detail-col'>${detailBtn}</td>`;
        }
        tbody.appendChild(tr);
    });
    setTimeout(() => {
        document.querySelectorAll('.view-detail-btn').forEach(btn => {
            btn.onclick = function() {
                const idx = this.getAttribute('data-idx');
                showDetailModal(filtered[idx]);
            };
        });
    }, 10);
}

const detailModal = document.createElement('div');
detailModal.id = 'detail-modal-popup';
detailModal.style.display = 'none';
detailModal.style.position = 'fixed';
detailModal.style.top = '0';
detailModal.style.left = '0';
detailModal.style.width = '100vw';
detailModal.style.height = '100vh';
detailModal.style.background = 'rgba(0,0,0,0.35)';
detailModal.style.zIndex = '99999';
detailModal.style.alignItems = 'center';
detailModal.style.justifyContent = 'center';
detailModal.innerHTML = `<div id='detail-modal-content' style='background:#fff;border-radius:16px;max-width:400px;width:90vw;padding:2rem 1.2rem;box-shadow:0 8px 32px rgba(0,0,0,0.18);position:relative;text-align:left;'></div>`;
document.body.appendChild(detailModal);

function showDetailModal(data) {
    let html = `<div style='display:flex;align-items:center;justify-content:space-between;margin-bottom:0.7rem;'>`;
    html += `<div style='font-size:1.35rem;font-weight:800;color:#1976d2;display:flex;align-items:center;gap:0.6rem;'><i class='fa fa-eye'></i> Detail Transaksi</div>`;
    html += `<button id='btn-print-detail' title='Cetak' style='background:none;border:none;cursor:pointer;padding:6px 10px;border-radius:50%;transition:background 0.18s;'><i class='fa fa-print' style='color:#1976d2;font-size:1.25em;'></i></button>`;
    html += `</div>`;
    html += `<div style='margin-bottom:1.2rem;color:#555;font-size:1.08rem;'>Berikut detail lengkap transaksi Anda:</div>`;
    html += `<table id='detail-print-table' style='width:100%;font-size:1.08rem;border-radius:10px;overflow:hidden;'>`;
    const labelMap = {
        tanggal: 'Tanggal',
        ewallet: 'e-Wallet',
        rekening: 'Rekening',
        nominal: 'Nominal',
        review: 'Ulasan',
        'Dari Rekening': 'Dari Rekening',
        'Ke Rekening': 'Ke Rekening',
    };
    for (const key in data) {
        if (key === 'id') continue;
        let label = labelMap[key] || key.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase());
        let value = data[key];
        if (key === 'nominal') value = 'Rp ' + parseInt(value).toLocaleString('id-ID');
        html += `<tr><td style='font-weight:600;color:#1976d2;padding:7px 0;width:44%;vertical-align:top;'>${label}</td><td style='padding:7px 0;'>${value}</td></tr>`;
    }
    html += `</table>`;
    html += `<button onclick='document.getElementById("detail-modal-popup").style.display="none"' style='margin-top:1.7rem;width:100%;padding:0.9rem 0;font-size:1.08rem;border-radius:8px;background:#1976d2;color:#fff;font-weight:700;border:none;cursor:pointer;'>Tutup</button>`;
    document.getElementById('detail-modal-content').innerHTML = html;
    detailModal.style.display = 'flex';
    setTimeout(() => {
        const btnPrint = document.getElementById('btn-print-detail');
        if (btnPrint) {
            btnPrint.onclick = function() {
                const printTable = document.getElementById('detail-print-table').outerHTML;
                const style = `
                <style>
                body { font-family:Segoe UI,Arial,sans-serif; background:#fff; }
                .print-title { color:#1976d2; font-size:1.35rem; font-weight:800; display:flex; align-items:center; gap:0.6rem; margin-bottom:1.1rem; }
                .print-desc { margin-bottom:1.2rem; color:#555; font-size:1.08rem; }
                table { border-collapse:collapse; min-width:340px; font-size:1.08rem; margin-bottom:1.2rem; }
                td { padding:7px 0; vertical-align:top; }
                td:first-child { color:#1976d2; font-weight:600; width:44%; padding-right:18px; }
                td:last-child { color:#222; }
                @media print { body,html{background:#fff!important;} }
                </style>
                `;
                const win = window.open('', '', 'width=500,height=600');
                win.document.write('<html><head><title>Cetak Detail Transaksi</title>' + style + '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"></head><body>');
                win.document.write('<div class="print-title"><i class="fa fa-eye"></i> Detail Transaksi</div>');
                win.document.write('<div class="print-desc">Berikut detail lengkap transaksi Anda:</div>');
                win.document.write(printTable);
                win.document.write('</body></html>');
                win.document.close();
                win.focus();
                setTimeout(function(){ win.print(); win.close(); }, 500);
            };
        }
    }, 50);
}

detailModal.onclick = function(e) { if (e.target === detailModal) detailModal.style.display = 'none'; };

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