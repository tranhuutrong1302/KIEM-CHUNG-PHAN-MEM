<?php

use PHPUnit\Framework\TestCase;

class BidRuleUnitTest extends TestCase
{
    public function testRejectsWhenAuctionEnded(): void
    {
        $product = [
            'price' => 1000000,
            'min_increment' => 100000,
            'end_time' => '2026-05-01 10:00:00',
        ];

        $result = validateBid($product, 1200000, '2026-05-01 10:00:01');

        $this->assertFalse($result['ok']);
        $this->assertSame('Phiên đấu giá đã kết thúc', $result['message']);
    }

    public function testRejectsWhenBidTooLow(): void
    {
        $product = [
            'price' => 1000000,
            'min_increment' => 100000,
            'end_time' => '2026-06-30 10:00:00',
        ];

        $result = validateBid($product, 1099999, '2026-06-01 10:00:00');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('Giá đấu phải cao hơn', $result['message']);
        $this->assertStringContainsString('1,100,000', $result['message']);
    }

    public function testAcceptsWhenBidMeetsMinRequired(): void
    {
        $product = [
            'price' => 1000000,
            'min_increment' => 100000,
            'end_time' => '2026-06-30 10:00:00',
        ];

        $result = validateBid($product, 1100000, '2026-06-01 10:00:00');

        $this->assertTrue($result['ok']);
        $this->assertSame('Đấu giá thành công', $result['message']);
    }
}
