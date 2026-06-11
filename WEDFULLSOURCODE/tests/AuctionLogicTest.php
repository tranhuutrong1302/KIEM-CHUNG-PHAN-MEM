<?php

use PHPUnit\Framework\TestCase;

class AuctionLogicTest extends TestCase
{
    /**
     * Test logic phân loại sản phẩm dựa trên tên
     */
    public function testResolveCategoryLogic(): void
    {
        // Case 1: Nhận diện đồng hồ
        $row = ['name' => 'Rolex Datejust 36', 'category' => 'Khác'];
        $this->assertEquals('Đồng hồ', resolveCategory($row));

        // Case 2: Nhận diện trang sức
        $row = ['name' => 'Nhẫn kim cương 18k', 'category' => 'Khác'];
        $this->assertEquals('Trang sức', resolveCategory($row));

        // Case 3: Nhận diện bất động sản
        $row = ['name' => 'Biệt thự Vinhomes Riverside', 'category' => 'Khác'];
        $this->assertEquals('Bất động sản', resolveCategory($row));

        // Case 4: Mặc định là xe sang
        $row = ['name' => 'Lamborghini Aventador', 'category' => 'Khác'];
        $this->assertEquals('Xe sang', resolveCategory($row));
    }

    /**
     * Test logic kiểm tra tính hợp lệ của giá đấu
     */
    public function testValidateBidLogic(): void
    {
        $product = [
            'price' => 1000000,          // Giá hiện tại 1tr
            'min_increment' => 50000,    // Bước giá 50k
            'end_time' => '2026-12-31 23:59:59'
        ];
        $now = '2026-06-12 12:00:00';

        // Case 1: Đấu giá hợp lệ (1.1tr > 1.05tr)
        $result = validateBid($product, 1100000, $now);
        $this->assertTrue($result['ok']);
        $this->assertEquals('BID_ACCEPTED', $result['code']);

        // Case 2: Giá đấu quá thấp (1.02tr < 1.05tr)
        $result = validateBid($product, 1020000, $now);
        $this->assertFalse($result['ok']);
        $this->assertEquals('BID_TOO_LOW', $result['code']);

        // Case 3: Phiên đấu giá đã kết thúc
        $expiredNow = '2027-01-01 00:00:00';
        $result = validateBid($product, 2000000, $expiredNow);
        $this->assertFalse($result['ok']);
        $this->assertEquals('AUCTION_ENDED', $result['code']);
    }

    /**
     * Test logic xác nhận thanh toán
     */
    public function testEvaluateConfirmPaymentLogic(): void
    {
        // Case 1: Số tiền khớp và chưa có order
        $result = evaluateConfirmPayment(5000000, 5000000, false);
        $this->assertTrue($result['ok']);

        // Case 2: Số tiền không khớp
        $result = evaluateConfirmPayment(5000000, 4999999, false);
        $this->assertFalse($result['ok']);
        $this->assertEquals('INVALID_AMOUNT', $result['code']);

        // Case 3: Đã có order (đã xác nhận rồi)
        $result = evaluateConfirmPayment(5000000, 5000000, true);
        $this->assertFalse($result['ok']);
        $this->assertEquals('ORDER_EXISTS', $result['code']);
    }
}
