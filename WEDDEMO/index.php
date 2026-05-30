<?php
include 'config/db.php';

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
    
    if (date("Y-m-d H:i:s") > $prod['end_time']) {
        echo "<script>alert('Phiên đấu giá đã kết thúc!');</script>";
    } elseif ($bid_amount < ($prod['price'] + $prod['min_increment'])) {
        echo "<script>alert('Giá đấu phải cao hơn mức hiện tại + bước giá!');</script>";
    } else {
        // Cập nhật giá mới & Lưu lịch sử
        mysqli_query($conn, "UPDATE products SET price = $bid_amount WHERE id=$prod_id");
        mysqli_query($conn, "INSERT INTO bids (user_id, product_id, bid_amount) VALUES ($user_id, $prod_id, $bid_amount)");
        echo "<script>alert('Đấu giá thành công!'); window.location='index.php';</script>";
        exit(); // Dừng thực thi sau khi chuyển hướng
    }
}

// Hàm xác định danh mục
function getCategory($row) {
    if (!empty($row['category']) && $row['category'] != 'Khác') return $row['category'];
    $n = mb_strtolower($row['name']);
    if (strpos($n, 'biển') !== false || strpos($n, '30a') !== false) return 'Biển số';
    if (strpos($n, 'nhẫn') !== false || strpos($n, 'dây') !== false || strpos($n, 'kim cương') !== false || strpos($n, 'đá quý') !== false || strpos($n, 'vòng') !== false) return 'Trang sức';
    if (strpos($n, 'đồng hồ') !== false || strpos($n, 'patek') !== false || strpos($n, 'richard') !== false || strpos($n, 'rolex') !== false) return 'Đồng hồ';
    if (strpos($n, 'biệt thự') !== false || strpos($n, 'penthouse') !== false || strpos($n, 'đất') !== false) return 'Bất động sản';
    return 'Xe sang'; 
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
    $row['category_name'] = getCategory($row);
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