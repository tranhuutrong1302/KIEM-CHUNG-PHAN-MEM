<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApiIntegrationTest extends TestCase
{
    private ?mysqli $conn = null;

    protected function setUp(): void
    {
        global $conn;
        $this->conn = $conn ?? null;
        if (!$this->conn || $this->conn->connect_errno !== 0) {
            $this->markTestSkipped('Không thể kết nối tới cơ sở dữ liệu kiểm thử.');
        }
        mysqli_begin_transaction($this->conn);
    }

    protected function tearDown(): void
    {
        if ($this->conn && $this->conn->connect_errno === 0) {
            mysqli_rollback($this->conn);
        }
    }

    public function testFetchProductsDataReturnsValidArray(): void
    {
        $products = fetchProductsData($this->conn);
        $this->assertIsArray($products);

        if (count($products) > 0) {
            $product = $products[0];
            $this->assertArrayHasKey('category_name', $product);
            $this->assertArrayHasKey('next_min', $product);
            $this->assertArrayHasKey('bid_count', $product);
            $this->assertArrayHasKey('end_time_js', $product);
        }
    }

    public function testRegisterAndLoginRequestFlow(): void
    {
        $username = 'test_user_' . uniqid();
        $email = $username . '@example.com';
        $password = 'TestPass123!';
        $fullName = 'Người dùng kiểm thử';

        $register = registerRequest($this->conn, [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName,
        ]);

        $this->assertTrue($register['success']);
        $this->assertSame('Đăng ký thành công', $register['message']);

        $session = [];
        $login = loginRequest($this->conn, [
            'username' => $username,
            'password' => $password,
        ], $session);

        $this->assertTrue($login['success']);
        $this->assertSame('Đăng nhập thành công', $login['message']);
        $this->assertArrayHasKey('user_id', $session);
        $this->assertArrayHasKey('username', $session);
        $this->assertSame($username, $session['username']);
    }

    public function testProcessBidRequestRequiresValidAmount(): void
    {
        $result = processBidRequest($this->conn, ['product_id' => 0, 'bid_amount' => 0], ['user_id' => 1]);
        $this->assertFalse($result['success']);
        $this->assertSame(400, $result['code']);
    }

    public function testProcessBidRequestWithValidProduct(): void
    {
        $productResult = mysqli_query($this->conn, "SELECT * FROM products WHERE end_time > NOW() ORDER BY id ASC LIMIT 1");
        if (!$productResult || mysqli_num_rows($productResult) === 0) {
            $this->markTestSkipped('Không tìm thấy sản phẩm chưa hết hạn để kiểm thử.');
            return;
        }

        $product = mysqli_fetch_assoc($productResult);
        $this->assertNotEmpty($product);

        $userName = 'bid_user_' . uniqid();
        $email = $userName . '@example.com';
        $passwordHash = password_hash('Password123!', PASSWORD_DEFAULT);
        $insertUser = mysqli_query($this->conn, "INSERT INTO users (username, password, full_name, email, role) VALUES ('{$userName}', '{$passwordHash}', 'Người đấu giá', '{$email}', 'user')");
        $this->assertTrue((bool)$insertUser, 'Không thể tạo người dùng kiểm thử');

        $userId = mysqli_insert_id($this->conn);
        $currentPrice = (int)$product['price'];
        $minIncrement = (int)$product['min_increment'];
        $bidAmount = $currentPrice + $minIncrement;

        $session = ['user_id' => $userId];
        $result = processBidRequest($this->conn, [
            'product_id' => (int)$product['id'],
            'bid_amount' => $bidAmount,
        ], $session);

        $this->assertTrue($result['success']);
        $this->assertSame('Đấu giá thành công', $result['message']);
        $this->assertSame(200, $result['code']);
    }
}
