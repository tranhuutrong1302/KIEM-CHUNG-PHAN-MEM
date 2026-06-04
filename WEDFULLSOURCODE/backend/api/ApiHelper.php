<?php

declare(strict_types=1);

function getCategory(array $row): string
{
    if (!empty($row['category']) && $row['category'] !== 'Khác') {
        return $row['category'];
    }

    $name = mb_strtolower($row['name'] ?? '');
    if (strpos($name, 'biệt thự') !== false || strpos($name, 'penthouse') !== false || strpos($name, 'đất') !== false) {
        return 'Bất động sản';
    }
    if (strpos($name, 'biển') !== false || strpos($name, '30a') !== false) {
        return 'Biển số';
    }
    if (strpos($name, 'nhẫn') !== false || strpos($name, 'dây') !== false || strpos($name, 'kim cương') !== false || strpos($name, 'đá quý') !== false || strpos($name, 'vòng') !== false) {
        return 'Trang sức';
    }
    if (strpos($name, 'đồng hồ') !== false || strpos($name, 'patek') !== false || strpos($name, 'richard') !== false || strpos($name, 'rolex') !== false) {
        return 'Đồng hồ';
    }
    return 'Xe sang';
}

function fetchProductsData(mysqli $conn): array
{
    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY end_time DESC");
    if (!$result) {
        return [];
    }

    $now = date('Y-m-d H:i:s');
    $products = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $row['is_expired'] = ($now > $row['end_time']);
        $row['next_min'] = $row['price'] + $row['min_increment'];
        $row['category_name'] = getCategory($row);
        $countQuery = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM bids WHERE product_id=" . (int)$row['id']));
        $row['bid_count'] = $countQuery ? (int)$countQuery['t'] : 0;
        $row['end_time_js'] = strtotime($row['end_time']) * 1000;
        $products[] = $row;
    }

    return $products;
}

function buildApiResponse(bool $success, string $message, array $data = [], int $code = 200): array
{
    return [
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'code' => $code,
    ];
}

function processBidRequest(mysqli $conn, array $input, array &$session): array
{
    $productId = isset($input['product_id']) ? (int)$input['product_id'] : 0;
    $bidAmount = isset($input['bid_amount']) ? (int)$input['bid_amount'] : 0;

    if (!$productId || !$bidAmount) {
        return buildApiResponse(false, 'Dữ liệu đặt giá không hợp lệ', [], 400);
    }

    if (empty($session['user_id'])) {
        return buildApiResponse(false, 'Vui lòng đăng nhập để đấu giá', [], 401);
    }

    $productResult = mysqli_query($conn, "SELECT * FROM products WHERE id={$productId}");
    if (!$productResult || mysqli_num_rows($productResult) === 0) {
        return buildApiResponse(false, 'Sản phẩm không tồn tại', [], 404);
    }

    $product = mysqli_fetch_assoc($productResult);
    $now = date('Y-m-d H:i:s');
    if ($now > $product['end_time']) {
        return buildApiResponse(false, 'Phiên đấu giá đã kết thúc', [], 400);
    }

    $minRequired = $product['price'] + $product['min_increment'];
    if ($bidAmount < $minRequired) {
        return buildApiResponse(false, 'Giá đấu phải cao hơn mức hiện tại + bước giá', [], 400);
    }

    mysqli_query($conn, "UPDATE products SET price={$bidAmount} WHERE id={$productId}");
    mysqli_query($conn, "INSERT INTO bids (user_id, product_id, bid_amount) VALUES ({$session['user_id']}, {$productId}, {$bidAmount})");

    return buildApiResponse(true, 'Đấu giá thành công', [], 200);
}

function loginRequest(mysqli $conn, array $input, array &$session): array
{
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (!$username || !$password) {
        return buildApiResponse(false, 'Yêu cầu không hợp lệ', [], 400);
    }

    $usernameEscaped = mysqli_real_escape_string($conn, $username);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='{$usernameEscaped}'");
    if (!$result || mysqli_num_rows($result) === 0) {
        return buildApiResponse(false, 'Tài khoản không tồn tại', [], 401);
    }

    $user = mysqli_fetch_assoc($result);
    if (!password_verify($password, $user['password'])) {
        return buildApiResponse(false, 'Mật khẩu không chính xác', [], 401);
    }

    $session['user_id'] = $user['id'];
    $session['username'] = $user['username'];
    $session['full_name'] = $user['full_name'];
    $session['role'] = $user['role'];

    return buildApiResponse(true, 'Đăng nhập thành công', [], 200);
}

function registerRequest(mysqli $conn, array $input): array
{
    $username = trim($input['username'] ?? '');
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $fullName = trim($input['full_name'] ?? '');

    if (!$username || !$email || !$password || !$fullName) {
        return buildApiResponse(false, 'Vui lòng điền đầy đủ thông tin', [], 400);
    }

    $usernameEscaped = mysqli_real_escape_string($conn, $username);
    $emailEscaped = mysqli_real_escape_string($conn, $email);
    $fullNameEscaped = mysqli_real_escape_string($conn, $fullName);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='{$usernameEscaped}' OR email='{$emailEscaped}'");
    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $message = ($row['username'] === $username) ? 'Tên đăng nhập đã tồn tại' : 'Email đã được sử dụng';
        return buildApiResponse(false, $message, [], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, full_name, email, role) VALUES ('{$usernameEscaped}', '{$hash}', '{$fullNameEscaped}', '{$emailEscaped}', 'user')";

    if (!mysqli_query($conn, $sql)) {
        return buildApiResponse(false, 'Lỗi hệ thống: ' . mysqli_error($conn), [], 500);
    }

    return buildApiResponse(true, 'Đăng ký thành công', [], 200);
}
