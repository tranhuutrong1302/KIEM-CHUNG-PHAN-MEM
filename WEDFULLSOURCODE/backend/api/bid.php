<?php
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đấu giá']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = [];
}

$product_id = isset($input['product_id']) ? (int)$input['product_id'] : null;
$bid_amount = isset($input['bid_amount']) ? (int)$input['bid_amount'] : null;

if (!$product_id || !$bid_amount) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu đặt giá không hợp lệ']);
    exit();
}

$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id"));
if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit();
}

$now = date('Y-m-d H:i:s');
if ($now > $product['end_time']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phiên đấu giá đã kết thúc']);
    exit();
}

$minRequired = $product['price'] + $product['min_increment'];
if ($bid_amount < $minRequired) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Giá đấu phải cao hơn mức hiện tại + bước giá']);
    exit();
}

$user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE products SET price=$bid_amount WHERE id=$product_id");
mysqli_query($conn, "INSERT INTO bids (user_id, product_id, bid_amount) VALUES ($user_id, $product_id, $bid_amount)");

echo json_encode(['success' => true, 'message' => 'Đấu giá thành công']);
