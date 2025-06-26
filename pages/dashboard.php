<?php
session_start();
$base_url = '../';
$page_title = 'Dashboard';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/auth.php';

// Get updated user data
$auth = new Auth();
$user_data = $auth->getUserData($_SESSION['user_id']);

if ($user_data) {
    $_SESSION['balance'] = $user_data['balance'];
}

$name = $_SESSION['user_name'];
$role = 'Nasabah';

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="main-navbar">
    <div class="navbar-content">
        <div class="navbar-logo">
            <img src="../assets/images/logo.png" alt="Logo">
            Bank FTI
        </div>
    </div>
</nav>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="sidebar-avatar"><?= strtoupper(substr($name,0,1)) ?></div>
            <div>
                <div class="sidebar-name"><?= htmlspecialchars($name) ?></div>
                <div class="sidebar-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <ul>
            <li><a href="#" class="active"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fa fa-user"></i> Profil</a></li>
            <li class="sidebar-logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <h2>Selamat Datang, <?= htmlspecialchars($name) ?></h2>
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Profil</h3>
                <p>Lihat dan edit profil Anda.</p>
            </div>
            <div class="dashboard-card">
                <h3>Data</h3>
                <p>Kelola data Anda di sini.</p>
            </div>
        </div>
    </main>
</div>

<script>
function showComingSoon() {
    alert('Fitur ini akan segera hadir! 🚀\n\nTerima kasih atas kesabaran Anda.');
}

// Auto refresh balance every 30 seconds
setInterval(function() {
    // In a real application, you would make an AJAX call to refresh the balance
    console.log('Balance refresh - In development');
}, 30000);

// Welcome animation
window.addEventListener('load', function() {
    const cards = document.querySelectorAll('.dashboard-card, .account-info, .action-btn');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

</body>
</html>