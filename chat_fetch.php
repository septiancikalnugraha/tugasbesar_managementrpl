<?php
include 'config/database.php';
session_start();

// Cek apakah user atau teller yang login
$self_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['teller_id']) ? $_SESSION['teller_id'] : null);
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : null;

if ($self_id && $partner_id) {
    $stmt = $conn->prepare("SELECT * FROM chat WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY created_at ASC");
    $stmt->bind_param("iiii", $self_id, $partner_id, $partner_id, $self_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $chats = [];
    while ($row = $result->fetch_assoc()) {
        $chats[] = $row;
    }
    echo json_encode($chats);
} else {
    echo json_encode([]);
}
?> 