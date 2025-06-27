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

// Jika hanya mengirim review
if (isset($_POST['review']) && isset($_POST['transfer_id'])) {
    $review = trim($_POST['review']);
    $transfer_id = intval($_POST['transfer_id']);
    if ($transfer_id <= 0 || !$review) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit;
    }
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare('UPDATE transfer_history SET review = ? WHERE id = ? AND from_user = ?');
        $stmt->execute([$review, $transfer_id, $user_id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ulasan: ' . $e->getMessage()]);
    }
    exit;
}

// Proses transfer seperti sebelumnya
$receiver_id = intval($_POST['receiver_id'] ?? 0);
$amount = intval($_POST['amount'] ?? 0);
$note = trim($_POST['note'] ?? '');
$date = date('Y-m-d H:i:s');
$rating = intval($_POST['rating'] ?? 0);
$review = trim($_POST['review'] ?? '');
if ($receiver_id <= 0 || $amount < 1000) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
    exit;
}
try {
    $db = new Database();
    $pdo = $db->getConnection();
    // Ambil rekening penerima
    $stmt = $pdo->prepare('SELECT account_number FROM receivers WHERE id = ? AND user_id = ?');
    $stmt->execute([$receiver_id, $user_id]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$receiver) {
        echo json_encode(['success' => false, 'message' => 'Penerima tidak ditemukan.']);
        exit;
    }
    // Ambil user_id tujuan dari nomor rekening
    $stmt2 = $pdo->prepare('SELECT id FROM users WHERE account_number = ?');
    $stmt2->execute([$receiver['account_number']]);
    $to_user = $stmt2->fetch(PDO::FETCH_ASSOC);
    if (!$to_user) {
        echo json_encode(['success' => false, 'message' => 'Akun tujuan tidak ditemukan.']);
        exit;
    }
    $to_user_id = $to_user['id'];
    if ($to_user_id == $user_id) {
        echo json_encode(['success' => false, 'message' => 'Tidak bisa transfer ke diri sendiri.']);
        exit;
    }
    // Cek saldo cukup
    $stmt3 = $pdo->prepare('SELECT balance FROM users WHERE id = ?');
    $stmt3->execute([$user_id]);
    $balance = $stmt3->fetchColumn();
    if ($balance < $amount) {
        echo json_encode(['success' => false, 'message' => 'Saldo tidak cukup.']);
        exit;
    }
    $pdo->beginTransaction();
    // Kurangi saldo pengirim
    $stmt4 = $pdo->prepare('UPDATE users SET balance = balance - ? WHERE id = ?');
    $stmt4->execute([$amount, $user_id]);
    // Tambah saldo penerima
    $stmt5 = $pdo->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
    $stmt5->execute([$amount, $to_user_id]);
    // Simpan riwayat transfer
    $stmt6 = $pdo->prepare('INSERT INTO transfer_history (from_user, to_user, receiver_id, amount, note, transfer_date, rating, review, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt6->execute([$user_id, $to_user_id, $receiver_id, $amount, $note, $date, $rating, $review]);
    $transfer_id = $pdo->lastInsertId();
    // Ambil saldo terbaru
    $stmt7 = $pdo->prepare('SELECT balance FROM users WHERE id = ?');
    $stmt7->execute([$user_id]);
    $new_balance = $stmt7->fetchColumn();
    $pdo->commit();
    echo json_encode(['success' => true, 'new_balance' => $new_balance, 'transfer_id' => $transfer_id]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Gagal transfer: ' . $e->getMessage()]);
} 