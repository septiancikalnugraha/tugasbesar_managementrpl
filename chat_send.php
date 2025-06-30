<?php
include 'config/database.php';
session_start();

// Cek apakah user atau teller yang login
$sender_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['teller_id']) ? $_SESSION['teller_id'] : null);
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($sender_id && $receiver_id && !empty($message)) {
    $stmt = $conn->prepare("INSERT INTO chat (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
    echo "success";
} else {
    echo "error";
}
?> 