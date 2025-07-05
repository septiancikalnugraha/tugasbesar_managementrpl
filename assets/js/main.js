// Main JavaScript for Bank FTI
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth loading animation
    const body = document.body;
    body.style.opacity = '0';
    setTimeout(() => {
        body.style.transition = 'opacity 0.5s ease';
        body.style.opacity = '1';
    }, 100);

    // Add hover effects to cards
    const cards = document.querySelectorAll('.dashboard-card, .auth-card, .action-btn');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add loading states to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading"></span> Memproses...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    });

    // Add input focus effects
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value;
            } else if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }
            this.value = value;
        });
    }

    // Real-time validation for email
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#dc3545';
                this.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.1)';
            } else {
                this.style.borderColor = '#28a745';
                this.style.boxShadow = '0 0 0 3px rgba(40, 167, 69, 0.1)';
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Add click ripple effect
    const buttons = document.querySelectorAll('.btn, .action-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.className = 'ripple';
            ripple.style.position = 'absolute';
            ripple.style.background = 'rgba(0,0,0,0.15)';
            ripple.style.borderRadius = '50%';
            ripple.style.pointerEvents = 'none';
            ripple.style.transform = 'scale(0)';
            ripple.style.transition = 'transform 0.5s, opacity 0.5s';

            this.style.position = 'relative';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.style.transform = 'scale(2.5)';
                ripple.style.opacity = '0';
            }, 10);

            setTimeout(() => {
                if (ripple.parentNode) {
                    ripple.parentNode.removeChild(ripple);
                }
            }, 600);
        });
    });

    // --- Theme & Language Switcher ---
    (function() {
        // Dictionary untuk multi-bahasa (ID/EN)
        const dict = {
            id: {
                'Profil Saya': 'Profil Saya',
                'Ubah Password': 'Ubah Password',
                'Ubah Email / No. HP': 'Ubah Email / No. HP',
                'Pengaturan Notifikasi': 'Pengaturan Notifikasi',
                'Keamanan Akun': 'Keamanan Akun',
                'Bahasa & Tema': 'Bahasa & Tema',
                'Rekening Favorit': 'Rekening Favorit',
                'E-Wallet Terkait': 'E-Wallet Terkait',
                'Privasi & Data': 'Privasi & Data',
                'Hapus Akun': 'Hapus Akun',
                'Pusat Bantuan': 'Pusat Bantuan',
                'Chat dengan Bank FTI': 'Chat dengan Bank FTI',
                'Beri Masukan': 'Beri Masukan',
                'Terang': 'Terang',
                'Gelap': 'Gelap',
                'Bahasa': 'Bahasa',
                'Tema': 'Tema',
                'Simpan': 'Simpan',
                'Pengaturan': 'Pengaturan',
            },
            en: {
                'Profil Saya': 'My Profile',
                'Ubah Password': 'Change Password',
                'Ubah Email / No. HP': 'Change Email / Phone',
                'Pengaturan Notifikasi': 'Notification Settings',
                'Keamanan Akun': 'Account Security',
                'Bahasa & Tema': 'Language & Theme',
                'Rekening Favorit': 'Favorite Accounts',
                'E-Wallet Terkait': 'Linked E-Wallets',
                'Privasi & Data': 'Privacy & Data',
                'Hapus Akun': 'Delete Account',
                'Pusat Bantuan': 'Help Center',
                'Chat dengan Bank FTI': 'Chat with Bank FTI',
                'Beri Masukan': 'Feedback',
                'Terang': 'Light',
                'Gelap': 'Dark',
                'Bahasa': 'Language',
                'Tema': 'Theme',
                'Simpan': 'Save',
                'Pengaturan': 'Settings',
            }
        };
        // Apply theme
        const tema = localStorage.getItem('tema') || 'light';
        if (tema === 'dark') {
            document.body.classList.add('dark-theme');
            document.documentElement.style.background = '#181c24';
        } else {
            document.body.classList.remove('dark-theme');
            document.documentElement.style.background = '';
        }
        // Apply language
        const bahasa = localStorage.getItem('bahasa') || 'id';
        document.documentElement.lang = bahasa;
        // Ganti label menu pengaturan
        if (document.querySelector('.settings-menu')) {
            document.querySelectorAll('.settings-item span').forEach(function(span) {
                const text = span.textContent.trim();
                if (dict[bahasa][text]) span.textContent = dict[bahasa][text];
            });
            // Ganti judul pengaturan
            const pengaturanTitle = document.querySelector('.main-content .dashboard-section > div[style*="font-size:1.3rem"]');
            if (pengaturanTitle && dict[bahasa]['Pengaturan']) pengaturanTitle.textContent = dict[bahasa]['Pengaturan'];
        }
        // Ganti label modal Bahasa & Tema
        const modal = document.getElementById('modal-bahasa-tema');
        if (modal) {
            modal.querySelector('h3').textContent = dict[bahasa]['Bahasa & Tema'];
            modal.querySelector('label[for="select-bahasa"]').textContent = dict[bahasa]['Bahasa'];
            modal.querySelector('label[for="select-tema"]').textContent = dict[bahasa]['Tema'];
            modal.querySelector('#simpan-bahasa-tema').textContent = dict[bahasa]['Simpan'];
            // Ganti opsi tema
            const temaSelect = modal.querySelector('#select-tema');
            temaSelect.options[0].text = dict[bahasa]['Terang'];
            temaSelect.options[1].text = dict[bahasa]['Gelap'];
        }
    })();
});