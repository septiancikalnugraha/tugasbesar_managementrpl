<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nasabah_id'], $_POST['status'])) {
    require_once '../config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    $nasabah_id = intval($_POST['nasabah_id']);
    $status = ($_POST['status'] === 'Aktif') ? 'Aktif' : 'Nonaktif';
    try {
        $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ? AND role = "nasabah"');
        $stmt->execute([$status, $nasabah_id]);
    } catch (Exception $e) {
        // Optional: log error
    }
}
header('Location: teller_nasabah.php');
exit; 