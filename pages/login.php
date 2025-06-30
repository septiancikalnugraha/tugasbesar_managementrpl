<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
require_once '../includes/auth.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    $login = $auth->login($_POST['email'], $_POST['password']);
    if ($login['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $message = $login['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank FTI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f4f6f8; }
        .simple-login-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .simple-login-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 2rem 2.5rem 1.5rem 2.5rem;
            width: 100%;
            max-width: 340px;
        }
        .simple-login-box h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: #1976d2;
        }
        .simple-login-box .input-group {
            margin-bottom: 1.1rem;
        }
        .simple-login-box .input-group i {
            margin-right: 8px;
            color: #1976d2;
        }
        .simple-login-box input[type="email"],
        .simple-login-box input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border-radius: 6px;
            border: 1.2px solid #e3e7ed;
            font-size: 1rem;
            margin-top: 0.2rem;
        }
        .simple-login-box button {
            width: 100%;
            background: #1976d2;
            color: #fff;
            border: none;
            padding: 0.8rem;
            border-radius: 6px;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background 0.2s;
        }
        .simple-login-box button:hover {
            background: #1565c0;
        }
        .simple-login-box .register-link {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.97rem;
        }
        .simple-login-box .register-link a {
            color: #1976d2;
            text-decoration: none;
        }
        .simple-login-box .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="register-bg">
  <div class="register-card-wrapper">
    <div class="auth-card">
      <form class="register-form" method="POST" action="">
<?php if (!empty($message)): ?>
    <div class="alert alert-error" style="margin-bottom:1rem;"> <?= htmlspecialchars($message) ?> </div>
<?php endif; ?>
          <div class="auth-logo">
              <img src="../image/logo.jpeg" alt="Logo" style="width:100px;height:100px;object-fit:contain;border-radius:18px;box-shadow:0 2px 8px rgba(0,0,0,0.10);margin-bottom:10px;" />
          </div>
          <h2>Login FTI M-Banking</h2>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-envelope"></i>
                  <input class="form-control" type="text" name="email" placeholder="Email atau Nomor Rekening" required>
              </div>
          </div>
          <div class="form-group">
              <div class="input-icon">
                  <i class="fa fa-lock"></i>
                  <input class="form-control" type="password" name="password" placeholder="Password" required>
              </div>
          </div>
          <button class="btn btn-primary" type="submit">Login</button>
          <p class="auth-link">Belum punya akun? <a href="register.php">Daftar</a></p>
      </form>
    </div>
  </div>
</div>
</body>
</html>
