<?php
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? $_POST['username'] ?? null;
$email = $input['email'] ?? $_POST['email'] ?? null;
$password = $input['password'] ?? $_POST['password'] ?? null;
$full_name = $input['full_name'] ?? $_POST['full_name'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$username || !$email || !$password || !$full_name) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit();
}

$username = mysqli_real_escape_string($conn, trim($username));
$email = mysqli_real_escape_string($conn, trim($email));
$full_name = mysqli_real_escape_string($conn, trim($full_name));

$check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
if ($check && mysqli_num_rows($check) > 0) {
    $row = mysqli_fetch_assoc($check);
    $message = $row['username'] === $username ? 'Tên đăng nhập đã tồn tại' : 'Email đã được sử dụng';
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, password, full_name, email, role) VALUES ('$username', '$hash', '$full_name', '$email', 'user')";
if (!mysqli_query($conn, $sql)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . mysqli_error($conn)]);
    exit();
}

echo json_encode(['success' => true, 'message' => 'Đăng ký thành công']);
