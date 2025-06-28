<?php
session_start();
$base_url = '../';
$page_title = 'Login';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'teller') {
        header('Location: dashboard_petugas.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

require_once '../includes/auth.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emailOrAccount = trim($_POST['email']);
    $password = $_POST['password'];
    
    $auth = new Auth();
    $result = $auth->login($emailOrAccount, $password);
    
    if ($result['success']) {
        if ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'teller') {
            header('Location: dashboard_petugas.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="register-bg">
  <div class="register-card-wrapper">
    <div class="auth-card">
      <form class="register-form" method="POST" action="">
        <?php if (!empty($message)): ?>
            <div class="alert <?= $message_type === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <div class="auth-logo">
            <img src="../assets/images/logo.png" alt="Logo" />
        </div>
        <h2>Login Mobile Banking</h2>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input class="form-control" type="text" name="email" id="email" placeholder="Email atau Nomor Rekening" required>
            </div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-lock"></i>
                <input class="form-control" type="password" name="password" id="password" placeholder="Password" required>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Masuk</button>
        <p class="auth-link">Belum punya akun? <a href="register.php">Daftar</a></p>
      </form>
    </div>
  </div>
</div>
</body>
</html>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
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

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<span class="loading"></span> Memproses...';
    btn.disabled = true;
});
</script>
