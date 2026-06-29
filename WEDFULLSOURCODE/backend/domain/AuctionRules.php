<?php

function resolveCategory(array $row): string
{
    $category = $row['category'] ?? '';
    if ($category !== '' && $category !== 'Khác') {
        return $category;
    }

    $name = mb_strtolower((string)($row['name'] ?? ''), 'UTF-8');

    if (strpos($name, 'biển') !== false || strpos($name, '30a') !== false) {
        return 'Biển số';
    }

    if (
        strpos($name, 'nhẫn') !== false
        || strpos($name, 'dây') !== false
        || strpos($name, 'kim cương') !== false
        || strpos($name, 'đá quý') !== false
        || strpos($name, 'vòng') !== false
    ) {
        return 'Trang sức';
    }

    if (
        strpos($name, 'đồng hồ') !== false
        || strpos($name, 'patek') !== false
        || strpos($name, 'richard') !== false
        || strpos($name, 'rolex') !== false
    ) {
        return 'Đồng hồ';
    }

    if (
        strpos($name, 'biệt thự') !== false
        || strpos($name, 'penthouse') !== false
        || strpos($name, 'đất') !== false
    ) {
        return 'Bất động sản';
    }

    return 'Xe sang';
}

function validateBid(?array $product, int $bidAmount, string $now): array
{
    if ($product === null) {
        return [
            'ok' => false,
            'code' => 'PRODUCT_NOT_FOUND',
            'message' => 'Sản phẩm không tồn tại',
            'min_required' => null,
        ];
    }

    if ($now > (string)$product['end_time']) {
        return [
            'ok' => false,
            'code' => 'AUCTION_ENDED',
            'message' => 'Phiên đấu giá đã kết thúc',
            'min_required' => null,
        ];
    }

    $minRequired = (int)$product['price'] + (int)$product['min_increment'];
    if ($bidAmount < $minRequired) {
        return [
            'ok' => false,
            'code' => 'BID_TOO_LOW',
            'message' => 'Giá đấu phải cao hơn mức hiện tại + bước giá (tối thiểu là ' . number_format($minRequired) . ' đ)',
            'min_required' => $minRequired,
        ];
    }

    return [
        'ok' => true,
        'code' => 'BID_ACCEPTED',
        'message' => 'Đấu giá thành công',
        'min_required' => $minRequired,
    ];
}

function evaluateConfirmPayment(int|string $productPrice, int|string $submittedAmount, bool $orderExists): array
{
    if ((int)$productPrice !== (int)$submittedAmount) {
        return [
            'ok' => false,
            'code' => 'INVALID_AMOUNT',
            'message' => 'Lỗi: Dữ liệu không hợp lệ.',
        ];
    }

    if ($orderExists) {
        return [
            'ok' => false,
            'code' => 'ORDER_EXISTS',
            'message' => '',
        ];
    }

    return [
        'ok' => true,
        'code' => 'CONFIRM_PAYMENT_ACCEPTED',
        'message' => 'Đã gửi thông báo chuyển khoản! Vui lòng chờ Admin duyệt.',
    ];
}
