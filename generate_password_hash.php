<?php
// Script untuk menghasilkan hash password yang valid
$password = 'teller123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";

// Test verifikasi
if (password_verify($password, $hash)) {
    echo "Hash valid!\n";
} else {
    echo "Hash tidak valid!\n";
}
?> 