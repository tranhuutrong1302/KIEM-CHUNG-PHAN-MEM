<?php
include 'config/db.php';
require_once __DIR__ . '/domain/AuctionRules.php';

// =========================================================================
// PHẦN 1: XỬ LÝ LOGIC NGHIỆP VỤ (BUSINESS LOGIC)
// =========================================================================

// Xử lý Form Đặt Giá
if (isset($_POST['submit_bid'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để đấu giá!'); window.location='login.php';</script>"; 
        exit();
    }
    
    $prod_id = $_POST['product_id'];
    $bid_amount = str_replace('.', '', $_POST['bid_amount']); // Xóa dấu chấm
    $user_id = $_SESSION['user_id'];

    $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$prod_id"));
    $bidRule = validateBid($prod, (int)$bid_amount, date("Y-m-d H:i:s"));

    if (!$bidRule['ok']) {
        echo "<script>alert('" . $bidRule['message'] . "!');</script>";
    } else {
        // Cập nhật giá mới & Lưu lịch sử
        mysqli_query($conn, "UPDATE products SET price = $bid_amount WHERE id=$prod_id");
        mysqli_query($conn, "INSERT INTO bids (user_id, product_id, bid_amount) VALUES ($user_id, $prod_id, $bid_amount)");
        echo "<script>alert('Đấu giá thành công!'); window.location='index.php';</script>";
        exit(); // Dừng thực thi sau khi chuyển hướng
    }
}

// =========================================================================
// PHẦN 2: CHUẨN BỊ DỮ LIỆU ĐỂ ĐẨY SANG GIAO DIỆN (DATA PREPARATION)
// =========================================================================
$products_data = []; // Mảng chứa toàn bộ dữ liệu sản phẩm đã được tính toán sẵn
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY end_time DESC");
$now = date("Y-m-d H:i:s");

while ($row = mysqli_fetch_assoc($result)) {
    $pid = $row['id'];
    $count_query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM bids WHERE product_id=$pid"));
    
    // Đóng gói các giá trị tính toán vào mảng row
    $row['is_expired'] = ($now > $row['end_time']);
    $row['next_min'] = $row['price'] + $row['min_increment'];
    $row['category_name'] = resolveCategory($row);
    $row['bid_count'] = $count_query['t'];
    $row['end_time_js'] = strtotime($row['end_time']) * 1000;
    
    // Đẩy row vào mảng chính
    $products_data[] = $row;
}

// =========================================================================
// PHẦN 3: GỌI GIAO DIỆN HIỂN THỊ (VIEW ROUTING)
// =========================================================================
include 'views/index_view.php';
?>
