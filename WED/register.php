<?php
include 'config/db.php';;

$mess = "";

if (isset($_POST['btn_register'])) {
    $user = $_POST['username'];
    $raw_pass = $_POST['password']; // Mật khẩu gốc để điền tự động
    $pass = password_hash($raw_pass, PASSWORD_DEFAULT); // Mã hóa để lưu DB
    $name = $_POST['full_name'];
    
    // Kiểm tra trùng tên
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    if (mysqli_num_rows($check) > 0) {
        $mess = "Tên đăng nhập đã tồn tại!";
    } else {
        $sql = "INSERT INTO users (username, password, full_name, role) VALUES ('$user', '$pass', '$name', 'user')";
        if (mysqli_query($conn, $sql)) {
            // Chuyển hướng qua trang login, gửi kèm user và pass để tự điền
            header("Location: login.php?u=$user&p=$raw_pass"); 
            exit();
        } else {
            $mess = "Lỗi: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Royal Bid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Manrope:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #121212; 
            background-image: radial-gradient(circle at 50% 0%, #1a1a1a 0%, #000000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Manrope', sans-serif;
            color: #e0e0e0;
        }

        .register-card {
            background: #1a1a1a;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 450px; /* Rộng hơn một chút so với login */
            position: relative;
            overflow: hidden;
            border: 1px solid #333;
        }

        /* Thanh vàng trang trí */
        .register-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, #bf953f, #fcf6ba, #b38728);
        }

        .register-header { text-align: center; margin-bottom: 30px; }
        
        .register-header h2 {
            color: #d4af37;
            font-family: 'Playfair Display', serif;
            font-weight: bold;
            font-size: 2rem;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        
        .icon-header { font-size: 40px; color: #d4af37; margin-bottom: 10px; }

        /* Input Styles */
        .form-label { color: #aaa; font-size: 0.9rem; }
        
        .input-group-text {
            background-color: #222;
            border: 1px solid #444;
            border-right: none;
            color: #d4af37;
        }
        
        .form-control {
            background-color: #222;
            border: 1px solid #444;
            border-left: none;
            color: #fff;
            padding: 12px;
        }
        
        .form-control::placeholder { color: #555; }
        
        .form-control:focus {
            background-color: #2a2a2a;
            color: #fff;
            box-shadow: none;
            border-color: #d4af37;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: #d4af37;
            background-color: #2a2a2a;
        }

        /* Button Style */
        .btn-register {
            background: linear-gradient(45deg, #bf953f, #b38728);
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-register:hover {
            background: #d4af37;
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        /* Links */
        .link-login {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            border-top: 1px solid #333;
            padding-top: 20px;
            color: #888;
        }
        .link-login a {
            color: #d4af37;
            text-decoration: none;
            font-weight: bold;
        }
        .link-login a:hover { text-decoration: underline; }

        /* Alert Error */
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff6b6b;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="register-header">
            <i class="fa-solid fa-user-plus icon-header"></i>
            <h2>ĐĂNG KÝ</h2>
            <p class="text-secondary small">Trở thành thành viên của Royal Bid</p>
        </div>

        <?php if (!empty($mess)): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <div><?php echo $mess; ?></div>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Chọn tên đăng nhập..." required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Họ và tên</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                    <input type="text" name="full_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
                </div>
            </div>
            
            <button type="submit" name="btn_register" class="btn btn-primary w-100 btn-register">
                Tạo Tài Khoản
            </button>
        </form>

        <div class="link-login">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </div>
    </div>

</body>
</html>
