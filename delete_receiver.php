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
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    exit;
}
try {
    $db = new Database();
    $pdo = $db->getConnection();
    $stmt = $pdo->prepare('DELETE FROM receivers WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Penerima tidak ditemukan atau tidak dapat dihapus.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus penerima: ' . $e->getMessage()]);
} 