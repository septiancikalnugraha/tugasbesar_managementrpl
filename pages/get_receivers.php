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
require_once '../config/database.php';
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
    // Ambil daftar favorit, jika tabelnya ada
    $fav_ids = [];
    try {
        $fav_stmt = $pdo->prepare('SELECT receiver_id FROM favorite_receivers WHERE user_id = ?');
        $fav_stmt->execute([$user_id]);
        $fav_ids = array_column($fav_stmt->fetchAll(PDO::FETCH_ASSOC), 'receiver_id');
    } catch (Exception $e) {
        // Jika tabel belum ada, abaikan saja
        $fav_ids = [];
    }
    foreach ($rows as &$row) {
        $row['is_favorite'] = in_array($row['id'], $fav_ids) ? 1 : 0;
    }
    echo json_encode($rows);
} catch (Exception $e) {
    echo json_encode([]);
} 