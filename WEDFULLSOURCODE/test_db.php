<?php
header("Content-Type: text/html; charset=utf-8");
$mysqli = new mysqli("127.0.0.1", "root", "", "dau_gia", 3306, "/opt/lampp/var/mysql/mysql.sock");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo "Lỗi kết nối database: " . $mysqli->connect_error;
} else {
    http_response_code(200);
    $count = $mysqli->query("SELECT COUNT(*) as cnt FROM products")->fetch_assoc()['cnt'];
    echo "Kết nối database thành công! Số sản phẩm trong DB: $count";
    $mysqli->close();
}
