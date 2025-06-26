<?php
session_start();
$base_url = '';
$page_title = 'Beranda';

// Redirect to dashboard if logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'teller') {
        header('Location: pages/dashboard_petugas.php');
    } else {
        header('Location: pages/dashboard.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank FTI - Solusi Perbankan Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .navbar {
            width: 100%;
            background: #fff;
            box-shadow: 0 2px 12px rgba(25, 118, 210, 0.07);
            padding: 0.7rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-content {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1976d2;
            letter-spacing: 1px;
        }
        .navbar-logo img {
            width: 38px;
            height: 38px;
            margin-right: 10px;
            border-radius: 10px;
        }
        .navbar-links {
            display: flex;
            gap: 1.5rem;
        }
        .navbar-links a {
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .navbar-links a:hover {
            color: #0d47a1;
        }
        @media (max-width: 700px) {
            .navbar-content {
                flex-direction: column;
                gap: 0.7rem;
                padding: 0 1rem;
            }
            .navbar-links {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-content">
        <div class="navbar-logo">
            <img src="assets/images/logo.png" alt="Logo">
            Bank FTI
        </div>
        <div class="navbar-links">
            <a href="pages/login.php" class="btn btn-primary">Login</a>
            <a href="pages/register.php" class="btn">Daftar</a>
        </div>
    </div>
</nav>
<div class="hero">
    <div class="hero-illustration">
        <img src="assets/images/mbanking_hero.png" alt="Ilustrasi Mobile Banking">
    </div>
    <div class="hero-title">Bank FTI</div>
    <div class="hero-tagline">Solusi Perbankan Digital Modern & Aman</div>
    <div class="features" id="fitur">
        <div class="feature-card">
            <i class="fa fa-mobile-alt"></i>
            <h4>Mobile Banking</h4>
            <p>Akses rekening & transaksi kapan saja, di mana saja.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-shield-alt"></i>
            <h4>Keamanan Terjamin</h4>
            <p>Data & transaksi Anda dilindungi enkripsi tingkat tinggi.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-clock"></i>
            <h4>Layanan 24/7</h4>
            <p>Dukungan dan layanan bank siap setiap saat.</p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>