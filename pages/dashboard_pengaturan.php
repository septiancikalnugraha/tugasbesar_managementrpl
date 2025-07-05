<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/auth.php';
$auth = new Auth();
$user_data = $auth->getUserData($_SESSION['user_id']);
$name = $user_data['full_name'] ?? '';
$role = $user_data['role'] ?? '';
$gender = $user_data['gender'] ?? '';

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
    <title>Pengaturan - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .navbar-content {
            max-width: unset !important;
            margin: 0 !important;
            padding-left: 0.7rem !important;
            padding-right: 0 !important;
            justify-content: flex-start !important;
            display: flex !important;
        }
        .navbar-logo {
            margin-left: 0 !important;
            gap: 0.7rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .navbar-logo img {
            margin-left: 0 !important;
            margin-right: 10px !important;
        }
        .settings-menu {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }
        .settings-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            text-decoration: none;
            color: #333;
            background: #fff;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .settings-item:hover {
            background: #f5f5f5;
        }
        .settings-item i:first-child {
            font-size: 1.2rem;
            width: 2rem;
            color: #1976d2;
        }
        .settings-item span {
            flex: 1;
            margin-left: 0.5rem;
            font-size: 1rem;
        }
        .settings-item i:last-child {
            color: #999;
            font-size: 0.9rem;
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
                        <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2.5px solid #fff;">
                    <?php else: ?>
                        <div style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:#1976d2;color:#fff;font-size:1.7rem;font-weight:700;border-radius:50%;border:2.5px solid #fff;">
                            <?= getInitials($name) ?>
                        </div>
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
            <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="dashboard_transaksi.php"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="#" class="active"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="dashboard-section" style="width:100%;margin:0;box-shadow:none;background:#fff;border-radius:32px;padding:1.5rem;position:relative;">
            <!-- Profil User di Pengaturan -->
            <div style="display:flex;align-items:center;gap:1.2rem;margin-bottom:2rem;background:#1976d2;padding:1.2rem 1.5rem 1.2rem 1.2rem;border-radius:18px;">
                <div style="width:60px;height:60px;flex-shrink:0;">
                    <?php if (isset($user_data['kategori']) && $user_data['kategori'] === 'prioritas'): ?>
                        <?php if (strtolower($gender) === 'laki-laki'): ?>
                            <img src="../image/prioritas_male.png" alt="Prioritas Laki-laki" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2.5px solid #fff;">
                        <?php elseif (strtolower($gender) === 'perempuan'): ?>
                            <img src="../image/prioritas_female.png" alt="Prioritas Perempuan" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2.5px solid #fff;">
                        <?php else: ?>
                            <img src="../image/default_avatar.png" alt="Prioritas" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2.5px solid #fff;">
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!empty($user_data['profile_photo'])): ?>
                            <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2.5px solid #fff;">
                        <?php else: ?>
                            <div style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:#1976d2;color:#fff;font-size:1.7rem;font-weight:700;border-radius:50%;border:2.5px solid #fff;">
                                <?= getInitials($name) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div style="display:flex;flex-direction:column;gap:0.2rem;">
                    <div style="font-size:1.18rem;font-weight:700;color:#fff;line-height:1.2;"> <?= htmlspecialchars($name) ?> </div>
                    <div style="font-size:1.01rem;color:#c7e0ff;letter-spacing:0.5px;"> <?= htmlspecialchars($user_data['account_number'] ?? '-') ?> </div>
                </div>
            </div>
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;height:100%;">
                <div style="font-size:1.3rem;font-weight:800;letter-spacing:0.5px;margin-bottom:1.2rem;color:#1976d2;">Pengaturan</div>
                <div class="settings-menu">
                    <a href="dashboard_profil.php" class="settings-item">
                        <i class="fas fa-user"></i>
                        <span>Profil Saya</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-key"></i>
                        <span>Ubah Password</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-envelope"></i>
                        <span>Ubah Email / No. HP</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-bell"></i>
                        <span>Pengaturan Notifikasi</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Keamanan Akun</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-language"></i>
                        <span>Bahasa & Tema</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-star"></i>
                        <span>Rekening Favorit</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-wallet"></i>
                        <span>E-Wallet Terkait</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-user-secret"></i>
                        <span>Privasi & Data</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-trash-alt"></i>
                        <span>Hapus Akun</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-question-circle"></i>
                        <span>Pusat Bantuan</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="chat_bankfti.php" class="settings-item">
                        <i class="fas fa-comments"></i>
                        <span>Chat dengan Bank FTI</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item">
                        <i class="fas fa-comment-dots"></i>
                        <span>Beri Masukan</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
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

<!-- Modal Bahasa & Tema -->
<div id="modal-bahasa-tema" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);align-items:center;justify-content:center;">
  <div style="background:#fff;padding:2rem 2.5rem;border-radius:18px;max-width:350px;width:90vw;box-shadow:0 8px 32px rgba(25,118,210,0.13);position:relative;">
    <button id="close-modal-bahasa-tema" style="position:absolute;top:12px;right:18px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;">&times;</button>
    <h3 style="margin-bottom:1.2rem;color:#1976d2;font-weight:700;">Bahasa & Tema</h3>
    <div style="margin-bottom:1.2rem;">
      <label for="select-bahasa" style="font-weight:600;">Bahasa</label>
      <select id="select-bahasa" style="width:100%;padding:0.6rem;margin-top:0.4rem;border-radius:8px;border:1.5px solid #e3e7ed;">
        <option value="id">Bahasa Indonesia</option>
        <option value="en">English</option>
      </select>
    </div>
    <div>
      <label for="select-tema" style="font-weight:600;">Tema</label>
      <select id="select-tema" style="width:100%;padding:0.6rem;margin-top:0.4rem;border-radius:8px;border:1.5px solid #e3e7ed;">
        <option value="light">Terang</option>
        <option value="dark">Gelap</option>
      </select>
    </div>
    <button id="simpan-bahasa-tema" style="margin-top:1.5rem;width:100%;background:#1976d2;color:#fff;border:none;padding:0.8rem;border-radius:8px;font-weight:700;font-size:1.08rem;cursor:pointer;">Simpan</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Tampilkan modal saat klik menu Bahasa & Tema
  document.querySelectorAll('.settings-item').forEach(function(item) {
    if (item.textContent.includes('Bahasa') || item.textContent.includes('Language')) {
      item.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('modal-bahasa-tema').style.display = 'flex';
      });
    }
  });
  document.getElementById('close-modal-bahasa-tema').onclick = function() {
    document.getElementById('modal-bahasa-tema').style.display = 'none';
  };
  // Load preferensi dari localStorage
  const bahasa = localStorage.getItem('bahasa') || 'id';
  const tema = localStorage.getItem('tema') || 'light';
  document.getElementById('select-bahasa').value = bahasa;
  document.getElementById('select-tema').value = tema;
  // Simpan preferensi
  document.getElementById('simpan-bahasa-tema').onclick = function() {
    localStorage.setItem('bahasa', document.getElementById('select-bahasa').value);
    localStorage.setItem('tema', document.getElementById('select-tema').value);
    document.getElementById('modal-bahasa-tema').style.display = 'none';
    window.location.reload();
  };
});
</script>
</body>
</html> 