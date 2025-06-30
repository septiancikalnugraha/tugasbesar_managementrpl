<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
require_once 'config/database.php';
try {
    $db = new Database();
    $pdo = $db->getConnection();
    $stmt = $pdo->prepare('UPDATE users SET kategori = :kategori WHERE id = :id');
    $stmt->execute([
        ':kategori' => 'prioritas',
        ':id' => $_SESSION['user_id']
    ]);
    // Update session jika perlu
    if (isset($_SESSION['user_data'])) {
        $_SESSION['user_data']['kategori'] = 'prioritas';
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Gagal upgrade: ' . $e->getMessage()]);
} 