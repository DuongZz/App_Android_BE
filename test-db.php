<?php
$host = 'localhost';
$db   = 'ecommerce'; // tên cơ sở dữ liệu
$user = 'root';      // tên người dùng MySQL
$pass = 'Duck130603@';          // mật khẩu MySQL (nếu có thì điền vào)
$charset = 'utf8mb4';

// Kiểm tra kết nối bằng PDO
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ Kết nối thành công đến cơ sở dữ liệu!";
} catch (PDOException $e) {
    echo "❌ Kết nối thất bại: " . $e->getMessage();
}
