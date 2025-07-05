<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$nama = trim($_POST['nama_baru'] ?? '');
$email = trim($_POST['email_baru'] ?? '');
$phone = trim($_POST['no_hp_baru'] ?? '');
$password = $_POST['password_konfirmasi'] ?? '';

if (!$nama || !$email || !$phone || !$password) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
    exit;
}

// Validasi nama minimal 2 karakter
if (mb_strlen($nama) < 2) {
    echo json_encode(['success' => false, 'message' => 'Nama minimal 2 karakter.']);
    exit;
}

// Validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
    exit;
}

// Validasi nomor HP (10-13 digit)
if (!preg_match('/^[0-9]{10,13}$/', preg_replace('/\s+/', '', $phone))) {
    echo json_encode(['success' => false, 'message' => 'Format nomor HP tidak valid.']);
    exit;
}

// Ambil data user
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

if (!password_verify($password, $password_hash)) {
    echo json_encode(['success' => false, 'message' => 'Password konfirmasi salah.']);
    $conn->close();
    exit;
}

// Update nama, email dan phone
$stmt = $conn->prepare('UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?');
$stmt->bind_param('sssi', $nama, $email, $phone, $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profil berhasil diupdate.', 'name' => $nama, 'email' => $email, 'phone' => $phone]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update profil.']);
}
$stmt->close();
$conn->close(); 