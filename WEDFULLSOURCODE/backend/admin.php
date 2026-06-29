<?php
include 'db.php';

checkAdminLogin(); // Bắt buộc đăng nhập với quyền admin

// 1. Xử lý XÓA SẢN PHẨM
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    mysqli_query($conn, "DELETE FROM bids WHERE product_id=$id");
    echo "<script>alert('Đã xóa thành công!'); window.location='admin.php';</script>";
}

// 2. Xử lý XÓA USER (Thêm mới)
if (isset($_GET['del_user'])) {
    $uid = $_GET['del_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$uid");
    echo "<script>alert('Đã xóa thành viên!'); window.location='admin.php';</script>";
}

// 3. Xử lý THÊM SẢN PHẨM
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $cat  = $_POST['category'];
    $price = str_replace([',', '.'], '', $_POST['price']);
    $inc   = str_replace([',', '.'], '', $_POST['min_increment']);
    $end_time = $_POST['end_time'];

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $originalName = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
    $filename = time() . '_' . $safeName . ($ext ? '.' . $ext : '');
    $imagePath = 'uploads/' . $filename;
    $targetFile = $uploadDir . $filename;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            echo "<script>alert('Lỗi khi lưu ảnh. Vui lòng thử lại.'); window.location='admin.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Vui lòng chọn một ảnh hợp lệ.'); window.location='admin.php';</script>";
        exit();
    }

    $sql = "INSERT INTO products (name, category, image, price, min_increment, end_time) 
            VALUES ('$name', '$cat', '$imagePath', '$price', '$inc', '$end_time')";
    mysqli_query($conn, $sql);
    echo "<script>alert('Đã thêm vật phẩm!'); window.location='admin.php';</script>";
}

// ==========================================
// 4. XỬ LÝ DUYỆT THANH TOÁN (TÍNH NĂNG MỚI)
// ==========================================
if (isset($_GET['approve_order'])) {
    $order_id = (int)$_GET['approve_order'];
    $query = mysqli_query($conn, "SELECT product_id FROM orders WHERE id=$order_id AND status='pending'");
    if ($row = mysqli_fetch_assoc($query)) {
        $p_id = $row['product_id'];
        // Cập nhật trạng thái đơn hàng thành 'paid'
        mysqli_query($conn, "UPDATE orders SET status='paid' WHERE id=$order_id");
        // Cập nhật sản phẩm thành đã thanh toán
        mysqli_query($conn, "UPDATE products SET is_paid=1 WHERE id=$p_id");
        echo "<script>alert('Đã duyệt thanh toán thành công!'); window.location='admin.php';</script>";
    }
}

// 5. XỬ LÝ TỪ CHỐI THANH TOÁN (TÍNH NĂNG MỚI)
if (isset($_GET['cancel_order'])) {
    $order_id = (int)$_GET['cancel_order'];
    // Xóa đơn hàng đang pending để user có thể gửi lại yêu cầu thanh toán
    mysqli_query($conn, "DELETE FROM orders WHERE id=$order_id AND status='pending'");
    echo "<script>alert('Đã từ chối đơn hàng!'); window.location='admin.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Royal Bid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #121212; color: #e0e0e0; font-family: 'Manrope', sans-serif; }
        
        /* Card Style */
        .admin-card {
            background: #1a1a1a; border: 1px solid #333; border-radius: 8px;
            padding: 25px; margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .card-header-custom {
            border-bottom: 1px solid #333; padding-bottom: 15px; margin-bottom: 20px;
            color: #d4af37; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;
            display: flex; justify-content: space-between; align-items: center;
        }

        /* Form Input */
        .form-label { color: #aaa; font-size: 0.9rem; margin-bottom: 5px; }
        .form-control, .form-select {
            background-color: #222; border: 1px solid #444; color: #fff; padding: 10px;
        }
        .form-control:focus, .form-select:focus {
            background-color: #2a2a2a; border-color: #d4af37; color: #fff; box-shadow: none;
        }

        /* Table */
        .table-dark-custom { background-color: #1a1a1a; color: #ccc; font-size: 0.9rem; }
        .table-dark-custom th { color: #d4af37; border-bottom: 1px solid #444; font-weight: 600; }
        .table-dark-custom td { border-bottom: 1px solid #333; vertical-align: middle; }
        
        .btn-gold {
            background: linear-gradient(45deg, #bf953f, #b38728); color: #000; font-weight: bold;
            border: none; padding: 12px; width: 100%; text-transform: uppercase;
        }
        .btn-gold:hover { background: #d4af37; color: #000; }

        .thumb-img { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #444; }
        
        /* Badge Status */
        .badge-paid { background: #198754; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.75rem; }
        .badge-pending { background: #fd7e14; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.75rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark border-bottom border-secondary mb-4 py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php"><i class="fa-solid fa-crown me-2"></i>ROYAL ADMIN</a>
            <div class="d-flex gap-3">
                <span class="text-light align-self-center">Xin chào, Admin</span>
                <a href="index.php" class="btn btn-outline-light btn-sm">Về trang chủ</a>
                <a href="logout.php" class="btn btn-sm text-secondary">Thoát</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            
            <div class="col-lg-4">
                <div class="admin-card">
                    <h5 class="card-header-custom"><i class="fa-solid fa-plus-circle"></i> Đăng Tài Sản Mới</h5>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tên tài sản</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select name="category" class="form-select">
                                <option value="Xe sang">Xe sang</option>
                                <option value="Bất động sản">Bất động sản</option>
                                <option value="Trang sức">Trang sức</option>
                                <option value="Đồng hồ">Đồng hồ</option>
                                <option value="Biển số">Biển số</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-warning">Giá khởi điểm</label>
                            <input type="text" name="price" class="form-control text-warning fw-bold" onkeyup="formatCurrency(this)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bước giá</label>
                            <input type="text" name="min_increment" class="form-control" onkeyup="formatCurrency(this)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kết thúc lúc</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-gold">ĐĂNG NGAY</button>
                    </form>
                </div>

                <div class="admin-card">
                    <h5 class="card-header-custom"><i class="fa-solid fa-users"></i> Danh Sách Thành Viên</h5>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            <?php
                            $users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                            while($u = mysqli_fetch_assoc($users)){
                                echo "<li class='list-group-item bg-transparent text-light d-flex justify-content-between align-items-center border-bottom border-secondary'>
                                        <div>
                                            <strong>{$u['username']}</strong> <br>
                                            <small class='text-muted'>{$u['full_name']}</small> <br>
                                            <small class='text-info'><i class='fa-solid fa-envelope me-1'></i> {$u['email']}</small>
                                        </div>
                                        <a href='admin.php?del_user={$u['id']}' onclick='return confirm(\"Xóa user này?\")' class='text-danger'><i class='fa-solid fa-trash'></i></a>
                                      </li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                
                <div class="admin-card" style="border-color: #f39c12;">
                    <h5 class="card-header-custom" style="color: #f39c12; border-bottom-color: #553a11;">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Yêu Cầu Thanh Toán Đang Chờ
                    </h5>
                    <div class="alert alert-dark text-warning border-warning mb-4">
                        <strong>Thông tin chuyển khoản:</strong> Ngân hàng VIETCOMBANK | Chủ TK: NGUYEN VAN A | STK: 0123456789<br>
                        <strong>Nội dung CK:</strong> CK_SP[ID sản phẩm]_UID[ID người dùng] để admin đối soát nhanh.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Người mua</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-danger">Số tiền</th>
                                    <th>Nội dung CK</th>
                                    <th>Thời gian gửi</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_orders = "SELECT o.id, o.amount, o.created_at, o.product_id, o.user_id, u.username, u.full_name, p.name 
                                               FROM orders o 
                                               JOIN users u ON o.user_id = u.id 
                                               JOIN products p ON o.product_id = p.id 
                                               WHERE o.status = 'pending' 
                                               ORDER BY o.created_at ASC";
                                $result_orders = mysqli_query($conn, $sql_orders);

                                if (mysqli_num_rows($result_orders) > 0) {
                                    while ($order = mysqli_fetch_assoc($result_orders)) {
                                        echo "<tr>";
                                        echo "<td>#{$order['id']}</td>";
                                        echo "<td>
                                                <span class='text-warning fw-bold'>{$order['username']}</span><br>
                                                <small class='text-muted'>{$order['full_name']}</small>
                                              </td>";
                                        echo "<td><div style='max-width: 150px;' class='text-truncate' title='{$order['name']}'>{$order['name']}</div></td>";
                                        echo "<td class='text-danger fw-bold'>" . number_format($order['amount']) . " đ</td>";
                                        echo "<td><small class='text-white'>CK_SP{$order['product_id']}_UID{$order['user_id']}</small></td>";
                                        echo "<td><small class='text-muted'>" . date("d/m H:i", strtotime($order['created_at'])) . "</small></td>";
                                        echo "<td>
                                                <a href='admin.php?approve_order={$order['id']}' class='btn btn-success btn-sm me-1' onclick='return confirm(\"Xác nhận bạn ĐÃ NHẬN ĐƯỢC TIỀN từ tài khoản này?\")' title='Duyệt'><i class='fa-solid fa-check'></i></a>
                                                <a href='admin.php?cancel_order={$order['id']}' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Từ chối đơn hàng này?\")' title='Từ chối'><i class='fa-solid fa-xmark'></i></a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-muted py-4'>Hiện không có đơn hàng nào chờ duyệt.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-card">
                    <h5 class="card-header-custom"><i class="fa-solid fa-gavel"></i> Tài Sản Đang Đấu Giá</h5>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình</th>
                                    <th>Tên & Giá</th>
                                    <th>Kết thúc</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                                if(mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $cat = isset($row['category']) ? $row['category'] : 'Khác';
                                        ?>
                                        <tr>
                                            <td>#<?php echo $row['id']; ?></td>
                                            <td><img src="<?php echo normalizeImageUrl($row['image']); ?>" class="thumb-img"></td>
                                            <td>
                                                <strong><?php echo $row['name']; ?></strong><br>
                                                <span class="text-warning small"><?php echo number_format($row['price']); ?> đ</span>
                                                <span class="badge bg-secondary ms-1" style="font-size:0.6rem"><?php echo $cat; ?></span>
                                            </td>
                                            <td><?php echo date("d/m H:i", strtotime($row['end_time'])); ?></td>
                                            <td>
                                                <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Xóa?')"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else { echo "<tr><td colspan='5' class='text-center'>Trống</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-card">
                    <h5 class="card-header-custom text-success"><i class="fa-solid fa-trophy"></i> Lịch Sử Thắng Cuộc</h5>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Người thắng</th>
                                    <th>Giá chốt</th>
                                    <th>Ngày xong</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query phức tạp: Lấy sản phẩm đã hết giờ + Người trả giá cao nhất
                                $sql_win = "SELECT p.name as pname, p.image, p.price, p.end_time, p.is_paid, u.full_name, u.username
                                            FROM products p
                                            JOIN bids b ON p.id = b.product_id
                                            JOIN users u ON b.user_id = u.id
                                            WHERE p.end_time < NOW()
                                            AND b.bid_amount = (SELECT MAX(bid_amount) FROM bids WHERE product_id = p.id)
                                            ORDER BY p.end_time DESC";
                                
                                $res_win = mysqli_query($conn, $sql_win);
                                if(mysqli_num_rows($res_win) > 0) {
                                    while($w = mysqli_fetch_assoc($res_win)) {
                                        $status = ($w['is_paid'] == 1) 
                                            ? "<span class='badge-paid'>Đã thanh toán</span>" 
                                            : "<span class='badge-pending'>Chờ thanh toán</span>";
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo normalizeImageUrl($w['image']); ?>" class="thumb-img me-2">
                                                    <span class="text-truncate" style="max-width: 150px;"><?php echo $w['pname']; ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo $w['full_name']; ?><br>
                                                <small class="text-muted">@<?php echo $w['username']; ?></small>
                                            </td>
                                            <td class="text-warning fw-bold"><?php echo number_format($w['price']); ?></td>
                                            <td><?php echo date("d/m", strtotime($w['end_time'])); ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else { echo "<tr><td colspan='5' class='text-center text-muted'>Chưa có giao dịch thành công</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) input.value = new Intl.NumberFormat('de-DE').format(value);
            else input.value = '';
        }
    </script>

</body>
</html>