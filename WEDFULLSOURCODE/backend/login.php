<?php
include 'db.php';

$mess = "";

// Xử lý logic đăng nhập (GIỮ NGUYÊN)
if (isset($_POST['btn_login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];
            
            header("Location: /index.php");
            exit();
        } else {
            $mess = "Mật khẩu không chính xác!";
        }
    } else {
        $mess = "Tài khoản không tồn tại!";
    }
}

$auto_user = isset($_GET['u']) ? $_GET['u'] : "";
$auto_pass = isset($_GET['p']) ? $_GET['p'] : "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Royal Bid</title>
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

        .login-card {
            background: #1a1a1a;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
            border: 1px solid #333;
        }

        /* Thanh vàng trang trí bên trên */
        .login-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, #bf953f, #fcf6ba, #b38728);
        }

        .login-header { text-align: center; margin-bottom: 30px; }
        
        .login-header h2 {
            color: #d4af37; /* Vàng kim */
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
        .btn-login {
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
        .btn-login:hover {
            background: #d4af37;
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        /* Links */
        .link-register {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            border-top: 1px solid #333;
            padding-top: 20px;
            color: #888;
        }
        .link-register a {
            color: #d4af37;
            text-decoration: none;
            font-weight: bold;
        }
        .link-register a:hover { text-decoration: underline; }

        .back-home {
            color: #666; font-size: 0.85rem; text-decoration: none;
            transition: 0.3s;
        }
        .back-home:hover { color: #d4af37; }

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

    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-crown icon-header"></i>
            <h2>ĐĂNG NHẬP</h2>
            <p class="text-secondary small">Chào mừng quý khách quay trở lại</p>
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
                    <input type="text" name="username" class="form-control" 
                           value="<?php echo $auto_user; ?>" 
                           placeholder="Nhập username..." required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" 
                           value="<?php echo $auto_pass; ?>" 
                           placeholder="Nhập mật khẩu..." required>
                </div>
            </div>
            
            <button type="submit" name="btn_login" class="btn btn-primary w-100 btn-login">
                Truy Cập
            </button>
        </form>
        
        <div class="link-register">
            Chưa là thành viên? <a href="register.php">Đăng ký ngay</a>
        </div>
        
        <div class="text-center mt-3">
            <a href="index.php" class="back-home">
                <i class="fa-solid fa-arrow-left me-1"></i> Về trang chủ
            </a>
        </div>
    </div>

</body>
</html>