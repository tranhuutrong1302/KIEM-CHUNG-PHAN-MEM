<?php
session_start(); // Khởi động session cho toàn bộ web
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dau_gia";

$conn = mysqli_connect($servername, $username, $password, $dbname);
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set múi giờ Việt Nam

if (!$conn) die("Kết nối thất bại: " . mysqli_connect_error());

// Hàm chuẩn hóa đường dẫn ảnh từ upload
function normalizeImageUrl($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $trimmed = ltrim($path, '/');
    $parts = explode('/', $trimmed);
    $encodedParts = array_map('rawurlencode', $parts);
    return '/' . implode('/', $encodedParts);
}

// Hàm kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>