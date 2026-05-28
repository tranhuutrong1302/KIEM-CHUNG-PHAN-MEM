<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/auth_helpers.php';

class DepositUnitTest extends TestCase {
    
    // Test Case 1: 100.000đ, tỉ lệ 10% -> Mong đợi 10.000đ
    public function testDepositCase1() {
        $result = Tinh_Tien_Coc(100000, 10);
        $this->assertEquals(10000, $result);
    }

    // Test Case 2: 105.500đ, tỉ lệ 10% -> Mong đợi 11.000đ (Làm tròn lên)
    public function testDepositCase2() {
        $result = Tinh_Tien_Coc(105500, 10);
        $this->assertEquals(11000, $result);
    }

    // Test Case 3: Giá khởi điểm âm -50.000đ -> Mong đợi báo lỗi
    public function testDepositCase3() {
        $result = Tinh_Tien_Coc(-50000, 10);
        $this->assertEquals("Giá trị không hợp lệ", $result);
    }
}
