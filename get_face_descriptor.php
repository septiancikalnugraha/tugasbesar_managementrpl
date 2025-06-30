<?php
// get_face_descriptor.php
header('Content-Type: application/json');
$email = isset($_GET['email']) ? strtolower($_GET['email']) : '';
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan']);
    exit;
}
$filename = __DIR__ . '/assets/face_descriptors/' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $email) . '.json';
if (!file_exists($filename)) {
    echo json_encode(['success' => false, 'message' => 'Data wajah tidak ditemukan']);
    exit;
}
$data = json_decode(file_get_contents($filename), true);
echo json_encode(['success' => true, 'descriptor' => $data['descriptor']]); 