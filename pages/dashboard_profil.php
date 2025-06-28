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
        .dashboard-layout {
            min-height: unset;
            display: block;
        }
        .main-content {
            flex: unset;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
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
    </style>
</head>
<body>
<nav class="main-navbar">
    <div class="navbar-content">
        <div class="navbar-logo">Bank FTI</div>
    </div>
</nav>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar" style="width:110px;height:110px;font-size:2.5rem;box-shadow:0 4px 18px rgba(25,118,210,0.10);background:#f4f6f8;">
                <?php if (!empty($user_data['profile_photo'])): ?>
                    <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                <?php else: ?>
                    <?= getInitials($name) ?>
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
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="dashboard_history.php"><i class="fa fa-history"></i> Riwayat</a></li>
            <li><a href="#" onclick="showComingSoon()"><i class="fa fa-cog"></i> Pengaturan</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="dashboard-section" style="width:100%;max-width:900px;margin:0 0 2.5rem 0;padding:0;box-shadow:0 4px 24px rgba(25,118,210,0.08);background:#fff;border-radius:18px;display:flex;align-items:center;justify-content:center;gap:0.7rem;padding:2.5rem 2.5rem 2.5rem 0;">
            <div style="flex-shrink:0;margin-left:40px;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;min-width:140px;">
                <div class="sidebar-avatar profile-avatar-large" id="profile-avatar-preview" style="width:130px;height:130px;font-size:3rem;box-shadow:0 4px 18px rgba(25,118,210,0.13);background:#f4f6f8;border:4px solid #1976d2;">
                    <?php if (!empty($user_data['profile_photo'])): ?>
                        <img src="../<?= htmlspecialchars($user_data['profile_photo']) ?>?t=<?= time() ?>" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php else: ?>
                        <?= getInitials($name) ?>
                    <?php endif; ?>
                </div>
                <form id="upload-photo-form" style="margin-top:0.7rem;text-align:center;">
                    <input type="file" id="profile-photo-input" accept="image/*" style="display:none;">
                    <button type="button" class="btn btn-lanjut" style="padding:0.7rem 2.2rem;font-size:1.1rem;min-width:unset;background:linear-gradient(90deg,#1976d2,#42a5f5);color:#fff;font-weight:700;border:none;border-radius:8px;box-shadow:0 2px 8px rgba(25,118,210,0.10);margin-bottom:0.7rem;" onclick="document.getElementById('profile-photo-input').click()">
                        <?php if (!empty($user_data['profile_photo'])): ?>
                            Ubah Foto
                        <?php else: ?>
                            Tambah Foto
                        <?php endif; ?>
                    </button>
                    <div id="upload-feedback" style="margin-top:0.5rem;font-size:0.98rem;color:#1976d2;"></div>
                </form>
            </div>
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center;height:100%;">
                <div style="font-size:1.6rem;font-weight:800;letter-spacing:0.5px;margin-bottom:1.2rem; text-align:center;">Profil Saya</div>
                <div style="display:grid;grid-template-columns:180px 1fr;gap:0.7rem 1.2rem;align-items:center;justify-content:center;max-width:420px;margin:0 auto;">
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Nama Lengkap</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($name) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Email</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($email) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">No. Handphone</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($phone) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Jenis Kelamin</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($gender) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Tanggal Lahir</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($formatted_birth_date) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Provinsi</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($provinsi) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Kota/Kabupaten</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($kota) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Nomor Rekening</div><div style="font-size:1.08rem;font-weight:700;color:#222;display:flex;align-items:center;gap:8px;"><span id="profil-account-number" style="user-select:all;"><?= htmlspecialchars($account_number) ?></span><button id="profil-copy-btn" title="Salin Nomor Rekening" style="background:none;border:none;cursor:pointer;outline:none;padding:0;"><i class="fa fa-copy" id="profil-copy-icon" style="color:#555;font-size:1.1em;"></i></button><span id="profil-copy-toast" style="display:none;margin-left:8px;color:#ff9800;font-size:0.98em;font-weight:600;vertical-align:middle;">Nomor rekening disalin!</span></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Role</div><div style="font-size:1.08rem;font-weight:700;color:#222;"><?= htmlspecialchars($role) ?></div>
                    <div style="color:#1976d2;font-size:1rem;font-weight:600;">Saldo</div><div style="font-size:1.08rem;font-weight:700;color:#222;">Rp <?= $formatted_balance ?></div>
                </div>
            </div>
        </div>
    </main>
</div>
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