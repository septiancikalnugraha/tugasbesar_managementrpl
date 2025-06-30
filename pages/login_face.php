<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/auth.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$check_only = isset($data['check_only']) ? (bool)$data['check_only'] : false;

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email dan password wajib diisi']);
    exit;
}

$auth = new Auth();
$result = $auth->login($email, $password, true); // true = hanya cek, tidak set session

if (!$result['success']) {
    echo json_encode(['success' => false, 'message' => $result['message']]);
    exit;
}

if ($check_only) {
    echo json_encode(['success' => true]);
    exit;
}

// Login final, set session
$user = $result['user'];
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
echo json_encode(['success' => true]); 