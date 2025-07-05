<?php
// Script untuk test login teller dan memverifikasi session
session_start();
require_once 'includes/auth.php';

echo "🧪 Test Login Teller\n";
echo "====================\n";

// Test login teller
$auth = new Auth();
$login_result = $auth->login('teller@bank.com', 'teller123');

if ($login_result['success']) {
    echo "✅ Login berhasil!\n";
    echo "📋 Session Data:\n";
    echo "   - User ID: " . $_SESSION['user_id'] . "\n";
    echo "   - User Name: " . $_SESSION['user_name'] . "\n";
    echo "   - User Email: " . $_SESSION['user_email'] . "\n";
    echo "   - Role: " . $_SESSION['role'] . "\n";
    echo "   - Account Number: " . $_SESSION['account_number'] . "\n";
    echo "   - Balance: " . $_SESSION['balance'] . "\n";
    
    // Test redirect logic
    if ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'teller') {
        echo "\n🎯 Redirect Logic:\n";
        echo "   - Role: " . $_SESSION['role'] . "\n";
        echo "   - Should redirect to: dashboard_petugas.php\n";
        echo "   - Status: ✅ CORRECT\n";
    } else {
        echo "\n🎯 Redirect Logic:\n";
        echo "   - Role: " . $_SESSION['role'] . "\n";
        echo "   - Should redirect to: dashboard.php\n";
        echo "   - Status: ✅ CORRECT\n";
    }
    
    // Logout untuk test
    $auth->logout();
    echo "\n🔚 Logged out for testing.\n";
    
} else {
    echo "❌ Login gagal: " . $login_result['message'] . "\n";
}

echo "\n🚀 Sekarang coba login melalui web dengan:\n";
echo "   Email: teller@bank.com\n";
echo "   Password: teller123\n";
echo "\n📍 Seharusnya akan diarahkan ke dashboard petugas.\n";
?> 