<?php
echo "<h2>Pemeriksaan Session</h2>";

// Test session configuration
echo "<h3>1. Session Configuration</h3>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";
echo "<p>Session Name: " . session_name() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "<p style='color: green;'>✅ Session started</p>";
} else {
    echo "<p style='color: blue;'>ℹ️ Session already active</p>";
}

// Check session data
echo "<h3>2. Session Data</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ No user_id in session</p>";
}

if (isset($_SESSION['user_name'])) {
    echo "<p style='color: green;'>✅ User Name: " . $_SESSION['user_name'] . "</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No user_name in session</p>";
}

// Show all session data
echo "<h3>3. All Session Data</h3>";
if (empty($_SESSION)) {
    echo "<p style='color: red;'>❌ Session is empty</p>";
} else {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}

// Test session persistence
echo "<h3>4. Test Session Persistence</h3>";
if (!isset($_SESSION['test_time'])) {
    $_SESSION['test_time'] = date('Y-m-d H:i:s');
    echo "<p style='color: green;'>✅ Test session data set</p>";
} else {
    echo "<p style='color: green;'>✅ Test session data exists: " . $_SESSION['test_time'] . "</p>";
}

// Check cookies
echo "<h3>5. Session Cookies</h3>";
if (isset($_COOKIE[session_name()])) {
    echo "<p style='color: green;'>✅ Session cookie exists: " . $_COOKIE[session_name()] . "</p>";
} else {
    echo "<p style='color: red;'>❌ No session cookie found</p>";
}

// Test database connection
echo "<h3>6. Database Connection Test</h3>";
try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Test user query
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare('SELECT id, full_name, email FROM users WHERE id = :user_id');
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<p style='color: green;'>✅ User found in database: " . $user['full_name'] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ User not found in database</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Fix session if needed
echo "<h3>7. Session Fix</h3>";
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: orange;'>⚠️ No user_id in session. Please login again.</p>";
    echo "<p><a href='pages/login.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login</a></p>";
} else {
    echo "<p style='color: green;'>✅ Session looks good!</p>";
    echo "<p><a href='pages/dashboard_transaksi.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a></p>";
}

echo "<hr>";
echo "<p><a href='test_connection.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Full Connection Test</a></p>";
?> 