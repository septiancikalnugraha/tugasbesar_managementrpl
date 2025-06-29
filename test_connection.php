<?php
echo "<h2>Test Koneksi Server</h2>";

// Test 1: Koneksi Database
echo "<h3>1. Test Koneksi Database</h3>";
try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "<p style='color: green;'>✅ Koneksi database berhasil</p>";
        
        // Test query sederhana
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total users: " . $result['total'] . "</p>";
        
        // Test query tagihan
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM tagihan');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total tagihan: " . $result['total'] . "</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Koneksi database gagal</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error database: " . $e->getMessage() . "</p>";
}

// Test 2: Session
echo "<h3>2. Test Session</h3>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Session aktif</p>";
    if (isset($_SESSION['user_id'])) {
        echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
        echo "<p>User Name: " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ User belum login</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Session tidak aktif</p>";
}

// Test 3: File Permissions
echo "<h3>3. Test File Permissions</h3>";
$files_to_test = [
    'config/database.php',
    'upload_upgrade_request.php',
    'get_user_saldo.php',
    'pages/dashboard_transaksi.php'
];

foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "<p style='color: green;'>✅ " . $file . " - OK</p>";
        } else {
            echo "<p style='color: red;'>❌ " . $file . " - Tidak bisa dibaca</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ " . $file . " - File tidak ditemukan</p>";
    }
}

// Test 4: API Endpoints
echo "<h3>4. Test API Endpoints</h3>";

// Test upload_upgrade_request.php dengan GET
$url = 'http://localhost/tugasbesar_managementrpl/upload_upgrade_request.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

try {
    $response = file_get_contents($url, false, $context);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($data['error'])) {
                echo "<p style='color: orange;'>⚠️ API Response: " . $data['error'] . "</p>";
            } else {
                echo "<p style='color: green;'>✅ API Response: " . count($data) . " records</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ API Response bukan JSON valid</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tidak bisa mengakses API</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error API: " . $e->getMessage() . "</p>";
}

// Test 5: PHP Info
echo "<h3>5. PHP Configuration</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Enabled' : '❌ Disabled') . "</p>";
echo "<p>cURL: " . (extension_loaded('curl') ? '✅ Enabled' : '❌ Disabled') . "</p>";
echo "<p>JSON: " . (extension_loaded('json') ? '✅ Enabled' : '❌ Disabled') . "</p>";

// Test 6: Directory Structure
echo "<h3>6. Directory Structure</h3>";
$current_dir = getcwd();
echo "<p>Current Directory: " . $current_dir . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' . "</p>";

echo "<hr>";
echo "<p><a href='pages/dashboard_transaksi.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Dashboard</a></p>";
?> 