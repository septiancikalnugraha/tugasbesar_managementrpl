<?php
session_start();
$base_url = '../';
$page_title = 'Registrasi';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../includes/auth.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $provinsi = $_POST['provinsi'] ?? '';
    $kota = $_POST['kota'] ?? '';
    
    // Validation
    if (strlen($password) < 6) {
        $message = 'Password minimal 6 karakter';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Konfirmasi password tidak sesuai';
        $message_type = 'error';
    } elseif (empty($gender)) {
        $message = 'Pilih jenis kelamin';
        $message_type = 'error';
    } elseif (empty($birth_date)) {
        $message = 'Lengkapi tanggal lahir';
        $message_type = 'error';
    } elseif (empty($provinsi) || empty($kota)) {
        $message = 'Lengkapi alamat (provinsi dan kota)';
        $message_type = 'error';
    } else {
        $auth = new Auth();
        $result = $auth->register($full_name, $email, $phone, $password, $gender, $birth_date, $provinsi, $kota);
        
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'error';
        
        if ($result['success']) {
            $account_number = $result['account_number'];
        }
    }
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
</head>
<body>
<div class="register-bg">
  <div class="register-card-wrapper">
    <div class="auth-card">
      <?php if (!empty($message)): ?>
          <div class="alert <?= $message_type === 'success' ? 'alert-success' : 'alert-error' ?>">
              <?= htmlspecialchars($message) ?>
              <?php if ($message_type === 'success' && isset($account_number)): ?>
                  <div class="account-number-display">
                      <strong>Nomor Rekening Anda:</strong><br>
                      <span class="account-number"><?= htmlspecialchars($account_number) ?></span>
                      <p class="account-note">Simpan nomor rekening ini dengan aman!</p>
                  </div>
              <?php endif; ?>
          </div>
      <?php endif; ?>
      <form class="register-form" method="POST" action="">
          <div class="auth-logo">
              <img src="../image/logo.jpeg" alt="Logo" style="width:100px;height:100px;object-fit:contain;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.10);margin-bottom:10px;" />
          </div>
          <h2>Registrasi FTI M-Banking</h2>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-user"></i>
                  <input class="form-control" type="text" name="name" id="name" placeholder="Nama Lengkap" required>
              </div>
          </div>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-envelope"></i>
                  <input class="form-control" type="email" name="email" id="email" placeholder="Email" required>
              </div>
          </div>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-phone"></i>
                  <input class="form-control" type="text" name="phone" id="phone" placeholder="No. HP" required>
              </div>
          </div>
          <div class="form-group">
              <label class="form-label">Jenis Kelamin</label>
              <div class="radio-group">
                  <label class="radio-label">
                      <input type="radio" name="gender" value="Laki-laki" required>
                      <span class="radio-text">Laki-laki</span>
                  </label>
                  <label class="radio-label">
                      <input type="radio" name="gender" value="Perempuan" required>
                      <span class="radio-text">Perempuan</span>
                  </label>
              </div>
          </div>
          <div class="form-group">
              <label class="form-label">Tanggal Lahir</label>
              <div class="input-icon">
                  <i class="fa fa-calendar"></i>
                  <input class="form-control" type="date" name="birth_date" id="birth_date" placeholder="Tanggal Lahir" required max="<?= date('Y-m-d', strtotime('-17 years')) ?>">
              </div>
          </div>
          <div class="form-group">
              <label class="form-label">Alamat</label>
              <div class="input-icon">
                  <i class="fa fa-map-marker-alt"></i>
                  <select class="form-control" name="provinsi" id="provinsi" required>
                      <option value="">Pilih Provinsi</option>
                      <option value="DKI Jakarta">DKI Jakarta</option>
                      <option value="Jawa Barat">Jawa Barat</option>
                  </select>
              </div>
              <div style="height: 1rem;"></div>
              <div class="input-icon">
                  <i class="fa fa-city"></i>
                  <select class="form-control" name="kota" id="kota" required>
                      <option value="">Pilih Kota/Kabupaten</option>
                      <optgroup label="Jawa Barat">
                          <option value="Bandung">Bandung</option>
                          <option value="Bekasi">Bekasi</option>
                          <option value="Bogor">Bogor</option>
                          <option value="Cimahi">Cimahi</option>
                          <option value="Cirebon">Cirebon</option>
                          <option value="Depok">Depok</option>
                          <option value="Sukabumi">Sukabumi</option>
                          <option value="Tasikmalaya">Tasikmalaya</option>
                          <!-- dst, tambahkan semua kota/kabupaten di Jawa Barat -->
                      </optgroup>
                      <optgroup label="DKI Jakarta">
                          <option value="Jakarta Pusat">Jakarta Pusat</option>
                          <option value="Jakarta Barat">Jakarta Barat</option>
                          <option value="Jakarta Selatan">Jakarta Selatan</option>
                          <option value="Jakarta Timur">Jakarta Timur</option>
                          <option value="Jakarta Utara">Jakarta Utara</option>
                      </optgroup>
                      <!-- dst, tambahkan semua kota/kabupaten untuk setiap provinsi -->
                  </select>
              </div>
          </div>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-lock"></i>
                  <input class="form-control" type="password" name="password" id="password" placeholder="Password" required>
              </div>
          </div>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-lock"></i>
                  <input class="form-control" type="password" name="confirm_password" id="confirm_password" placeholder="Konfirmasi Password" required>
              </div>
          </div>
          <button class="btn btn-primary" type="submit">Daftar</button>
          <p class="auth-link">Sudah punya akun? <a href="login.php">Login</a></p>
      </form>
    </div>
  </div>
</div>

</body>
</html>