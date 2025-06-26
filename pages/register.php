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
    
    // Validation
    if (strlen($password) < 6) {
        $message = 'Password minimal 6 karakter';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Konfirmasi password tidak sesuai';
        $message_type = 'error';
    } else {
        $auth = new Auth();
        $result = $auth->register($full_name, $email, $phone, $password);
        
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
</head>
<body>
<div class="auth-container">
    <?php if (!empty($message)): ?>
        <div class="alert <?= $message_type === 'success' ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <form class="auth-card" method="POST" action="">
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
</script>

</body>
</html>