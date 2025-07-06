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
    echo json_encode(['success' => false, 'message' => 'Session expired. Silakan login ulang.']);
    exit;
}
$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$account_number = trim($_POST['account_number'] ?? '');
if (!$name || !$account_number) {
    echo json_encode(['success' => false, 'message' => 'Nama dan nomor rekening wajib diisi.']);
    exit;
}
try {
    $db = new Database();
    $pdo = $db->getConnection();
    $stmt = $pdo->prepare('INSERT INTO receivers (user_id, name, account_number) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $name, $account_number]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal menambah penerima: ' . $e->getMessage()]);
} 