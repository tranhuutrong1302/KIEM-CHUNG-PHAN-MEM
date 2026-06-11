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
     * Test logic phân loại sản phẩm - trường hợp cạnh
     */
    public function testResolveCategoryEdgeCases(): void
    {
        // Category already set (not 'Khác') should return as is
        $row = ['name' => 'Random product', 'category' => 'Đồng hồ'];
        $this->assertEquals('Đồng hồ', resolveCategory($row));

        // Empty name and category
        $row = ['name' => '', 'category' => 'Khác'];
        $this->assertEquals('Xe sang', resolveCategory($row));

        // Name with multiple keywords - should match first priority? Actually, order matters in the function
        // The function checks in order: category, then biển/30a, then nhẫn..., then đồng hồ..., then biệt thự..., then default
        // So if name has both 'biển' and 'nhẫn', it will match 'biển' first -> Biển số
        $row = ['name' => 'Nhẫn kim cương với biển số', 'category' => 'Khác'];
        $this->assertEquals('Biển số', resolveCategory($row)); // Matches 'biển' before 'nhẫn'

        // Case insensitive
        $row = ['name' => 'ROLEX DATEJUST', 'category' => 'Khác'];
        $this->assertEquals('Đồng hồ', resolveCategory($row));

        // Special characters
        $row = ['name' => 'Nhẫn & Vòng tay kim cương', 'category' => 'Khác'];
        $this->assertEquals('Trang sức', resolveCategory($row));
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

        // Case 3: Giá đấuactly bằng mức tối thiểu (should accept)
        $result = validateBid($product, 1050000, $now);
        $this->assertTrue($result['ok']);
        $this->assertEquals('BID_ACCEPTED', $result['code']);

        // Case 4: Phiên đấu giá đã kết thúc
        $expiredNow = '2027-01-01 00:00:00';
        $result = validateBid($product, 2000000, $expiredNow);
        $this->assertFalse($result['ok']);
        $this->assertEquals('AUCTION_ENDED', $result['code']);

        // Case 5: Phiên đấu giá kết thúc giây này (now == end_time)
        $product['end_time'] = $now;
        $result = validateBid($product, 1100000, $now);
        $this->assertTrue($result['ok']); // Should accept if not strictly greater? Check code: $now > (string)$product['end_time']
        // If now == end_time, condition is false, so it proceeds to check bid amount
        $this->assertEquals('BID_ACCEPTED', $result['code']);

        // Case 6: Giá âm
        $result = validateBid($product, -1000, $now);
        $this->assertFalse($result['ok']);
        $this->assertEquals('BID_TOO_LOW', $result['code']);

        // Case 7: Gia tri lon hon nhieu
        $result = validateBid($product, 5000000, $now);
        $this->assertTrue($result['ok']);
    }

    /**
     * Test logic xác nhận thanh toán
     */
    public function testEvaluateConfirmPaymentLogic(): void
    {
        // Case 1: Số tiền khớp và chưa có order
        $result = evaluateConfirmPayment(5000000, 5000000, false);
        $this->assertTrue($result['ok']);

        // Case 2: Số tiền không khớp (nhỏ hơn)
        $result = evaluateConfirmPayment(5000000, 4999999, false);
        $this->assertFalse($result['ok']);
        $this->assertEquals('INVALID_AMOUNT', $result['code']);

        // Case 3: Số tiền không khớp (lớn hơn)
        $result = evaluateConfirmPayment(5000000, 5000001, false);
        $this->assertFalse($result['ok']);
        $this->assertEquals('INVALID_AMOUNT', $result['code']);

        // Case 4: Đã có order (đã xác nhận rồi)
        $result = evaluateConfirmPayment(5000000, 5000000, true);
        $this->assertFalse($result['ok']);
        $this->assertEquals('ORDER_EXISTS', $result['code']);

        // Case 5: Số tiền dưới dạng string
        $result = evaluateConfirmPayment('5000000', '5000000', false);
        $this->assertTrue($result['ok']);

        // Case 6: Một chuỗi, một số
        $result = evaluateConfirmPayment(5000000, '5000000', false);
        $this->assertTrue($result['ok']);
    }
}
