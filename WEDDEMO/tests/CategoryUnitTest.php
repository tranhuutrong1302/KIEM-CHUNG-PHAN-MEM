<?php
use PHPUnit\Framework\TestCase;

// Nhúng file chứa hàm getCategory() (Giả sử bạn đã tách hàm này ra file helpers.php)
require_once __DIR__ . '/../config/helpers.php'; 

class CategoryUnitTest extends TestCase {

    /**
     * Kịch bản 1: Đoán đúng danh mục "Đồng hồ" dựa vào từ khóa
     */
    public function testGetCategoryReturnsWatch() {
        // 1. ARRANGE (Chuẩn bị dữ liệu đầu vào giả mạo)
        // Cố tình để category rỗng để ép hệ thống phải đọc tên sản phẩm
        $fakeRow = [
            'category' => '', 
            'name' => 'Đồng hồ Patek Philippe đính kim cương'
        ];

        // 2. ACT (Thực thi hàm cần kiểm thử)
        $result = getCategory($fakeRow);

        // 3. ASSERT (Khẳng định kết quả)
        // Chúng ta kỳ vọng kết quả trả ra phải là chữ 'Đồng hồ'
        $this->assertEquals('Đồng hồ', $result, "Lỗi: Hệ thống phân loại sai danh mục Đồng hồ!");
    }

    /**
     * Kịch bản 2: Đoán đúng danh mục "Trang sức"
     */
    public function testGetCategoryReturnsJewelry() {
        // 1. ARRANGE
        $fakeRow = [
            'category' => 'Khác', // Nếu user chọn 'Khác', hệ thống cũng phải tự đoán
            'name' => 'Vòng cổ ngọc trai'
        ];

        // 2. ACT
        $result = getCategory($fakeRow);

        // 3. ASSERT
        $this->assertEquals('Trang sức', $result);
    }
    
    /**
     * Kịch bản 3: Trả về danh mục mặc định nếu không có từ khóa nào khớp
     */
    public function testGetCategoryReturnsDefault() {
        // 1. ARRANGE
        $fakeRow = [
            'category' => '',
            'name' => 'Sản phẩm bí ẩn không rõ tên'
        ];

        // 2. ACT
        $result = getCategory($fakeRow);

        // 3. ASSERT (Kỳ vọng trả về 'Xe sang' vì code của bạn để nó làm mặc định ở dòng cuối)
        $this->assertEquals('Xe sang', $result);
    }
}
?>
