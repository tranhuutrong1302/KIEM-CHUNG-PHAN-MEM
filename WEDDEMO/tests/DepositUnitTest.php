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

    // Test Case 4: Giá khởi điểm bằng 0đ -> Mong đợi tiền cọc bằng 0đ
    public function testDepositCaseZeroStartPrice() {
        $result = Tinh_Tien_Coc(0, 10);
        $this->assertEquals(0, $result);
    }

    // Test Case 5: Tỷ lệ cọc bằng 0% -> Mong đợi tiền cọc bằng 0đ
    public function testDepositCaseZeroRate() {
        $result = Tinh_Tien_Coc(100000, 0);
        $this->assertEquals(0, $result);
    }

    // Test Case 6: Kết quả tính ra số tròn nghìn chẵn -> Không cần làm tròn lên
    public function testDepositCaseExactThousand() {
        $result = Tinh_Tien_Coc(100000, 10); // 10% của 100.000 = 10.000 (tròn nghìn)
        $this->assertEquals(10000, $result);
    }

    // Test Case 7: Kết quả tính ra chỉ dư 1 đồng lẻ -> Vẫn phải làm tròn lên hàng nghìn tiếp theo
    public function testDepositCaseOneDollarRoundUp() {
        $result = Tinh_Tien_Coc(10010, 10); // 10% của 10.010 = 1001 -> Phải làm tròn lên thành 2000
        $this->assertEquals(2000, $result);
    }
}
