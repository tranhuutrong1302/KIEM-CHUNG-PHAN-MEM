<?php
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? $_POST['username'] ?? null;
$password = $input['password'] ?? $_POST['password'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$username || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit();
}

$username = mysqli_real_escape_string($conn, trim($username));
$result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
if (!$result || mysqli_num_rows($result) === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
    exit();
}

$user = mysqli_fetch_assoc($result);
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Mật khẩu không chính xác']);
    exit();
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role'] = $user['role'];

echo json_encode(['success' => true, 'message' => 'Đăng nhập thành công']);
