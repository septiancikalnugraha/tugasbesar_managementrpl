<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

if (!$password_lama || !$password_baru || !$konfirmasi_password) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
    exit;
}

if ($password_baru !== $konfirmasi_password) {
    echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok.']);
    exit;
}

if (strlen($password_baru) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password baru minimal 6 karakter.']);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal.']);
    exit;
}

$stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($password_hash);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'User tidak ditemukan.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

if (!password_verify($password_lama, $password_hash)) {
    echo json_encode(['success' => false, 'message' => 'Password lama salah.']);
    $conn->close();
    exit;
}

$new_hash = password_hash($password_baru, PASSWORD_DEFAULT);
$stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
$stmt->bind_param('si', $new_hash, $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password berhasil diubah.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update password.']);
}
$stmt->close();
$conn->close(); 