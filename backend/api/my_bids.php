<?php
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$sql = "SELECT b.bid_time, b.bid_amount, p.id as product_id, p.name, p.image, p.price as current_price, p.end_time, p.is_paid, 
               (SELECT status FROM orders WHERE product_id = p.id AND user_id = $user_id LIMIT 1) as order_status
        FROM bids b
        JOIN products p ON b.product_id = p.id
        WHERE b.user_id = $user_id
        ORDER BY b.bid_time DESC";
$result = mysqli_query($conn, $sql);
$bids = [];
$now = date("Y-m-d H:i:s");
while ($row = mysqli_fetch_assoc($result)) {
    $row['end_time_js'] = strtotime($row['end_time']) * 1000;
    $bids[] = $row;
}

echo json_encode(['success' => true, 'data' => $bids]);
