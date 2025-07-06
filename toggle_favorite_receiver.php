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
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}
$user_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
if (!$receiver_id) {
    echo json_encode(['error' => 'invalid_receiver']);
    exit;
}
require_once 'config/database.php';
$db = new Database();
$pdo = $db->getConnection();
try {
    // Cek apakah sudah favorit
    $stmt = $pdo->prepare("SELECT id FROM favorite_receivers WHERE user_id=? AND receiver_id=?");
    $stmt->execute([$user_id, $receiver_id]);
    if ($stmt->fetch()) {
        // Sudah favorit, hapus
        $del = $pdo->prepare("DELETE FROM favorite_receivers WHERE user_id=? AND receiver_id=?");
        $del->execute([$user_id, $receiver_id]);
        echo json_encode(['status' => 'removed']);
    } else {
        // Belum favorit, tambah
        $add = $pdo->prepare("INSERT INTO favorite_receivers (user_id, receiver_id) VALUES (?, ?)");
        $add->execute([$user_id, $receiver_id]);
        echo json_encode(['status' => 'added']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
} 