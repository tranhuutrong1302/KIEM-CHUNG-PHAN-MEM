<?php

use PHPUnit\Framework\TestCase;

class BidHandlerUnitTest extends TestCase
{
    public function testRejectsWhenUserNotLoggedIn(): void
    {
        $result = handleBid(
            ['product_id' => 10, 'bid_amount' => 2000000],
            [],
            fn () => null,
            fn () => true,
            '2026-06-01 10:00:00'
        );

        $this->assertSame(401, $result['status']);
        $this->assertFalse($result['body']['success']);
    }

    public function testRejectsWhenInputInvalid(): void
    {
        $result = handleBid(
            ['product_id' => 0, 'bid_amount' => 0],
            ['user_id' => 1],
            fn () => null,
            fn () => true,
            '2026-06-01 10:00:00'
        );

        $this->assertSame(400, $result['status']);
        $this->assertSame('Dữ liệu đặt giá không hợp lệ', $result['body']['message']);
    }

    public function testRejectsWhenProductNotFound(): void
    {
        $result = handleBid(
            ['product_id' => 10, 'bid_amount' => 2000000],
            ['user_id' => 1],
            fn () => null,
            fn () => true,
            '2026-06-01 10:00:00'
        );

        $this->assertSame(404, $result['status']);
        $this->assertSame('Sản phẩm không tồn tại', $result['body']['message']);
    }

    public function testRejectsWhenRuleFails(): void
    {
        $product = [
            'price' => 1000000,
            'min_increment' => 100000,
            'end_time' => '2026-05-01 10:00:00',
        ];

        $result = handleBid(
            ['product_id' => 10, 'bid_amount' => 2000000],
            ['user_id' => 1],
            fn () => $product,
            fn () => true,
            '2026-06-01 10:00:00'
        );

        $this->assertSame(400, $result['status']);
        $this->assertSame('Phiên đấu giá đã kết thúc', $result['body']['message']);
    }

    public function testAcceptsWhenRulePassesAndSaved(): void
    {
        $product = [
            'price' => 1000000,
            'min_increment' => 100000,
            'end_time' => '2026-06-30 10:00:00',
        ];

        $savedArgs = [];
        $result = handleBid(
            ['product_id' => 10, 'bid_amount' => 1200000],
            ['user_id' => 1],
            fn () => $product,
            function (int $userId, int $productId, int $bidAmount) use (&$savedArgs): bool {
                $savedArgs = [$userId, $productId, $bidAmount];
                return true;
            },
            '2026-06-01 10:00:00'
        );

        $this->assertSame(200, $result['status']);
        $this->assertTrue($result['body']['success']);
        $this->assertSame('Đấu giá thành công', $result['body']['message']);
        $this->assertSame([1, 10, 1200000], $savedArgs);
    }
}
