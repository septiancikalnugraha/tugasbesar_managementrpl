<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

file_put_contents('debug_session.txt', json_encode($_SESSION) . PHP_EOL, FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Silakan login ulang.']);
    exit;
}

if (isset($_POST['topup_id']) && isset($_POST['review'])) {
    $user_id = $_SESSION['user_id'];
    $topup_id = intval($_POST['topup_id']);
    $review = trim($_POST['review']);
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare('UPDATE topup_history SET review = :review WHERE id = :id AND user_id = :user_id');
        $stmt->execute([':review' => $review, ':id' => $topup_id, ':user_id' => $user_id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ulasan: ' . $e->getMessage()]);
    }
    exit;
}

$user_id = $_SESSION['user_id'];
$ewallet = $_POST['ewallet'] ?? '';
$rekening = $_POST['rekening'] ?? '';
$amount = intval($_POST['amount'] ?? 0);
if ($amount < 1000 || !$ewallet || !$rekening) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak valid.',
        'debug' => [
            'ewallet' => $ewallet,
            'rekening' => $rekening,
            'amount' => $amount,
            'raw_post' => $_POST
        ]
    ]);
    exit;
}

file_put_contents('debug_topup.txt', json_encode($_POST) . PHP_EOL, FILE_APPEND);

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $pdo->beginTransaction();
    // Update saldo user
    $stmt = $pdo->prepare('UPDATE users SET balance = balance + :amount WHERE id = :id');
    $stmt->execute([':amount' => $amount, ':id' => $user_id]);
    // Insert ke riwayat
    $stmt2 = $pdo->prepare('INSERT INTO topup_history (user_id, ewallet, rekening, nominal, tanggal, review) VALUES (:user_id, :ewallet, :rekening, :nominal, NOW(), :review)');
    $stmt2->execute([
        ':user_id' => $user_id,
        ':ewallet' => $ewallet,
        ':rekening' => $rekening,
        ':nominal' => $amount,
        ':review' => isset($_POST['review']) ? trim($_POST['review']) : null
    ]);
    $topup_id = $pdo->lastInsertId();
    // Ambil saldo terbaru
    $stmt3 = $pdo->prepare('SELECT balance FROM users WHERE id = :id');
    $stmt3->execute([':id' => $user_id]);
    $new_balance = $stmt3->fetchColumn();
    $pdo->commit();
    echo json_encode(['success' => true, 'new_balance' => $new_balance, 'topup_id' => $topup_id]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Gagal top up: ' . $e->getMessage()]);
} 