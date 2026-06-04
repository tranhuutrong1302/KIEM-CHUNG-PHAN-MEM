<?php
include __DIR__ . '/../config/db.php';
require_once __DIR__ . '/BidHandler.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = [];
}

$result = handleBid(
    $input,
    $_SESSION,
    function (int $productId) use ($conn): ?array {
        $query = mysqli_query($conn, "SELECT * FROM products WHERE id=$productId");
        if (!$query) {
            return null;
        }

        $product = mysqli_fetch_assoc($query);
        return is_array($product) ? $product : null;
    },
    function (int $userId, int $productId, int $bidAmount) use ($conn): bool {
        $updated = mysqli_query($conn, "UPDATE products SET price=$bidAmount WHERE id=$productId");
        $inserted = mysqli_query($conn, "INSERT INTO bids (user_id, product_id, bid_amount) VALUES ($userId, $productId, $bidAmount)");
        return (bool)$updated && (bool)$inserted;
    },
    date('Y-m-d H:i:s')
);

http_response_code($result['status']);
echo json_encode($result['body']);
