<?php
ob_start(); // Mulai output buffering
// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Jangan tampilkan error ke browser
ini_set('log_errors', 1); // Log error ke file log

require_once 'config/database.php';
session_start();
header('Content-Type: application/json');

// Log untuk debugging
file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - Request Method: ' . $_SERVER['REQUEST_METHOD'] . PHP_EOL, FILE_APPEND);

$method = $_SERVER['REQUEST_METHOD'];

// Cek session untuk semua method
if (!isset($_SESSION['user_id'])) {
    file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - Unauthorized: No user_id in session' . PHP_EOL, FILE_APPEND);
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Unauthorized or session expired. Silakan login ulang.']);
    exit;
}

$user_id = $_SESSION['user_id'];
file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - User ID: ' . $user_id . ', Method: ' . $method . PHP_EOL, FILE_APPEND);

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    if ($method === 'POST') {
        // Tambah tagihan baru (status Draft)
        $jenis = $_POST['jenis'] ?? '';
        $keterangan = $_POST['keterangan'] ?? '';
        $nominal = intval($_POST['nominal'] ?? 0);
        
        file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - POST Data: jenis=' . $jenis . ', keterangan=' . $keterangan . ', nominal=' . $nominal . PHP_EOL, FILE_APPEND);
        
        if (!$jenis || !$keterangan || !$nominal) {
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO tagihan (user_id, jenis, keterangan, nominal, status) VALUES (:user_id, :jenis, :keterangan, :nominal, "Draft")');
        $stmt->execute([
            ':user_id' => $user_id,
            ':jenis' => $jenis,
            ':keterangan' => $keterangan,
            ':nominal' => $nominal
        ]);
        ob_clean();
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
        
    } else if ($method === 'GET') {
        // Ambil semua tagihan user
        file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - GET request for user_id: ' . $user_id . PHP_EOL, FILE_APPEND);
        
        $stmt = $pdo->prepare('SELECT * FROM tagihan WHERE user_id = :user_id ORDER BY waktu DESC');
        $stmt->execute([':user_id' => $user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        file_put_contents('debug_tagihan.txt', json_encode($rows) . PHP_EOL, FILE_APPEND);
        ob_clean();
        echo json_encode($rows);
        exit;
        
    } else if ($method === 'PUT') {
        // Update status tagihan jadi Lunas dan tambahkan saldo ke rekening teller
        parse_str(file_get_contents('php://input'), $_PUT);
        $id = intval($_PUT['id'] ?? 0);
        
        file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - PUT request for tagihan_id: ' . $id . PHP_EOL, FILE_APPEND);
        
        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'ID tagihan tidak valid']);
            exit;
        }
        
        // Mulai transaction untuk keamanan data
        $pdo->beginTransaction();
        
        try {
            // 1. Ambil data tagihan
            $stmt = $pdo->prepare('SELECT * FROM tagihan WHERE id = :id AND user_id = :user_id AND status = "Belum Lunas"');
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            $tagihan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$tagihan) {
                throw new Exception('Tagihan tidak ditemukan atau sudah tidak dalam status Belum Lunas');
            }
            
            // 2. Ambil saldo teller saat ini
            $stmt = $pdo->prepare('SELECT balance FROM users WHERE role = "teller" LIMIT 1');
            $stmt->execute();
            $teller = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$teller) {
                throw new Exception('Data teller tidak ditemukan');
            }
            
            $saldoTeller = floatval($teller['balance']);
            $nominal = intval($tagihan['nominal']);
            
            // 3. Tambahkan saldo ke rekening teller
            $saldoTellerBaru = $saldoTeller + $nominal;
            $stmt = $pdo->prepare('UPDATE users SET balance = :balance WHERE role = "teller" LIMIT 1');
            $stmt->execute([':balance' => $saldoTellerBaru]);
            
            // 4. Update status tagihan menjadi "Lunas"
            $stmt = $pdo->prepare('UPDATE tagihan SET status = "Lunas" WHERE id = :id AND user_id = :user_id');
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            
            // Commit transaction
            $pdo->commit();
            
            ob_clean();
            echo json_encode([
                'success' => true, 
                'message' => 'Pembayaran berhasil! Saldo ditambahkan ke rekening teller Rp ' . number_format($nominal, 0, ',', '.'),
                'saldo_teller_sebelum' => $saldoTeller,
                'saldo_teller_sesudah' => $saldoTellerBaru,
                'nominal' => $nominal
            ]);
            
        } catch (Exception $e) {
            // Rollback jika ada error
            $pdo->rollBack();
            file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - PUT Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            ob_clean();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
        
    } else if ($method === 'PATCH') {
        // Update status tagihan dari Draft ke Belum Lunas (Order) dengan pengecekan saldo
        parse_str(file_get_contents('php://input'), $_PATCH);
        $id = intval($_PATCH['id'] ?? 0);
        
        file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - PATCH request for tagihan_id: ' . $id . PHP_EOL, FILE_APPEND);
        
        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'ID tagihan tidak valid']);
            exit;
        }
        
        // Mulai transaction untuk keamanan data
        $pdo->beginTransaction();
        
        try {
            // 1. Ambil data tagihan
            $stmt = $pdo->prepare('SELECT * FROM tagihan WHERE id = :id AND user_id = :user_id AND status = "Draft"');
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            $tagihan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$tagihan) {
                throw new Exception('Tagihan tidak ditemukan atau sudah tidak dalam status Draft');
            }
            
            // 2. Cek saldo user
            $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = :user_id');
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('Data user tidak ditemukan');
            }
            
            $saldo = floatval($user['balance']);
            $nominal = intval($tagihan['nominal']);
            
            // 3. Cek apakah saldo cukup
            if ($saldo < $nominal) {
                throw new Exception('Saldo tidak cukup. Saldo: Rp ' . number_format($saldo, 0, ',', '.') . ', Dibutuhkan: Rp ' . number_format($nominal, 0, ',', '.'));
            }
            
            // 4. Kurangi saldo user
            $saldoBaru = $saldo - $nominal;
            $stmt = $pdo->prepare('UPDATE users SET balance = :balance WHERE id = :user_id');
            $stmt->execute([':balance' => $saldoBaru, ':user_id' => $user_id]);
            
            // 5. Update status tagihan menjadi "Belum Lunas"
            $stmt = $pdo->prepare('UPDATE tagihan SET status = "Belum Lunas" WHERE id = :id AND user_id = :user_id');
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            
            // Commit transaction
            $pdo->commit();
            
            ob_clean();
            echo json_encode([
                'success' => true, 
                'message' => 'Order berhasil! Saldo berkurang Rp ' . number_format($nominal, 0, ',', '.'),
                'saldo_sebelum' => $saldo,
                'saldo_sesudah' => $saldoBaru,
                'nominal' => $nominal
            ]);
            
        } catch (Exception $e) {
            // Rollback jika ada error
            $pdo->rollBack();
            file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - PATCH Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            ob_clean();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
        
    } else if ($method === 'DELETE') {
        // Hapus tagihan draft milik user
        parse_str(file_get_contents('php://input'), $_DELETE);
        $id = intval($_DELETE['id'] ?? 0);
        
        file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - DELETE request for tagihan_id: ' . $id . PHP_EOL, FILE_APPEND);
        
        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'ID tagihan tidak valid']);
            exit;
        }
        
        // Hanya boleh hapus tagihan status Draft milik user
        $stmt = $pdo->prepare('DELETE FROM tagihan WHERE id = :id AND user_id = :user_id AND status = "Draft"');
        $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        
        if ($stmt->rowCount() > 0) {
            ob_clean();
            echo json_encode(['success' => true]);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'Tagihan tidak ditemukan atau bukan status Draft']);
        }
        exit;
        
    } else if ($method === 'POST' && isset($_FILES['bukti'])) {
        $user_id = $_SESSION['user_id'];
        $upload_dir = 'uploads/upgrade_bukti/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file = $_FILES['bukti'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'bukti_' . $user_id . '_' . time() . '.' . $ext;
        $target = $upload_dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Langsung upgrade user ke prioritas
            try {
                $db = new Database();
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare('UPDATE users SET kategori = :kategori WHERE id = :id');
                $stmt->execute([
                    ':kategori' => 'prioritas',
                    ':id' => $user_id
                ]);
                if (isset($_SESSION['user_data'])) {
                    $_SESSION['user_data']['kategori'] = 'prioritas';
                }
                echo json_encode(['success' => true, 'file' => $target]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Gagal upgrade: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Gagal upload file.']);
        }
        exit;
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    
} catch (Exception $e) {
    file_put_contents('debug_api.txt', date('Y-m-d H:i:s') . ' - General Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

// Fallback jika terjadi error di luar try-catch
if (!headers_sent()) {
    header('Content-Type: application/json');
}
echo json_encode(['success' => false, 'error' => 'Unknown error occurred.']);
exit; 