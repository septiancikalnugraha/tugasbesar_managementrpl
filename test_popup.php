<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Popup - Bank FTI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .test-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(25,118,210,0.13);
        }
        .test-button {
            background: #1976d2;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            cursor: pointer;
            margin: 10px;
            font-size: 1rem;
            font-weight: 600;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 3000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.25);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 18px;
            max-width: 400px;
            width: 90vw;
            box-shadow: 0 8px 32px rgba(25,118,210,0.13);
            position: relative;
        }
        .close-btn {
            position: absolute;
            top: 12px;
            right: 18px;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: #1976d2;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1 style="color: #1976d2; text-align: center; margin-bottom: 30px;">Test Popup Functionality</h1>
        
        <div style="text-align: center;">
            <button class="test-button" onclick="openModal('modal-ubah-password')">
                <i class="fas fa-key"></i> Test Ubah Password
            </button>
            <button class="test-button" onclick="openModal('modal-ubah-email-hp')">
                <i class="fas fa-envelope"></i> Test Ubah Email/HP
            </button>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="pages/dashboard_pengaturan.php" style="color: #1976d2; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard Pengaturan
            </a>
        </div>
    </div>
    
    <!-- Modal Ubah Password -->
    <div id="modal-ubah-password" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('modal-ubah-password')">&times;</button>
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
    <div id="modal-ubah-email-hp" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('modal-ubah-email-hp')">&times;</button>
            <h3 style="margin-bottom:1.5rem;color:#1976d2;font-weight:700;">Ubah Email / No. HP</h3>
            <form id="form-ubah-email-hp">
                <div style="margin-bottom:1.2rem;">
                    <label for="email-baru" style="font-weight:600;display:block;margin-bottom:0.4rem;">Email Baru</label>
                    <input type="email" id="email-baru" name="email_baru" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
                </div>
                <div style="margin-bottom:1.2rem;">
                    <label for="no-hp-baru" style="font-weight:600;display:block;margin-bottom:0.4rem;">No. HP Baru</label>
                    <input type="tel" id="no-hp-baru" name="no_hp_baru" required style="width:100%;padding:0.8rem;border-radius:8px;border:1.5px solid #e3e7ed;font-size:1rem;">
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
        function openModal(modalId) {
            console.log('Opening modal:', modalId);
            document.getElementById(modalId).style.display = 'flex';
        }
        
        function closeModal(modalId) {
            console.log('Closing modal:', modalId);
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = ['modal-ubah-password', 'modal-ubah-email-hp'];
            modals.forEach(function(modalId) {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        };
        
        // Handle form submissions
        document.getElementById('form-ubah-password').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Password berhasil diubah!');
            closeModal('modal-ubah-password');
        });
        
        document.getElementById('form-ubah-email-hp').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Email dan No. HP berhasil diubah!');
            closeModal('modal-ubah-email-hp');
        });
        
        // Test on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Test page loaded successfully');
        });
    </script>
</body>
</html> 