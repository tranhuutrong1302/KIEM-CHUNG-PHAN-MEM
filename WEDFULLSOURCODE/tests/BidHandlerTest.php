<?php

use PHPUnit\Framework\TestCase;

class BidHandlerTest extends TestCase
{
    /**
     * Test handleBid when user is not logged in
     */
    public function testHandleBidUserNotLoggedIn(): void
    {
        $input = ['product_id' => 1, 'bid_amount' => 1000];
        $session = []; // No user_id
        $findProduct = fn($id) => ['id' => $id, 'price' => 500, 'min_increment' => 50, 'end_time' => '2026-12-31 23:59:59'];
        $saveBid = fn($userId, $productId, $bidAmount) => true;
        $now = '2026-06-12 12:00:00';

        $result = handleBid($input, $session, $findProduct, $saveBid, $now);

        $this->assertEquals(401, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Vui lòng đăng nhập để đấu giá', $result['body']['message']);
    }

    /**
     * Test handleBid when input is invalid (non-positive IDs or amounts)
     */
    public function testHandleBidInvalidInput(): void
    {
        $session = ['user_id' => 1];
        $findProduct = fn($id) => ['id' => $id, 'price' => 500, 'min_increment' => 50, 'end_time' => '2026-12-31 23:59:59'];
        $saveBid = fn($userId, $productId, $bidAmount) => true;
        $now = '2026-06-12 12:00:00';

        // Test product_id <= 0
        $input = ['product_id' => 0, 'bid_amount' => 1000];
        $result = handleBid($input, $session, $findProduct, $saveBid, $now);
        $this->assertEquals(400, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Dữ liệu đặt giá không hợp lệ', $result['body']['message']);

        // Test bid_amount <= 0
        $input = ['product_id' => 1, 'bid_amount' => 0];
        $result = handleBid($input, $session, $findProduct, $saveBid, $now);
        $this->assertEquals(400, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Dữ liệu đặt giá không hợp lệ', $result['body']['message']);

        // Test negative values
        $input = ['product_id' => -1, 'bid_amount' => -100];
        $result = handleBid($input, $session, $findProduct, $saveBid, $now);
        $this->assertEquals(400, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Dữ liệu đặt giá không hợp lệ', $result['body']['message']);
    }

    /**
     * Test handleBid when product is not found
     */
    public function testHandleBidProductNotFound(): void
    {
        $session = ['user_id' => 1];
        $findProduct = fn($id) => null; // Simulate product not found
        $saveBid = fn($userId, $productId, $bidAmount) => true;
        $now = '2026-06-12 12:00:00';

        $input = ['product_id' => 999, 'bid_amount' => 1000];

        $result = handleBid($input, $session, $findProduct, $saveBid, $now);

        $this->assertEquals(404, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Sản phẩm không tồn tại', $result['body']['message']);
    }

    /**
     * Test handleBid when bid rule fails (auction ended)
     */
    public function testHandleBidRuleFailsAuctionEnded(): void
    {
        $session = ['user_id' => 1];
        $findProduct = fn($id) => [
            'id' => $id,
            'price' => 500,
            'min_increment' => 50,
            'end_time' => '2026-06-01 00:00:00' // Already ended
        ];
        $saveBid = fn($userId, $productId, $bidAmount) => true;
        $now = '2026-06-12 12:00:00'; // Current time is after end_time

        $input = ['product_id' => 1, 'bid_amount' => 1000];

        $result = handleBid($input, $session, $findProduct, $saveBid, $now);

        $this->assertEquals(400, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Phiên đấu giá đã kết thúc', $result['body']['message']);
    }

    /**
     * Test handleBid when bid rule fails (bid too low)
     */
    public function testHandleBidRuleFailsBidTooLow(): void
    {
        $session = ['user_id' => 1];
        $findProduct = fn($id) => [
            'id' => $id,
            'price' => 1000,
            'min_increment' => 100,
            'end_time' => '2026-12-31 23:59:59'
        ];
        $saveBid = fn($userId, $productId, $bidAmount) => true;
        $now = '2026-06-12 12:00:00';

        $input = ['product_id' => 1, 'bid_amount' => 1050]; // minRequired = 1000+100=1100, so 1050 < 1100

        $result = handleBid($input, $session, $findProduct, $saveBid, $now);

        $this->assertEquals(400, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Giá đấu phải cao hơn mức hiện tại + bước giá', $result['body']['message']);
    }

    /**
     * Test handleBid when bid is accepted and saved successfully
     */
    public function testHandleBidAccepted(): void
    {
        $session = ['user_id' => 1];
        $findProduct = fn($id) => [
            'id' => $id,
            'price' => 1000,
            'min_increment' => 100,
            'end_time' => '2026-12-31 23:59:59'
        ];
        $saveBid = fn($userId, $productId, $bidAmount) => true; // Simulate successful save
        $now = '2026-06-12 12:00:00';

        $input = ['product_id' => 1, 'bid_amount' => 1200]; // >= 1100

        $result = handleBid($input, $session, $findProduct, $saveBid, $now);

        $this->assertEquals(200, $result['status']);
        $this->assertTrue($result['body']['success']);
        $this->assertEquals('Đấu giá thành công', $result['body']['message']);
    }

    /**
     * Test handleBid when saveBid fails (e.g., database error)
     */
    public function testHandleBidSaveFails(): void
    {
        $session = ['user_id' => 1];
        $findProduct = fn($id) => [
            'id' => $id,
            'price' => 1000,
            'min_increment' => 100,
            'end_time' => '2026-12-31 23:59:59'
        ];
        $saveBid = fn($userId, $productId, $bidAmount) => false; // Simulate save failure
        $now = '2026-06-12 12:00:00';

        $input = ['product_id' => 1, 'bid_amount' => 1200];

        $result = handleBid($input, $session, $findProduct, $saveBid, $now);

        $this->assertEquals(500, $result['status']);
        $this->assertFalse($result['body']['success']);
        $this->assertEquals('Không thể lưu dữ liệu đặt giá', $result['body']['message']);
    }
}
