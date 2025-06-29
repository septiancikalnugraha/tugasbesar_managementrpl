<?php
header('Content-Type: application/json');
session_start();
try {
    // ... kode simpan ke database ...
    // (kode lama tetap, pastikan ada koneksi dan query)
    // Jika berhasil:
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 