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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }
        
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
            /* max-width: 1100px; */
            /* margin: 0 auto; */
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 1.5rem;
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1976d2;
            letter-spacing: 1px;
            margin-right: auto;
            padding-left: 0;
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
        
        .hero-section {
            width: 100vw !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            background: linear-gradient(135deg, #1976d2 0%, #0d47a1 50%, #1a237e 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
            z-index: 1;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 15%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 70px;
            height: 70px;
            top: 25%;
            right: 8%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 50px;
            height: 50px;
            bottom: 25%;
            left: 10%;
            animation-delay: 4s;
        }
        
        .shape:nth-child(4) {
            width: 35px;
            height: 35px;
            top: 55%;
            right: 15%;
            animation-delay: 1s;
        }
        
        .shape:nth-child(5) {
            width: 80px;
            height: 80px;
            bottom: 10%;
            right: 5%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.1; }
            50% { transform: translateY(-25px) rotate(180deg); opacity: 0.15; }
        }
        
        .hero-content {
            width: 100vw !important;
            max-width: 100vw !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 2rem !important;
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            justify-content: center;
        }
        
        .hero-left {
            color: white;
            animation: slideInLeft 1s ease-out;
        }
        
        .hero-badge {
            background: linear-gradient(135deg, #64b5f6, #1976d2);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            font-weight: 800;
            font-size: 1rem;
            display: inline-block;
            margin-bottom: 1.5rem;
            box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .hero-title {
            font-size: 2rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        .service-badges {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .service-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .service-badge:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        .ewallet-section {
            margin: 1.5rem 0;
        }
        
        .ewallet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }
        
        .ewallet-card {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .ewallet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .ewallet-card img {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            margin-bottom: 6px;
            object-fit: contain;
        }
        
        .ewallet-card span {
            font-weight: 700;
            color: #1976d2;
            font-size: 0.8rem;
            display: block;
        }
        
        .download-section {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .download-btn {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .download-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        
        .download-btn img {
            width: 25px;
            height: 25px;
        }
        
        .hero-right {
            display: flex;
            justify-content: center;
            align-items: center;
            animation: slideInRight 1s ease-out;
        }
        
        .phone-mockup {
            position: relative;
            width: 600px;
            height: 420px;
            background: linear-gradient(135deg, #424242, #212121);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }
        
        .phone-mockup::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background: #424242;
            border-radius: 3px;
            z-index: 3;
        }
        
        .phone-screen {
            width: 100%;
            height: 100%;
            background: #1976d2;
            border-radius: 25px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .phone-screen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 25px;
        }
        
        .info-section {
            margin-top: 1.5rem;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .info-section p {
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .info-section .highlight {
            color: #64b5f6;
            font-weight: 700;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }
            
            .phone-mockup {
                width: 450px;
                height: 315px;
            }
        }
        
        @media (max-width: 768px) {
            .hero-content {
                gap: 2rem;
                padding: 0 1.5rem;
            }
            
            .hero-title {
                font-size: 1.8rem;
            }
            
            .ewallet-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .phone-mockup {
                width: 350px;
                height: 245px;
            }
            
            .service-badges {
                justify-content: center;
            }
            
            .download-section {
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .hero-content {
                padding: 0 1rem;
            }
            
            .hero-badge {
                font-size: 0.9rem;
                padding: 0.6rem 1.5rem;
            }
            
            .hero-title {
                font-size: 1.6rem;
            }
            
            .phone-mockup {
                width: 250px;
                height: 175px;
            }
            
            .ewallet-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .download-section {
                flex-direction: column;
                align-items: center;
            }
            
            .download-btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }
        
        .slider-container {
            position: relative;
            width: 100vw;
            max-width: 100vw;
            overflow: hidden;
            min-height: 100vh;
            background: none;
        }
        .slider-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(.77,0,.18,1);
            will-change: transform;
            width: 200vw;
        }
        .slide {
            width: 100vw !important;
            min-width: 100vw !important;
            max-width: 100vw !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
            background: none;
            display: flex;
            align-items: stretch;
        }
        .slider-arrow {
            position: absolute;
            top: 50%;
            z-index: 10;
            background: rgba(25, 118, 210, 0.85);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            font-size: 1.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 12px rgba(25,118,210,0.13);
            transform: translateY(-50%);
            transition: background 0.2s;
        }
        .slider-arrow:active {
            background: #1565c0;
        }
        .slider-arrow.left { left: 18px; }
        .slider-arrow.right { right: 18px; }
        @media (max-width: 700px) {
            .slider-arrow { width: 36px; height: 36px; font-size: 1.2rem; }
        }
        
        .about-section {
            width: 100vw !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e3f2fd 0%, #90caf9 100%) !important;
        }
        .about-container {
            width: 100vw !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 2rem !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .about-container h2, .about-container h3 {
            color: #0d47a1 !important;
            font-weight: 800;
        }
        .values-row {
            display: flex;
            gap: 2.5rem;
            justify-content: center;
            flex-wrap: wrap;
            overflow-x: auto;
        }
        .value-card {
            flex: 0 0 260px;
            background: #fff;
            border-radius: 20px;
            padding: 2rem 1.2rem 1.5rem 1.2rem;
            box-shadow: 0 4px 24px rgba(25,118,210,0.10);
            margin-bottom: 1.2rem;
            border: 1.5px solid #e3f2fd;
            transition: box-shadow 0.2s;
        }
        .value-card:hover {
            box-shadow: 0 8px 32px rgba(25,118,210,0.18);
        }
        .value-card svg {
            display: block;
            margin: 0 auto 0.7rem auto;
        }
        .value-card div[style*='font-weight:700'] {
            color: #1976d2 !important;
        }
        
        /* Tambahan untuk slide layanan */
        .services-section {
            width: 100vw;
            min-height: 100vh;
            background: linear-gradient(135deg, #e3f2fd 0%, #90caf9 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 2rem;
        }
        .services-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0d47a1;
            margin-bottom: 1.2rem;
            letter-spacing: 1px;
            text-align: center;
        }
        .services-desc {
            font-size: 1.15rem;
            color: #222;
            max-width: 700px;
            margin: 0 auto 2.5rem auto;
            line-height: 1.6;
            text-align: center;
        }
        .services-row {
            display: flex;
            gap: 2.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .service-card {
            flex: 0 0 260px;
            background: #fff;
            border-radius: 20px;
            padding: 2rem 1.2rem 1.5rem 1.2rem;
            box-shadow: 0 4px 24px rgba(25,118,210,0.10);
            margin-bottom: 1.2rem;
            border: 1.5px solid #90caf9;
            transition: box-shadow 0.2s, border 0.2s;
            text-align: center;
        }
        .service-card:hover {
            box-shadow: 0 8px 32px rgba(25,118,210,0.18);
            border: 1.5px solid #1976d2;
        }
        .service-card svg {
            display: block;
            margin: 0 auto 0.7rem auto;
            background: #e3f2fd;
            border-radius: 50%;
            padding: 8px;
        }
        .service-card .service-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1976d2;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-logo">
                <img src="image/logo.jpeg" alt="Logo" style="width:60px;height:60px;object-fit:contain;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.10);margin-right:12px;background:#fff;" />
                FTI M-Banking
            </div>
            <div class="navbar-links">
                <a href="pages/login.php" class="btn btn-primary">Login</a>
                <a href="pages/register.php" class="btn">Daftar</a>
            </div>
        </div>
    </nav>

    <div class="slider-container">
        <button class="slider-arrow left" onclick="slideTo(currentSlide-1)" style="display:none;" id="arrowLeft">&#8592;</button>
        <button class="slider-arrow right" onclick="slideTo(currentSlide+1)" id="arrowRight">&#8594;</button>
        <div class="slider-track" id="sliderTrack" style="width:300vw;">
            <div class="slide">
                <section class="hero-section">
                    <div class="floating-shapes">
                        <div class="shape"></div>
                        <div class="shape"></div>
                        <div class="shape"></div>
                        <div class="shape"></div>
                        <div class="shape"></div>
                    </div>
                    
                    <div class="hero-content">
                        <div class="hero-left">
                            <div class="hero-badge">KEMUDAHAN BERTRANSAKSI</div>
                            
                            <h1 class="hero-title">Solusi Perbankan Digital Terdepan</h1>
                            
                            <div class="service-badges">
                                <span class="service-badge">Top Up</span>
                                <span class="service-badge">Pembayaran</span>
                            </div>
                            
                            <div class="ewallet-section">
                                <div class="ewallet-grid">
                                    <div class="ewallet-card">
                                        <img src="https://eswpcd25uod.exactdn.com/blog/wp-content/uploads/2023/08/DANA-Apa-pun-transaksinya-selalu-ada-DANA-1.png" alt="DANA" style="width:56px;height:56px;object-fit:contain;margin:10px auto 12px auto;display:block;border-radius:12px;" />
                                        <span>DANA</span>
                                    </div>
                                    <div class="ewallet-card">
                                        <img src="https://tokpee.co/blog/wp-content/uploads/2025/03/Begini-Cara-Membagikan-Kode-QR-ShopeePay-Biar-Uang-Langsung-Masuk.webp" alt="ShopeePay" style="width:56px;height:56px;object-fit:contain;margin:10px auto 12px auto;display:block;border-radius:12px;" />
                                        <span>ShopeePay</span>
                                    </div>
                                    <div class="ewallet-card">
                                        <img src="https://play-lh.googleusercontent.com/LHX1dwx4ZAZqY9Uhxt-uSJU7Em2aY-7YmY6-cJ_Zf39I9cCoORwgYHfhzmuRqWu14drt" alt="LinkAja" style="width:56px;height:56px;object-fit:contain;margin:10px auto 12px auto;display:block;border-radius:12px;" />
                                        <span>LinkAja</span>
                                    </div>
                                    <div class="ewallet-card">
                                        <img src="https://storage.googleapis.com/narasi-production.appspot.com/production/large/1683880606232/simak-cara-transfer-uang-sesama-pengguna-ovo-large.jpg" alt="OVO" style="width:56px;height:56px;object-fit:contain;margin:10px auto 12px auto;display:block;border-radius:12px;" />
                                        <span>OVO</span>
                                    </div>
                                    <div class="ewallet-card">
                                        <img src="https://img.jakpost.net/c/2019/01/02/2019_01_02_62094_1546439463._large.jpg" alt="GoPay" style="width:56px;height:56px;object-fit:contain;margin:10px auto 12px auto;display:block;border-radius:12px;" />
                                        <span>GoPay</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="download-section">
                                <a href="#" class="download-btn">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
                                    <span>Download Sekarang!</span>
                                </a>
                                <a href="#" class="download-btn">
                                    <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store">
                                    <span>Download Sekarang!</span>
                                </a>
                            </div>
                            
                            <div class="info-section">
                                <p>Info lebih lanjut hubungi Call Center <span class="highlight">Bank FTI: 1500-FTI</span></p>
                                <p style="font-size:0.9rem;">*) Nikmati bebas biaya transaksi hanya di menu top up dan pembayaran.</p>
                            </div>
                        </div>
                        
                        <div class="hero-right">
                            <div class="phone-mockup">
                                <div class="phone-screen">
                                    <img src="image/transaksi.png" alt="Transaksi Banking" />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="slide">
                <section class="about-section">
                    <div class="about-container">
                        <h2 style="font-size:2.3rem;font-weight:800;color:#1a237e;margin-bottom:1.2rem;letter-spacing:1px;">Tentang Kami</h2>
                        <p style="font-size:1.1rem;color:#222;max-width:700px;margin:0 auto 2.5rem auto;line-height:1.6;">
                            FTI M-Banking adalah penyedia solusi mobile banking inovatif terkemuka. Misi kami adalah memberdayakan nasabah dengan layanan keuangan yang aman, mudah, dan praktis langsung di genggaman Anda.
                        </p>
                        <h3 style="font-size:1.5rem;font-weight:700;color:#1a237e;margin-bottom:2rem;">Nilai Kami</h3>
                        <div class="values-row" style="display:flex;gap:2.5rem;justify-content:center;flex-wrap:wrap;overflow-x:auto;">
                            <div class="value-card" style="flex:0 0 220px;background:#f7fafd;border-radius:18px;padding:2rem 1.2rem 1.5rem 1.2rem;box-shadow:0 2px 12px rgba(25,118,210,0.07);margin-bottom:1.2rem;">
                                <div style="margin-bottom:1.1rem;">
                                    <!-- Shield Icon -->
                                    <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1976d2"/><path d="M24 12l10 4v7c0 7.5-5.5 13.5-10 15-4.5-1.5-10-7.5-10-15v-7l10-4z" fill="#fff"/><path d="M24 14.5l8 3.2v5.8c0 6.2-4.5 11.2-8 12.5-3.5-1.3-8-6.3-8-12.5v-5.8l8-3.2z" fill="#1976d2"/></svg>
                                </div>
                                <div style="font-weight:700;font-size:1.1rem;color:#1a237e;margin-bottom:0.5rem;">Keamanan</div>
                                <div style="font-size:0.98rem;color:#333;opacity:0.85;">Kami mengutamakan keamanan dan privasi data keuangan nasabah.</div>
                            </div>
                            <div class="value-card" style="flex:0 0 220px;background:#f7fafd;border-radius:18px;padding:2rem 1.2rem 1.5rem 1.2rem;box-shadow:0 2px 12px rgba(25,118,210,0.07);margin-bottom:1.2rem;">
                                <div style="margin-bottom:1.1rem;">
                                    <!-- Hand/Phone Icon -->
                                    <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1976d2"/><path d="M32 16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H16a2 2 0 0 1-2-2V18a2 2 0 0 1 2-2h16zm-8 18a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" fill="#fff"/><rect x="20" y="20" width="8" height="8" rx="2" fill="#1976d2"/></svg>
                                </div>
                                <div style="font-weight:700;font-size:1.1rem;color:#1a237e;margin-bottom:0.5rem;">Kemudahan</div>
                                <div style="font-size:0.98rem;color:#333;opacity:0.85;">Aplikasi kami dirancang untuk memberikan pengalaman perbankan yang mudah dan nyaman kapan saja, di mana saja.</div>
                            </div>
                            <div class="value-card" style="flex:0 0 220px;background:#f7fafd;border-radius:18px;padding:2rem 1.2rem 1.5rem 1.2rem;box-shadow:0 2px 12px rgba(25,118,210,0.07);margin-bottom:1.2rem;">
                                <div style="margin-bottom:1.1rem;">
                                    <!-- Rocket Icon -->
                                    <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1976d2"/><path d="M24 14l6 6-6 14-6-14 6-6zm0 2.8L20.7 20h6.6L24 16.8zm0 17.2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" fill="#fff"/><circle cx="24" cy="34" r="2" fill="#1976d2"/></svg>
                                </div>
                                <div style="font-weight:700;font-size:1.1rem;color:#1a237e;margin-bottom:0.5rem;">Inovasi</div>
                                <div style="font-size:0.98rem;color:#333;opacity:0.85;">Kami terus berinovasi menghadirkan fitur-fitur baru untuk memenuhi kebutuhan nasabah.</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="slide">
                <section class="services-section">
                    <h2 class="services-title">Layanan Kami</h2>
                    <p class="services-desc">
                        FTI M-Banking adalah penyedia solusi mobile banking inovatif terkemuka. Misi kami adalah memberdayakan nasabah dengan layanan keuangan yang aman, mudah, dan praktis langsung di genggaman Anda.
                    </p>
                    <div class="services-row">
                        <div class="service-card">
                            <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1a237e"/><path d="M16 22h16M14 26h20M12 30h24M24 14l10 8v2H14v-2l10-8z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <div class="service-title">Manajemen Akun</div>
                            <div>Mengelola dan mengakses akun bank Anda dengan mudah kapan saja, di mana saja.</div>
                        </div>
                        <div class="service-card">
                            <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1a237e"/><path d="M30 18l6 6-6 6M18 30l-6-6 6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M36 24H12" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
                            <div class="service-title">Transfer Dana</div>
                            <div>Transfer dana antar akun atau ke bank lain dengan cepat dan aman.</div>
                        </div>
                        <div class="service-card">
                            <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1a237e"/><rect x="16" y="18" width="16" height="12" rx="2" fill="#fff"/><path d="M20 22h8M20 26h8" stroke="#1a237e" stroke-width="2" stroke-linecap="round"/></svg>
                            <div class="service-title">Pembayaran Tagihan</div>
                            <div>Bayar tagihan Anda dengan aman dan praktis melalui aplikasi kami.</div>
                        </div>
                        <div class="service-card">
                            <svg width="48" height="48" fill="none" viewBox="0 0 48 48"><rect width="48" height="48" rx="24" fill="#1a237e"/><rect x="18" y="16" width="12" height="16" rx="2" fill="#fff"/><path d="M24 28v-4M24 24h2a2 2 0 1 0-2-2" stroke="#1a237e" stroke-width="2" stroke-linecap="round"/></svg>
                            <div class="service-title">Setoran Mobile</div>
                            <div>Setor dana dengan mudah melalui foto cek atau dokumen via aplikasi mobile Anda.</div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
    <script>
    let currentSlide = 0;
    const maxSlide = 2;
    function slideTo(idx) {
        const track = document.getElementById('sliderTrack');
        currentSlide = idx;
        track.style.transform = `translateX(-${idx * 100}vw)`;
        document.getElementById('arrowLeft').style.display = idx === 0 ? 'none' : '';
        document.getElementById('arrowRight').style.display = idx === maxSlide ? 'none' : '';
        if(document.getElementById('arrowMid')) document.getElementById('arrowMid').style.display = 'none';
    }
    // Optional: swipe gesture for mobile
    document.addEventListener('DOMContentLoaded', function() {
        let startX = null;
        const track = document.getElementById('sliderTrack');
        track.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        });
        track.addEventListener('touchend', function(e) {
            if (startX === null) return;
            let endX = e.changedTouches[0].clientX;
            if (endX - startX > 60) slideTo(0); // swipe right
            else if (startX - endX > 60) slideTo(2); // swipe left
            startX = null;
        });
    });
    </script>
</body>
</html>