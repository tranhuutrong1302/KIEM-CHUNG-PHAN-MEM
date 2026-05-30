<?php
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

function getCategory($row) {
    if (!empty($row['category']) && $row['category'] != 'Khác') return $row['category'];
    $n = mb_strtolower($row['name']);
    if (strpos($n, 'biển') !== false || strpos($n, '30a') !== false) return 'Biển số';
    if (strpos($n, 'nhẫn') !== false || strpos($n, 'dây') !== false || strpos($n, 'kim cương') !== false || strpos($n, 'đá quý') !== false || strpos($n, 'vòng') !== false) return 'Trang sức';
    if (strpos($n, 'đồng hồ') !== false || strpos($n, 'patek') !== false || strpos($n, 'richard') !== false || strpos($n, 'rolex') !== false) return 'Đồng hồ';
    if (strpos($n, 'biệt thự') !== false || strpos($n, 'penthouse') !== false || strpos($n, 'đất') !== false) return 'Bất động sản';
    return 'Xe sang';
}

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY end_time DESC");
$now = date("Y-m-d H:i:s");
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['is_expired'] = ($now > $row['end_time']);
    $row['next_min'] = $row['price'] + $row['min_increment'];
    $row['category_name'] = getCategory($row);
    $count_query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM bids WHERE product_id=" . (int)$row['id']));
    $row['bid_count'] = $count_query ? (int)$count_query['t'] : 0;
    $row['end_time_js'] = strtotime($row['end_time']) * 1000;
    $products[] = $row;
}

echo json_encode($products);
