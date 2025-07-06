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
$account_number = $user_data['account_number'] ?? ($_SESSION['account_number'] ?? '-');
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
    <title>Pengaturan - Teller</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
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
        .dashboard-section { width:100%;margin:0;box-shadow:none;background:#fff;border-radius:32px;padding:1.5rem;position:relative; }
        .settings-menu { display: flex; flex-direction: column; gap: 0.5rem; width: 100%; }
        .settings-item { display: flex; align-items: center; padding: 1rem; text-decoration: none; color: #333; background: #fff; border-radius: 8px; transition: background 0.2s; }
        .settings-item:hover { background: #f5f5f5; }
        .settings-item i:first-child { font-size: 1.2rem; width: 2rem; color: #1976d2; }
        .settings-item span { flex: 1; margin-left: 0.5rem; font-size: 1rem; }
        .settings-item i:last-child { color: #999; font-size: 0.9rem; }
        /* Modal Styles */
        .modal-custom { display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);align-items:center;justify-content:center; }
        .modal-content-custom { background:#fff;padding:2rem 2.5rem;border-radius:18px;max-width:400px;width:90vw;box-shadow:0 8px 32px rgba(25,118,210,0.13);position:relative; }
        .modal-content-custom button.close-modal { position:absolute;top:12px;right:18px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer; }
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
                        <div><?= getInitials($name) ?></div>
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
        <div class="dashboard-section">
            <!-- Profile Card -->
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
                    <div style="font-size:1.01rem;color:#c7e0ff;letter-spacing:0.5px;"> <?= htmlspecialchars($account_number) ?> </div>
                </div>
            </div>
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;height:100%;">
                <div style="font-size:1.3rem;font-weight:800;letter-spacing:0.5px;margin-bottom:1.2rem;color:#1976d2;">Pengaturan</div>
                <div class="settings-menu">
                    <a href="#" class="settings-item" id="btn-ubah-password">
                        <i class="fas fa-key"></i>
                        <span>Ubah Password</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#" class="settings-item" id="btn-ubah-email-hp">
                        <i class="fas fa-envelope"></i>
                        <span>Ubah Email / No. HP</span>
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
<!-- Modal Ubah Password -->
<div id="modal-ubah-password" class="modal-custom">
  <div class="modal-content-custom">
    <button class="close-modal" id="close-modal-ubah-password">&times;</button>
    <h3 style="margin-bottom:1.5rem;color:#1976d2;font-weight:700;">Ubah Password</h3>
    <form id="form-ubah-password">
      <div style="margin-bottom:1.2rem;">
        <label for="password-lama" style="font-weight:600;display:block;margin-bottom:0.4rem;">Password Lama</label>
        <input type="password" id="password-lama" name="password_lama" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.2rem;">
        <label for="password-baru" style="font-weight:600;display:block;margin-bottom:0.4rem;">Password Baru</label>
        <input type="password" id="password-baru" name="password_baru" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.5rem;">
        <label for="konfirmasi-password" style="font-weight:600;display:block;margin-bottom:0.4rem;">Konfirmasi Password Baru</label>
        <input type="password" id="konfirmasi-password" name="konfirmasi_password" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
      <div id="password-error" style="color:#d32f2f;font-size:0.9rem;margin-bottom:1rem;display:none;"></div>
      <button type="submit" style="width:100%;background:#1976d2;color:#fff;border:none;padding:0.8rem;border-radius:8px;font-weight:700;font-size:1.08rem;cursor:pointer;">Ubah Password</button>
    </form>
  </div>
</div>
<!-- Modal Ubah Email / No. HP -->
<div id="modal-ubah-email-hp" class="modal-custom">
  <div class="modal-content-custom">
    <button class="close-modal" id="close-modal-ubah-email-hp">&times;</button>
    <h3 style="margin-bottom:1.5rem;color:#1976d2;font-weight:700;">Ubah Email / No. HP</h3>
    <form id="form-ubah-email-hp">
      <div style="margin-bottom:1.2rem;">
        <label for="email-baru" style="font-weight:600;display:block;margin-bottom:0.4rem;">Email Baru</label>
        <input type="email" id="email-baru" name="email_baru" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.2rem;">
        <label for="no-hp-baru" style="font-weight:600;display:block;margin-bottom:0.4rem;">No. HP Baru</label>
        <input type="tel" id="no-hp-baru" name="no_hp_baru" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
      <div style="margin-bottom:1.5rem;">
        <label for="password-konfirmasi" style="font-weight:600;display:block;margin-bottom:0.4rem;">Password untuk Konfirmasi</label>
        <input type="password" id="password-konfirmasi" name="password_konfirmasi" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
      <div id="email-hp-error" style="color:#d32f2f;font-size:0.9rem;margin-bottom:1rem;display:none;"></div>
      <button type="submit" style="width:100%;background:#1976d2;color:#fff;border:none;padding:0.8rem;border-radius:8px;font-weight:700;font-size:1.08rem;cursor:pointer;">Simpan Perubahan</button>
    </form>
  </div>
</div>
<script>
// Modal open/close logic
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Ubah Password
    const btnUbahPassword = document.getElementById('btn-ubah-password');
    if (btnUbahPassword) {
        btnUbahPassword.addEventListener('click', function(e) {
            e.preventDefault();
            openModal('modal-ubah-password');
            const form = document.getElementById('form-ubah-password');
            if (form) form.reset();
            const errorDiv = document.getElementById('password-error');
            if (errorDiv) errorDiv.style.display = 'none';
        });
    }
    const closeBtnPassword = document.getElementById('close-modal-ubah-password');
    if (closeBtnPassword) {
        closeBtnPassword.onclick = function() {
            closeModal('modal-ubah-password');
        };
    }
    // Ubah Email/HP
    const btnUbahEmailHp = document.getElementById('btn-ubah-email-hp');
    if (btnUbahEmailHp) {
        btnUbahEmailHp.addEventListener('click', function(e) {
            e.preventDefault();
            openModal('modal-ubah-email-hp');
            const form = document.getElementById('form-ubah-email-hp');
            if (form) form.reset();
            const errorDiv = document.getElementById('email-hp-error');
            if (errorDiv) errorDiv.style.display = 'none';
        });
    }
    const closeBtnEmailHp = document.getElementById('close-modal-ubah-email-hp');
    if (closeBtnEmailHp) {
        closeBtnEmailHp.onclick = function() {
            closeModal('modal-ubah-email-hp');
        };
    }
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html> 