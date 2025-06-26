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
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
try {
    $db = new Database();
    $pdo = $db->getConnection();
    if ($type === 'topup') {
        // Hanya riwayat topup
        $stmt = $pdo->prepare('SELECT id, tanggal, ewallet, rekening, nominal, review FROM topup_history WHERE user_id = :user_id ORDER BY tanggal DESC');
        $stmt->execute([':user_id' => $user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['tanggal'] = date('d/m/Y, H:i', strtotime($row['tanggal']));
        }
        echo json_encode($rows);
        exit;
    } else if ($type === 'transfer') {
        // Hanya riwayat transfer
        $stmt = $pdo->prepare('SELECT IFNULL(th.transfer_date, th.created_at) as tanggal, u.account_number as rekening, th.amount as nominal, th.review FROM transfer_history th JOIN users u ON th.to_user = u.id WHERE th.from_user = :user_id ORDER BY tanggal DESC');
        $stmt->execute([':user_id' => $user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['tanggal'] = date('d/m/Y, H:i', strtotime($row['tanggal']));
        }
        echo json_encode($rows);
        exit;
    } else {
        // Gabungan (default)
        $stmt = $pdo->prepare('SELECT id, tanggal, ewallet, rekening, nominal, review, "topup" as kategori FROM topup_history WHERE user_id = :user_id ORDER BY tanggal DESC');
        $stmt->execute([':user_id' => $user_id]);
        $topup = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt2 = $pdo->prepare('SELECT IFNULL(th.transfer_date, th.created_at) as tanggal, "Transfer" as ewallet, u.account_number as rekening, th.amount as nominal, th.review, "transfer" as kategori FROM transfer_history th JOIN users u ON th.to_user = u.id WHERE th.from_user = :user_id ORDER BY tanggal DESC');
        $stmt2->execute([':user_id' => $user_id]);
        $transfer = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $all = array_merge($topup, $transfer);
        usort($all, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });
        foreach ($all as &$row) {
            $row['tanggal'] = date('d/m/Y, H:i', strtotime($row['tanggal']));
        }
        echo json_encode($all);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 