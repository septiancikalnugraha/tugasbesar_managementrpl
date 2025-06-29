<?php
// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
session_start();
header('Content-Type: application/json');

// Log untuk debugging
file_put_contents('debug_saldo.txt', date('Y-m-d H:i:s') . ' - Request to get_user_saldo.php' . PHP_EOL, FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    file_put_contents('debug_saldo.txt', date('Y-m-d H:i:s') . ' - Unauthorized: No user_id in session' . PHP_EOL, FILE_APPEND);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
file_put_contents('debug_saldo.txt', date('Y-m-d H:i:s') . ' - User ID: ' . $user_id . PHP_EOL, FILE_APPEND);

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $balance = floatval($user['balance']);
        file_put_contents('debug_saldo.txt', date('Y-m-d H:i:s') . ' - Success: Balance = ' . $balance . PHP_EOL, FILE_APPEND);
        
        echo json_encode([
            'success' => true,
            'balance' => $balance
        ]);
    } else {
        file_put_contents('debug_saldo.txt', date('Y-m-d H:i:s') . ' - Error: User not found' . PHP_EOL, FILE_APPEND);
        echo json_encode(['error' => 'User tidak ditemukan']);
    }
    
} catch (Exception $e) {
    file_put_contents('debug_saldo.txt', date('Y-m-d H:i:s') . ' - Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 