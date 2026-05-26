<?php
session_start(); // Khởi động session cho toàn bộ web
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dau_gia";

$conn = mysqli_connect($servername, $username, $password, $dbname);
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set múi giờ Việt Nam

if (!$conn) die("Kết nối thất bại: " . mysqli_connect_error());

// Hàm kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>