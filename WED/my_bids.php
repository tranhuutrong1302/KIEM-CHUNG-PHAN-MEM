<?php
include 'db.php';
checkLogin(); // Bắt buộc đăng nhập
$user_id = $_SESSION['user_id'];

// Xử lý thanh toán (Khi bấm nút Thanh toán)
if (isset($_GET['pay'])) {
    $p_id = $_GET['pay'];
    $amount = $_GET['amount'];
    
    // Kiểm tra lại lần cuối xem user này có phải người thắng không
    $check_winner = mysqli_query($conn, "SELECT price FROM products WHERE id=$p_id");
    $prod_info = mysqli_fetch_assoc($check_winner);
    
    if ($prod_info['price'] == $amount) {
        // Tạo đơn hàng
        mysqli_query($conn, "INSERT INTO orders (user_id, product_id, amount) VALUES ($user_id, $p_id, $amount)");
        // Cập nhật trạng thái đã thanh toán
        mysqli_query($conn, "UPDATE products SET is_paid = 1 WHERE id = $p_id");
        echo "<script>alert('Thanh toán thành công! Cảm ơn bạn.'); window.location='my_bids.php';</script>";
    } else {
        echo "<script>alert('Lỗi: Giá sản phẩm đã thay đổi hoặc bạn không phải người thắng.'); window.location='my_bids.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Đấu Giá - Royal Bid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Manrope:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        /* --- CẤU HÌNH THEME ROYAL --- */
        :root {
            --gold-start: #bf953f;
            --gold-mid: #fcf6ba;
            --gold-end: #b38728;
            --bg-dark: #050505;
            --glass-bg: rgba(20, 20, 20, 0.7);
        }

        body { 
            background-color: var(--bg-dark);
            background-image: radial-gradient(circle at 50% 0%, #1a1a1a 0%, #000000 100%);
            font-family: 'Manrope', sans-serif; 
            color: #e0e0e0;
            min-height: 100vh;
        }

        /* --- NAVBAR (Đồng bộ trang chủ) --- */
        .navbar {
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
        }
        .navbar-brand { 
            font-family: 'Cinzel', serif; font-weight: 700; font-size: 1.4rem;
            background: linear-gradient(to right, var(--gold-start), var(--gold-mid), var(--gold-end));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* --- TIÊU ĐỀ TRANG --- */
        .page-header {
            text-align: center; margin: 40px 0;
        }
        .page-title {
            font-family: 'Cinzel', serif; color: var(--gold-mid); font-size: 2rem;
            display: inline-block; position: relative; padding-bottom: 15px;
        }
        .page-title::after {
            content: ''; position: absolute; bottom: 0; left: 25%; width: 50%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold-mid), transparent);
        }

        /* --- TABLE CONTAINER (GLASS STYLE) --- */
        .glass-container {
            background: var(--glass-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            padding: 20px;
            overflow: hidden;
        }

        /* --- TABLE CUSTOM --- */
        .custom-table {
            width: 100%;
            color: #ccc;
            vertical-align: middle;
        }
        .custom-table thead th {
            background: rgba(0,0,0,0.5);
            color: var(--gold-mid);
            font-family: 'Cinzel', serif;
            font-weight: normal;
            border-bottom: 1px solid var(--gold-start);
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .custom-table tbody td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .custom-table tbody tr {
            transition: all 0.3s;
        }
        .custom-table tbody tr:hover {
            background: rgba(255, 215, 0, 0.05); /* Hover màu vàng nhạt */
        }

        /* --- HÌNH ẢNH SẢN PHẨM --- */
        .product-thumb {
            width: 60px; height: 60px; object-fit: cover;
            border: 1px solid #444; border-radius: 4px;
        }

        /* --- STATUS BADGES (NEON STYLE) --- */
        .status-badge {
            padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; 
            font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            display: inline-block;
        }
        
        /* Thất bại (Red) */
        .badge-fail {
            color: #ff4757; border: 1px solid rgba(255, 71, 87, 0.3);
            background: rgba(255, 71, 87, 0.1);
        }
        
        /* Đang dẫn đầu (Blue/Gold) */
        .badge-lead {
            color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);
            background: rgba(46, 213, 115, 0.1);
        }
        
        /* Chiến thắng (Gold) */
        .badge-won {
            color: var(--gold-mid); border: 1px solid var(--gold-start);
            background: rgba(191, 149, 63, 0.2);
            box-shadow: 0 0 10px rgba(191, 149, 63, 0.2);
        }
        
        /* Đã thanh toán (Green) */
        .badge-paid {
            color: #fff; background: #2ed573; border: 1px solid #2ed573;
        }

        /* --- BUTTONS --- */
        .btn-pay-gold {
            background: linear-gradient(45deg, var(--gold-start), var(--gold-end));
            color: #000; border: none; padding: 5px 15px;
            font-weight: bold; font-size: 0.8rem;
            transition: 0.3s; box-shadow: 0 0 10px rgba(191, 149, 63, 0.4);
        }
        .btn-pay-gold:hover {
            transform: translateY(-2px); box-shadow: 0 0 20px rgba(191, 149, 63, 0.7); color: #fff;
        }
        
        .back-link {
            color: #888; text-decoration: none; transition: 0.3s;
        }
        .back-link:hover { color: var(--gold-mid); }
        
        /* Footer mini */
        .footer-note { margin-top: 30px; color: #666; font-size: 0.8rem; text-align: center; font-style: italic; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">ROYAL BID</a>
            <div class="ms-auto">
                <a href="index.php" class="back-link small"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại sàn đấu</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2 class="page-title">LỊCH SỬ GIAO DỊCH</h2>
        </div>

        <div class="glass-container">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Sản phẩm</th>
                            <th>Giá bạn đặt</th>
                            <th>Giá cao nhất</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, p.name, p.image, p.price as current_price, p.end_time, p.is_paid 
                                FROM bids b 
                                JOIN products p ON b.product_id = p.id 
                                WHERE b.user_id = $user_id 
                                ORDER BY b.bid_time DESC";

                        $result = mysqli_query($conn, $sql);
                        $now = date("Y-m-d H:i:s");

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Logic trạng thái
                                $my_bid = $row['bid_amount'];
                                $curr_price = $row['current_price'];
                                $end_time = $row['end_time'];
                                $is_paid = $row['is_paid'];
                                
                                $status_html = "";
                                $action_html = "";

                                // Logic hiển thị (Giống logic cũ nhưng style mới)
                                if ($my_bid < $curr_price) {
                                    $status_html = "<span class='status-badge badge-fail'>Bị vượt giá</span>";
                                    $action_html = "<span class='text-muted small'>-</span>";
                                } else {
                                    if ($now > $end_time) {
                                        if ($is_paid == 1) {
                                            $status_html = "<span class='status-badge badge-paid'>Đã thanh toán</span>";
                                            $action_html = "<span class='text-success small'><i class='fa-solid fa-check'></i> Xong</span>";
                                        } else {
                                            $status_html = "<span class='status-badge badge-won'>CHIẾN THẮNG</span>";
                                            $action_html = "<a href='my_bids.php?pay={$row['product_id']}&amount={$my_bid}' class='btn btn-pay-gold'>THANH TOÁN</a>";
                                        }
                                    } else {
                                        $status_html = "<span class='status-badge badge-lead'>Đang dẫn đầu</span>";
                                        $action_html = "<span class='text-muted small'>...</span>";
                                    }
                                }

                                // Render Row
                                echo "<tr>";
                                echo "<td>" . date("H:i d/m", strtotime($row['bid_time'])) . "</td>";
                                echo "<td>
                                        <div class='d-flex align-items-center'>
                                            <img src='{$row['image']}' class='product-thumb me-3'>
                                            <span class='fw-bold text-white'>{$row['name']}</span>
                                        </div>
                                      </td>";
                                echo "<td style='color:var(--gold-mid); font-weight:bold;'>" . number_format($my_bid) . "</td>";
                                echo "<td class='text-muted'>" . number_format($curr_price) . "</td>";
                                echo "<td>$status_html</td>";
                                echo "<td>$action_html</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Bạn chưa tham gia đấu giá nào.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer-note">
            * Lưu ý: Các vật phẩm chiến thắng cần được thanh toán trong vòng 24h để đảm bảo quyền lợi.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>