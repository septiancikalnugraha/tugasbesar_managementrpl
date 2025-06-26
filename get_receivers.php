<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
session_start();
header('Content-Type: application/json');
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}
$user_id = $_SESSION['user_id'];
try {
    $db = new Database();
    $pdo = $db->getConnection();
    $stmt = $pdo->prepare('SELECT id, name, account_number FROM receivers WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (Exception $e) {
    echo json_encode([]);
} 