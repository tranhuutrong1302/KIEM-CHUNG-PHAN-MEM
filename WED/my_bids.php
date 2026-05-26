<?php
include 'db.php';
checkLogin(); // Bắt buộc đăng nhập
$user_id = $_SESSION['user_id'];

// XỬ LÝ KHI NGƯỜI DÙNG XÁC NHẬN ĐÃ CHUYỂN KHOẢN
if (isset($_POST['confirm_pay'])) {
    $p_id = (int)$_POST['product_id'];
    $amount = $_POST['amount'];
    
    // Kiểm tra xem user này có phải người thắng không
    $check_winner = mysqli_query($conn, "SELECT price FROM products WHERE id=$p_id");
    $prod_info = mysqli_fetch_assoc($check_winner);
    
    if ($prod_info['price'] == $amount) {
        // Kiểm tra xem đã tạo đơn hàng pending trước đó chưa (tránh spam click)
        $check_order = mysqli_query($conn, "SELECT id FROM orders WHERE product_id=$p_id AND user_id=$user_id");
        if (mysqli_num_rows($check_order) == 0) {
            // TẠO ĐƠN HÀNG VỚI TRẠNG THÁI 'pending' (Chờ duyệt)
            mysqli_query($conn, "INSERT INTO orders (user_id, product_id, amount, status) VALUES ($user_id, $p_id, $amount, 'pending')");
            echo "<script>alert('Đã gửi thông báo chuyển khoản! Vui lòng chờ Admin duyệt.'); window.location='my_bids.php';</script>";
        }
    } else {
        echo "<script>alert('Lỗi: Dữ liệu không hợp lệ.'); window.location='my_bids.php';</script>";
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

        .navbar { background: rgba(0, 0, 0, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 215, 0, 0.1); }
        .navbar-brand { font-family: 'Cinzel', serif; font-weight: 700; font-size: 1.4rem; background: linear-gradient(to right, var(--gold-start), var(--gold-mid), var(--gold-end)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .page-header { text-align: center; margin: 40px 0; }
        .page-title { font-family: 'Cinzel', serif; color: var(--gold-mid); font-size: 2rem; display: inline-block; position: relative; padding-bottom: 15px; }
        .page-title::after { content: ''; position: absolute; bottom: 0; left: 25%; width: 50%; height: 2px; background: linear-gradient(90deg, transparent, var(--gold-mid), transparent); }
        .glass-container { background: var(--glass-bg); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); padding: 20px; overflow: hidden; }
        .custom-table { width: 100%; color: #ccc; vertical-align: middle; }
        .custom-table thead th { background: rgba(0,0,0,0.5); color: var(--gold-mid); font-family: 'Cinzel', serif; font-weight: normal; border-bottom: 1px solid var(--gold-start); padding: 15px; text-transform: uppercase; font-size: 0.9rem; }
        .custom-table tbody td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .custom-table tbody tr { transition: all 0.3s; }
        .custom-table tbody tr:hover { background: rgba(255, 215, 0, 0.05); }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border: 1px solid #444; border-radius: 4px; }
        
        .status-badge { padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: inline-block; }
        .badge-fail { color: #ff4757; border: 1px solid rgba(255, 71, 87, 0.3); background: rgba(255, 71, 87, 0.1); }
        .badge-lead { color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3); background: rgba(46, 213, 115, 0.1); }
        .badge-won { color: var(--gold-mid); border: 1px solid var(--gold-start); background: rgba(191, 149, 63, 0.2); box-shadow: 0 0 10px rgba(191, 149, 63, 0.2); }
        .badge-paid { color: #fff; background: #2ed573; border: 1px solid #2ed573; }
        
        /* Màu cho trạng thái Đang xử lý */
        .badge-pending { color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.3); background: rgba(243, 156, 18, 0.1); }

        .btn-pay-gold { background: linear-gradient(45deg, var(--gold-start), var(--gold-end)); color: #000; border: none; padding: 5px 15px; font-weight: bold; font-size: 0.8rem; transition: 0.3s; box-shadow: 0 0 10px rgba(191, 149, 63, 0.4); }
        .btn-pay-gold:hover { transform: translateY(-2px); box-shadow: 0 0 20px rgba(191, 149, 63, 0.7); color: #fff; }
        .back-link { color: #888; text-decoration: none; transition: 0.3s; }
        .back-link:hover { color: var(--gold-mid); }
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
                            <th>Ngày đặt</th>
                            <th>Sản phẩm</th>
                            <th>Giá bạn đặt</th>
                            <th style="color: #ff4757 !important; font-weight: bold;">Giá cao nhất</th> 
                            <th>Kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Cập nhật câu lệnh SQL: Kéo thêm cột 'status' từ bảng orders để biết đơn hàng đang ở trạng thái nào
                        $sql = "SELECT b.*, p.name, p.image, p.price as current_price, p.end_time, p.is_paid, 
                                       (SELECT status FROM orders WHERE product_id = p.id AND user_id = $user_id LIMIT 1) as order_status 
                                FROM bids b 
                                JOIN products p ON b.product_id = p.id 
                                WHERE b.user_id = $user_id 
                                ORDER BY b.bid_time DESC";

                        $result = mysqli_query($conn, $sql);
                        $now = date("Y-m-d H:i:s");

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $my_bid = $row['bid_amount'];
                                $curr_price = $row['current_price'];
                                $end_time = $row['end_time'];
                                $is_paid = $row['is_paid'];
                                $order_status = $row['order_status']; // Trạng thái: NULL, 'pending', hoặc 'paid'
                                $endTimeJs = strtotime($end_time) * 1000;
                                
                                $status_html = "";
                                $action_html = "";

                                if ($my_bid < $curr_price) {
                                    $status_html = "<span class='status-badge badge-fail'>Bị vượt giá</span>";
                                    $action_html = "<span class='text-muted small'>-</span>";
                                } else {
                                    if ($now > $end_time) {
                                        // KIỂM TRA TRẠNG THÁI THANH TOÁN MỚI
                                        if ($is_paid == 1 || $order_status == 'paid') {
                                            $status_html = "<span class='status-badge badge-paid'>Đã thanh toán</span>";
                                            $action_html = "<span class='text-success small'><i class='fa-solid fa-check'></i> Xong</span>";
                                        } elseif ($order_status == 'pending') {
                                            $status_html = "<span class='status-badge badge-pending'>Đang xử lý</span>";
                                            $action_html = "<span class='text-warning small'><i class='fa-solid fa-spinner fa-spin'></i> Chờ duyệt</span>";
                                        } else {
                                            $status_html = "<span class='status-badge badge-won'>CHIẾN THẮNG</span>";
                                            // Gọi function mở Modal thay vì điều hướng URL
                                            $action_html = "<button onclick='openBankModal({$row['product_id']}, {$my_bid}, \"".htmlspecialchars($row['name'])."\")' class='btn btn-pay-gold'>THANH TOÁN</button>";
                                        }
                                    } else {
                                        $status_html = "<span class='status-badge badge-lead'>Đang dẫn đầu</span>";
                                        $action_html = "<span class='text-muted small'>...</span>";
                                    }
                                }

                                echo "<tr>";
                                echo "<td>" . date("H:i d/m", strtotime($row['bid_time'])) . "</td>";
                                echo "<td>
                                        <div class='d-flex align-items-center'>
                                            <img src='{$row['image']}' class='product-thumb me-3'>
                                            <span class='fw-bold text-white'>{$row['name']}</span>
                                        </div>
                                      </td>";
                                echo "<td style='color:var(--gold-mid); font-weight:bold;'>" . number_format($my_bid) . " đ</td>";
                                echo "<td class='text-danger fw-bold'>" . number_format($curr_price) . " đ</td>";
                                
                                echo "<td>
                                        <span class='timer text-warning fw-bold' data-time='{$endTimeJs}' style='font-size: 0.85rem;'>
                                            <i class='fa-regular fa-hourglass-half'></i> Đang tính...
                                        </span><br>
                                        <small class='text-muted'>" . date("H:i d/m", strtotime($end_time)) . "</small>
                                      </td>";
                                      
                                echo "<td>$status_html</td>";
                                echo "<td>$action_html</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Bạn chưa tham gia đấu giá nào.</td></tr>";
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

    <div class="modal fade" id="bankModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #1a1a1a; border: 1px solid #c5a059;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" style="color: var(--gold-mid); font-family: 'Cinzel', serif;">THÔNG TIN CHUYỂN KHOẢN</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body text-light">
                        <p class="text-muted small mb-3">Vui lòng chuyển khoản đúng số tiền để hệ thống xác nhận thanh toán cho: <strong id="bProductName" class="text-white"></strong></p>
                        
                        <div class="p-3 mb-3" style="background: rgba(0,0,0,0.5); border: 1px dashed var(--gold-start); border-radius: 8px;">
                            <div class="mb-2"><span class="text-muted">Ngân hàng:</span> <strong class="text-white float-end">VIETCOMBANK</strong></div>
                            <div class="mb-2"><span class="text-muted">Chủ tài khoản:</span> <strong class="text-white float-end">NGUYEN VAN A</strong></div>
                            <div class="mb-2"><span class="text-muted">Số tài khoản:</span> <strong class="text-warning float-end fs-5">0123456789</strong></div>
                            <div class="mb-2"><span class="text-muted">Số tiền:</span> <strong id="bAmountShow" class="text-danger float-end fw-bold"></strong></div>
                            <div><span class="text-muted">Nội dung CK:</span> <strong id="bContentShow" class="text-info float-end"></strong></div>
                        </div>

                        <input type="hidden" name="product_id" id="bProductId">
                        <input type="hidden" name="amount" id="bAmount">
                        
                        <div class="alert alert-warning py-2 mb-0" style="font-size: 0.85rem; background-color: rgba(243, 156, 18, 0.1); border-color: rgba(243, 156, 18, 0.3); color: #f39c12;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Vui lòng bấm "Đã chuyển khoản" <strong>SAU KHI</strong> thực hiện giao dịch thành công. Admin sẽ kiểm tra và duyệt đơn.
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="confirm_pay" class="btn btn-pay-gold">ĐÃ CHUYỂN KHOẢN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // SCRIPT ĐẾM NGƯỢC
        setInterval(() => {
            document.querySelectorAll('.timer').forEach(el => {
                const dest = parseInt(el.dataset.time); 
                const now = new Date().getTime(); 
                const diff = dest - now; 
                
                if(diff <= 0) { 
                    el.innerHTML = "Đã kết thúc"; el.style.color = "#888"; el.classList.remove('text-warning');
                } else {
                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);
                    let timeString = `<i class="fa-regular fa-hourglass-half"></i> `;
                    if(d > 0) timeString += `${d} ngày `;
                    timeString += `${h}h ${m}p ${s}s`;
                    el.innerHTML = timeString;
                }
            });
        }, 1000);

        // SCRIPT MỞ BẢNG THANH TOÁN (MODAL)
        function openBankModal(id, amount, name) {
            document.getElementById('bProductId').value = id;
            document.getElementById('bAmount').value = amount;
            document.getElementById('bProductName').innerText = name;
            
            // Format số tiền và nội dung chuyển khoản tự động
            document.getElementById('bAmountShow').innerText = new Intl.NumberFormat('de-DE').format(amount) + ' VNĐ';
            document.getElementById('bContentShow').innerText = 'THANHTOAN SP' + id;
            
            new bootstrap.Modal(document.getElementById('bankModal')).show();
        }
    </script>
</body>
</html>