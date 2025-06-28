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
              <img src="../assets/images/logo.png" alt="Logo" />
          </div>
          <h2>Registrasi Mobile Banking</h2>
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

<script>
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak sesuai!');
        return;
    }
    
    const btn = document.getElementById('registerBtn');
    btn.innerHTML = '<span class="loading"></span> Memproses...';
    btn.disabled = true;
});

// Real-time password validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

// Data provinsi dan kota (ringkas, bisa diperluas)
const dataWilayah = {
  'DKI Jakarta': ['Jakarta Pusat', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Utara'],
  'Jawa Barat': ['Bandung', 'Bekasi', 'Bogor', 'Depok', 'Cimahi', 'Sukabumi', 'Cirebon'],
  'Jawa Tengah': ['Semarang', 'Surakarta', 'Magelang', 'Salatiga', 'Pekalongan', 'Tegal'],
  'DI Yogyakarta': ['Yogyakarta', 'Sleman', 'Bantul', 'Gunungkidul', 'Kulon Progo'],
  'Jawa Timur': ['Surabaya', 'Malang', 'Kediri', 'Blitar', 'Madiun', 'Mojokerto', 'Pasuruan', 'Probolinggo'],
  'Banten': ['Serang', 'Cilegon', 'Tangerang', 'Tangerang Selatan'],
  'Bali': ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Bangli', 'Buleleng', 'Jembrana', 'Karangasem', 'Klungkung'],
  'Sumatera Utara': ['Medan', 'Binjai', 'Tebing Tinggi', 'Pematangsiantar', 'Sibolga', 'Tanjungbalai', 'Padangsidimpuan'],
  'Sumatera Barat': ['Padang', 'Bukittinggi', 'Padangpanjang', 'Pariaman', 'Payakumbuh', 'Sawahlunto', 'Solok'],
  'Riau': ['Pekanbaru', 'Dumai'],
  'Kepulauan Riau': ['Batam', 'Tanjungpinang'],
  'Sumatera Selatan': ['Palembang', 'Lubuklinggau', 'Pagar Alam', 'Prabumulih'],
  'Lampung': ['Bandar Lampung', 'Metro'],
  'Kalimantan Barat': ['Pontianak', 'Singkawang'],
  'Kalimantan Timur': ['Balikpapan', 'Bontang', 'Samarinda'],
  'Kalimantan Selatan': ['Banjarbaru', 'Banjarmasin'],
  'Kalimantan Tengah': ['Palangka Raya'],
  'Kalimantan Utara': ['Tarakan'],
  'Sulawesi Selatan': ['Makassar', 'Palopo', 'Parepare'],
  'Sulawesi Utara': ['Manado', 'Bitung', 'Tomohon', 'Kotamobagu'],
  'Sulawesi Tengah': ['Palu'],
  'Sulawesi Tenggara': ['Kendari', 'Baubau'],
  'Gorontalo': ['Gorontalo'],
  'Maluku': ['Ambon', 'Tual'],
  'Maluku Utara': ['Ternate', 'Tidore Kepulauan'],
  'Papua': ['Jayapura'],
  'Papua Barat': ['Manokwari', 'Sorong']
};

const provinsiSelect = document.getElementById('provinsi');
const kotaSelect = document.getElementById('kota');

// Populate provinsi
for (const prov in dataWilayah) {
  const opt = document.createElement('option');
  opt.value = prov;
  opt.textContent = prov;
  provinsiSelect.appendChild(opt);
}

provinsiSelect.addEventListener('change', function() {
  const selectedProv = this.value;
  kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
  if (selectedProv && dataWilayah[selectedProv]) {
    dataWilayah[selectedProv].forEach(function(kota) {
      const opt = document.createElement('option');
      opt.value = kota;
      opt.textContent = kota;
      kotaSelect.appendChild(opt);
    });
  }
});

// After populating provinsi and kota, set selected values if available
const selectedProv = "<?= isset($_POST['provinsi']) ? addslashes($_POST['provinsi']) : '' ?>";
const selectedKota = "<?= isset($_POST['kota']) ? addslashes($_POST['kota']) : '' ?>";
if (selectedProv) {
  provinsiSelect.value = selectedProv;
  provinsiSelect.dispatchEvent(new Event('change'));
  setTimeout(function() {
    if (selectedKota) kotaSelect.value = selectedKota;
  }, 100);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daerah-indonesia@1.0.1/daerah.min.js"></script>
<script>
const provinsiSelect = document.getElementById('provinsi');
const kotaSelect = document.getElementById('kota');
const provinsiChoices = new Choices(provinsiSelect, { searchEnabled: true, itemSelectText: '', shouldSort: false });
const kotaChoices = new Choices(kotaSelect, { searchEnabled: true, itemSelectText: '', shouldSort: false });

// Populate provinsi
provinsiChoices.clearChoices();
provinsiChoices.setChoices([{ value: '', label: 'Pilih Provinsi', selected: true, disabled: true }], 'value', 'label', false);
window.daerahIndonesia.provinsi.forEach(function(prov) {
  provinsiChoices.setChoices([{ value: prov, label: prov }], 'value', 'label', false);
});

provinsiSelect.addEventListener('change', function() {
  const selectedProv = this.value;
  kotaChoices.clearChoices();
  kotaChoices.setChoices([{ value: '', label: 'Pilih Kota/Kabupaten', selected: true, disabled: true }], 'value', 'label', false);
  if (selectedProv && window.daerahIndonesia.kabupaten[selectedProv]) {
    kotaChoices.setChoices(
      window.daerahIndonesia.kabupaten[selectedProv].map(kota => ({ value: kota, label: kota })),
      'value', 'label', false
    );
  }
  if (typeof selectedKota !== 'undefined' && selectedKota) {
    kotaChoices.setChoiceByValue(selectedKota);
  }
});

document.addEventListener('DOMContentLoaded', function() {
  if (typeof selectedProv !== 'undefined' && selectedProv) {
    provinsiChoices.setChoiceByValue(selectedProv);
    provinsiSelect.dispatchEvent(new Event('change'));
    setTimeout(function() {
      if (typeof selectedKota !== 'undefined' && selectedKota) kotaChoices.setChoiceByValue(selectedKota);
    }, 100);
  }
});
</script>

</body>
</html>