<?php

require_once __DIR__ . '/../domain/AuctionRules.php';

function handleBid(
    array $input,
    array $session,
    callable $findProduct,
    callable $saveBid,
    string $now
): array {
    if (!isset($session['user_id'])) {
        return [
            'status' => 401,
            'body' => ['success' => false, 'message' => 'Vui lòng đăng nhập để đấu giá'],
        ];
    }

    $productId = isset($input['product_id']) ? (int)$input['product_id'] : 0;
    $bidAmount = isset($input['bid_amount']) ? (int)$input['bid_amount'] : 0;
    if ($productId <= 0 || $bidAmount <= 0) {
        return [
            'status' => 400,
            'body' => ['success' => false, 'message' => 'Dữ liệu đặt giá không hợp lệ'],
        ];
    }

    $product = $findProduct($productId);
    if (!is_array($product)) {
        return [
            'status' => 404,
            'body' => ['success' => false, 'message' => 'Sản phẩm không tồn tại'],
        ];
    }

    $ruleResult = validateBid($product, $bidAmount, $now);
    if (!$ruleResult['ok']) {
        return [
            'status' => 400,
            'body' => ['success' => false, 'message' => $ruleResult['message']],
        ];
    }

    $saved = $saveBid((int)$session['user_id'], $productId, $bidAmount);
    if ($saved === false) {
        return [
            'status' => 500,
            'body' => ['success' => false, 'message' => 'Không thể lưu dữ liệu đặt giá'],
        ];
    }

    return [
        'status' => 200,
        'body' => ['success' => true, 'message' => $ruleResult['message']],
    ];
}
