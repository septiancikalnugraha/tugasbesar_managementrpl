<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        if (!$this->db) {
            die('Koneksi database gagal. Silakan cek konfigurasi database.php dan pastikan database berjalan.');
        }
    }
    
    public function register($full_name, $email, $phone, $password) {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email sudah terdaftar'];
            }
            
            // Generate account number
            $account_number = $this->generateAccountNumber();
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user, role default 'nasabah'
            $stmt = $this->db->prepare("INSERT INTO users (full_name, email, phone, password, account_number, role) VALUES (?, ?, ?, ?, ?, 'nasabah')");
            $result = $stmt->execute([$full_name, $email, $phone, $hashed_password, $account_number]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Registrasi berhasil', 'account_number' => $account_number];
            } else {
                return ['success' => false, 'message' => 'Registrasi gagal'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $password, $role) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
            $stmt->execute([$email, $role]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'User tidak ditemukan. Cek email dan role.'];
            }
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Password salah.'];
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['account_number'] = $user['account_number'];
            $_SESSION['balance'] = $user['balance'];
            $_SESSION['role'] = $user['role'];
            
            return ['success' => true, 'message' => 'Login berhasil'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    private function generateAccountNumber() {
        // Generate account number with format: FTI + 10 digits
        return 'FTI' . str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }
    
    public function getUserData($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>