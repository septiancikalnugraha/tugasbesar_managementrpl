<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

try {
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }

    // Cek apakah ada file yang diupload
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded or upload error',
            'files' => $_FILES,
            'error_code' => isset($_FILES['photo']) ? $_FILES['photo']['error'] : 'not set'
        ]);
        exit;
    }

    $file = $_FILES['photo'];
    $user_id = $_SESSION['user_id'];

    // Validasi tipe file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = $file['type'];

    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    // Validasi ukuran file (maksimal 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.']);
        exit;
    }

    // Buat direktori uploads jika belum ada
    $upload_dir = '../uploads/profile_photos/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
            exit;
        }
    }

    // Generate nama file unik
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    $relative_path = 'uploads/profile_photos/' . $new_filename;

    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        exit;
    }

    // Update database
    require_once __DIR__ . '/includes/auth.php';
    $auth = new Auth();
    // Hapus foto lama jika ada
    $old_user_data = $auth->getUserData($user_id);
    $unlink_error = null;
    if ($old_user_data && !empty($old_user_data['profile_photo']) && file_exists('../' . $old_user_data['profile_photo'])) {
        if (!unlink('../' . $old_user_data['profile_photo'])) {
            $unlink_error = 'Gagal menghapus foto lama: ' . '../' . $old_user_data['profile_photo'];
        }
    }
    // Update path foto di database (pakai path relatif)
    $success = $auth->updateProfilePhoto($user_id, $relative_path);
    if ($success || $auth->db->errorCode() === '00000') {
        $updated_user = $auth->getUserData($user_id);
        echo json_encode([
            'success' => true, 
            'message' => 'Profile photo updated successfully',
            'photo' => $relative_path,
            'unlink_error' => $unlink_error,
            'upload_path' => $upload_path,
            'db_path' => $relative_path,
            'db_profile_photo' => $updated_user['profile_photo'] ?? null
        ]);
    } else {
        // Hapus file yang sudah diupload jika gagal update database
        unlink($upload_path);
        $errorInfo = $auth->db->errorInfo();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update database',
            'errorInfo' => $errorInfo,
            'user_id' => $user_id,
            'unlink_error' => $unlink_error,
            'upload_path' => $upload_path,
            'db_path' => $relative_path,
            'db_profile_photo' => $auth->getUserData($user_id)['profile_photo'] ?? null
        ]);
    }
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>