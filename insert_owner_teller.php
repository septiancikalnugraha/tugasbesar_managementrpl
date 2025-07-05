<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$users = [
    [
        'full_name' => 'Owner Bank',
        'email' => 'owner@bank.com',
        'phone' => '0811111111',
        'password' => password_hash('owner123', PASSWORD_DEFAULT),
        'account_number' => 'FTI00000001',
        'role' => 'owner'
    ],
    [
        'full_name' => 'Teller Bank',
        'email' => 'teller@bank.com',
        'phone' => '0822222222',
        'password' => password_hash('teller123', PASSWORD_DEFAULT),
        'account_number' => '+62 30100000002',
        'role' => 'teller'
    ]
];

foreach ($users as $user) {
    $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password, account_number, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user['full_name'],
        $user['email'],
        $user['phone'],
        $user['password'],
        $user['account_number'],
        $user['role']
    ]);
}
echo "Owner dan Teller berhasil ditambahkan!"; 