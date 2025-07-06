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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Pengaturan - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
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
</script>
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
                    <a href="#" class="settings-item">
                        <i class="fas fa-language"></i>
                        <span>Bahasa & Tema</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <div id="favorit-card" class="settings-item" tabindex="0" style="cursor:pointer;">
                        <i class="fas fa-star"></i>
                        <span>Rekening Favorit</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <a href="chat_bankfti.php" class="settings-item">
                        <i class="fas fa-comments"></i>
                        <span>Chat dengan Bank FTI</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <div class="settings-item" style="cursor:default;">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Device yang Digunakan:<br><span style='font-size:0.93em;color:#1976d2;font-weight:500;'><?= htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? '-') ?></span></span>
                    </div>
                    <a href="#" class="settings-item">
                        <i class="fas fa-trash-alt"></i>
                        <span>Hapus Akun</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- Rekening Favorit -->
        <div id="favorit-modal" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);align-items:center;justify-content:center;">
          <div style="background:#fff;padding:2rem 2.5rem;border-radius:18px;max-width:420px;width:90vw;box-shadow:0 8px 32px rgba(25,118,210,0.13);position:relative;">
            <button id="close-favorit-modal" style="position:absolute;top:12px;right:18px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;">&times;</button>
            <h3 style="margin-bottom:1.2rem;color:#1976d2;font-weight:700;">Rekening Favorit</h3>
            <div id="favorit-list-modal"></div>
          </div>
        </div>
        <!-- END: Rekening Favorit -->
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

<!-- Modal Ubah Password -->
<div id="modal-ubah-password" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);align-items:center;justify-content:center;">
  <div style="background:#fff;padding:2rem 2.5rem;border-radius:18px;max-width:400px;width:90vw;box-shadow:0 8px 32px rgba(25,118,210,0.13);position:relative;">
    <button id="close-modal-ubah-password" style="position:absolute;top:12px;right:18px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;">&times;</button>
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
<div id="modal-ubah-email-hp" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);align-items:center;justify-content:center;">
  <div style="background:#fff;padding:2rem 2.5rem;border-radius:18px;max-width:400px;width:90vw;box-shadow:0 8px 32px rgba(25,118,210,0.13);position:relative;">
    <button id="close-modal-ubah-email-hp" style="position:absolute;top:12px;right:18px;background:none;border:none;font-size:1.3rem;color:#1976d2;cursor:pointer;">&times;</button>
    <h3 style="margin-bottom:1.5rem;color:#1976d2;font-weight:700;">Ubah Email / No. HP</h3>
    <form id="form-ubah-email-hp">
      <div style="margin-bottom:1.2rem;">
        <label for="nama-baru" style="font-weight:600;display:block;margin-bottom:0.4rem;">Nama Baru</label>
        <input type="text" id="nama-baru" name="nama_baru" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
      </div>
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
// Fungsi untuk membuka modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    } else {
        console.error('Modal not found:', modalId);
    }
}

// Fungsi untuk menutup modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners...');
    
    // Tampilkan modal saat klik menu Bahasa & Tema
    document.querySelectorAll('.settings-item').forEach(function(item) {
        if (item.textContent.includes('Bahasa') || item.textContent.includes('Language')) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                openModal('modal-bahasa-tema');
            });
        }
    });
    
    // Tampilkan modal saat klik menu Ubah Password
    const btnUbahPassword = document.getElementById('btn-ubah-password');
    if (btnUbahPassword) {
        btnUbahPassword.addEventListener('click', function(e) {
            e.preventDefault();
            openModal('modal-ubah-password');
            // Reset form
            const form = document.getElementById('form-ubah-password');
            if (form) form.reset();
            const errorDiv = document.getElementById('password-error');
            if (errorDiv) errorDiv.style.display = 'none';
        });
    } else {
        console.error('Button Ubah Password not found');
    }
    
    // Tampilkan modal saat klik menu Ubah Email / No. HP
    const btnUbahEmailHp = document.getElementById('btn-ubah-email-hp');
    if (btnUbahEmailHp) {
        btnUbahEmailHp.addEventListener('click', function(e) {
            e.preventDefault();
            openModal('modal-ubah-email-hp');
            // Reset form
            const form = document.getElementById('form-ubah-email-hp');
            if (form) form.reset();
            const errorDiv = document.getElementById('email-hp-error');
            if (errorDiv) errorDiv.style.display = 'none';
        });
    } else {
        console.error('Button Ubah Email/HP not found');
    }
  
  // Close modal Bahasa & Tema
  const closeBtnBahasa = document.getElementById('close-modal-bahasa-tema');
  if (closeBtnBahasa) {
    closeBtnBahasa.onclick = function() {
      closeModal('modal-bahasa-tema');
    };
  }
  
  // Close modal Ubah Password
  const closeBtnPassword = document.getElementById('close-modal-ubah-password');
  if (closeBtnPassword) {
    closeBtnPassword.onclick = function() {
      closeModal('modal-ubah-password');
    };
  }
  
  // Close modal Ubah Email / No. HP
  const closeBtnEmailHp = document.getElementById('close-modal-ubah-email-hp');
  if (closeBtnEmailHp) {
    closeBtnEmailHp.onclick = function() {
      closeModal('modal-ubah-email-hp');
    };
  }
  
  // Close modals when clicking outside
  window.onclick = function(event) {
    const modals = ['modal-bahasa-tema', 'modal-ubah-password', 'modal-ubah-email-hp'];
    modals.forEach(function(modalId) {
      const modal = document.getElementById(modalId);
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });
  };
  
  // Handle form submission for Ubah Password
  document.getElementById('form-ubah-password').addEventListener('submit', function(e) {
    e.preventDefault();
    const passwordLama = document.getElementById('password-lama').value;
    const passwordBaru = document.getElementById('password-baru').value;
    const konfirmasiPassword = document.getElementById('konfirmasi-password').value;
    const errorDiv = document.getElementById('password-error');
    
    // Reset error
    errorDiv.style.display = 'none';
    
    // Validation
    if (passwordBaru !== konfirmasiPassword) {
      errorDiv.textContent = 'Konfirmasi password tidak cocok!';
      errorDiv.style.display = 'block';
      return;
    }
    
    if (passwordBaru.length < 6) {
      errorDiv.textContent = 'Password baru minimal 6 karakter!';
      errorDiv.style.display = 'block';
      return;
    }
    
    // Kirim data ke server via AJAX
    const formData = new FormData();
    formData.append('password_lama', passwordLama);
    formData.append('password_baru', passwordBaru);
    formData.append('konfirmasi_password', konfirmasiPassword);
    
    fetch('../update_password.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Password berhasil diubah!');
        document.getElementById('modal-ubah-password').style.display = 'none';
        document.getElementById('form-ubah-password').reset();
      } else {
        errorDiv.textContent = data.message || 'Gagal update password!';
        errorDiv.style.display = 'block';
      }
    })
    .catch(() => {
      errorDiv.textContent = 'Terjadi kesalahan koneksi!';
      errorDiv.style.display = 'block';
    });
  });
  
  // Handle form submission for Ubah Email / No. HP
  document.getElementById('form-ubah-email-hp').addEventListener('submit', function(e) {
    e.preventDefault();
    const namaBaru = document.getElementById('nama-baru').value;
    const emailBaru = document.getElementById('email-baru').value;
    const noHpBaru = document.getElementById('no-hp-baru').value;
    const passwordKonfirmasi = document.getElementById('password-konfirmasi').value;
    const errorDiv = document.getElementById('email-hp-error');
    
    // Reset error
    errorDiv.style.display = 'none';
    
    // Validation
    if (!namaBaru || !emailBaru || !noHpBaru || !passwordKonfirmasi) {
      errorDiv.textContent = 'Semua field harus diisi!';
      errorDiv.style.display = 'block';
      return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailBaru)) {
      errorDiv.textContent = 'Format email tidak valid!';
      errorDiv.style.display = 'block';
      return;
    }
    // Phone validation
    const phoneRegex = /^[0-9]{10,13}$/;
    if (!phoneRegex.test(noHpBaru.replace(/\s/g, ''))) {
      errorDiv.textContent = 'Format nomor HP tidak valid!';
      errorDiv.style.display = 'block';
      return;
    }
    // Nama validation (opsional: min 2 karakter)
    if (namaBaru.length < 2) {
      errorDiv.textContent = 'Nama minimal 2 karakter!';
      errorDiv.style.display = 'block';
      return;
    }
    // Kirim data ke server via AJAX
    const formData = new FormData();
    formData.append('nama_baru', namaBaru);
    formData.append('email_baru', emailBaru);
    formData.append('no_hp_baru', noHpBaru);
    formData.append('password_konfirmasi', passwordKonfirmasi);
    fetch('../update_profile.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update tampilan nama, email & no HP di profil jika ada
        const nameField = document.querySelector('.sidebar-profile .sidebar-name');
        if (nameField) nameField.textContent = data.name;
        const emailField = document.querySelector('.sidebar-profile .sidebar-email');
        if (emailField) emailField.textContent = data.email;
        const phoneField = document.querySelector('.sidebar-profile .sidebar-phone');
        if (phoneField) phoneField.textContent = data.phone;
        alert('Profil berhasil diubah!');
        document.getElementById('modal-ubah-email-hp').style.display = 'none';
        document.getElementById('form-ubah-email-hp').reset();
        window.location.reload();
      } else {
        errorDiv.textContent = data.message || 'Gagal update profil!';
        errorDiv.style.display = 'block';
      }
    })
    .catch(() => {
      errorDiv.textContent = 'Terjadi kesalahan koneksi!';
      errorDiv.style.display = 'block';
    });
  });
  
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

  // Modal popup rekening favorit
  var favoritCard = document.getElementById('favorit-card');
  var favoritModal = document.getElementById('favorit-modal');
  var closeFavoritModalBtn = document.getElementById('close-favorit-modal');
  if (!favoritCard) console.error('Elemen #favorit-card tidak ditemukan di DOM!');
  if (!favoritModal) console.error('Elemen #favorit-modal tidak ditemukan di DOM!');
  if (!closeFavoritModalBtn) console.error('Elemen #close-favorit-modal tidak ditemukan di DOM!');
  if (favoritCard && favoritModal && closeFavoritModalBtn) {
    favoritCard.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('Favorit card diklik!');
      favoritModal.style.display = 'flex';
      renderFavoritListModal();
    });
    closeFavoritModalBtn.addEventListener('click', function() {
      favoritModal.style.display = 'none';
    });
    favoritModal.addEventListener('click', function(e) {
      if (e.target === favoritModal) favoritModal.style.display = 'none';
    });
  }
  // Fetch dan render rekening favorit ke dalam modal
  function renderFavoritListModal() {
    fetch('get_receivers.php')
        .then(res => res.json())
        .then(data => {
            console.log('Data rekening favorit:', data);
            const list = data.filter(r => r.is_favorite == 1);
            const container = document.getElementById('favorit-list-modal');
            if(list.length === 0) {
                container.innerHTML = '<div style="padding:1.2rem 1rem;color:#888;font-size:1.08rem;background:#fafbfc;border-radius:12px;">Belum ada rekening favorit.</div>';
                return;
            }
            container.innerHTML = list.map(r => `
                <div style="display:flex;align-items:center;gap:1.1rem;background:#fafbfc;border-radius:12px;padding:1.1rem 1.3rem;margin-bottom:1rem;">
                    <span class="favorite-icon" data-id="${r.id}" style="cursor:pointer;color:${r.is_favorite==1?'#FFD600':'#bbb'};font-size:1.3rem;margin-right:0.7rem;">
                        <i class="fa${r.is_favorite==1?'-solid':'-regular'} fa-star"></i>
                    </span>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:1.08rem;">${r.name}</div>
                        <div style="color:#555;font-size:0.98rem;">${r.account_number}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch((err) => {
            console.error('Gagal fetch rekening favorit:', err);
            document.getElementById('favorit-list-modal').innerHTML = '<div style="padding:1.2rem 1rem;color:#d32f2f;font-size:1.08rem;background:#fafbfc;border-radius:12px;">Gagal memuat data rekening favorit.</div>';
        });
  }

  // Tambahkan event listener setelah render
  document.querySelectorAll('.favorite-icon').forEach(function(star) {
    star.addEventListener('click', function(e) {
      e.stopPropagation();
      var id = this.getAttribute('data-id');
      fetch('toggle_favorite_receiver.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'receiver_id=' + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        // Optionally, refresh list or just toggle icon
        renderFavoritListModal();
      });
    });
  });
});
</script>
</body>
</html> 