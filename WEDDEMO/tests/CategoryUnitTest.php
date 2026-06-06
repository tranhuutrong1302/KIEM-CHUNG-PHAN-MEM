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

    /**
     * Kịch bản 4: Đoán đúng danh mục "Biển số" dựa vào từ khóa
     */
    public function testGetCategoryReturnsBiểnSố() {
        $fakeRow1 = ['category' => '', 'name' => 'Biển số ngũ quý 9 cực độc'];
        $fakeRow2 = ['category' => 'Khác', 'name' => 'Xe máy biển kiểm soát 30A-99999'];

        $this->assertEquals('Biển số', getCategory($fakeRow1));
        $this->assertEquals('Biển số', getCategory($fakeRow2));
    }

    /**
     * Kịch bản 5: Đoán đúng danh mục "Bất động sản" dựa vào từ khóa
     */
    public function testGetCategoryReturnsBấtĐộngSản() {
        $fakeRow1 = ['category' => '', 'name' => 'Biệt thự nghỉ dưỡng view biển'];
        $fakeRow2 = ['category' => 'Khác', 'name' => 'Căn hộ Penthouse cao cấp'];
        $fakeRow3 = ['category' => '', 'name' => 'Lô đất nền thổ cư'];

        $this->assertEquals('Bất động sản', getCategory($fakeRow1));
        $this->assertEquals('Bất động sản', getCategory($fakeRow2));
        $this->assertEquals('Bất động sản', getCategory($fakeRow3));
    }

    /**
     * Kịch bản 6: Tính năng không phân biệt chữ hoa/chữ thường (Case-insensitivity)
     */
    public function testGetCategoryCaseInsensitive() {
        $fakeRow1 = ['category' => '', 'name' => 'NHẪN KIM CƯƠNG PNJ'];
        $fakeRow2 = ['category' => '', 'name' => 'ĐỒNG HỒ ROLEX DATEJUST'];
        $fakeRow3 = ['category' => '', 'name' => 'BIỆT THỰ VINHOMES'];

        $this->assertEquals('Trang sức', getCategory($fakeRow1));
        $this->assertEquals('Đồng hồ', getCategory($fakeRow2));
        $this->assertEquals('Bất động sản', getCategory($fakeRow3));
    }

    /**
     * Kịch bản 7: Trả về category được cung cấp sẵn nếu nó khác trống và khác "Khác"
     */
    public function testGetCategoryReturnsCategoryWhenProvided() {
        // Mặc dù tên là nhẫn kim cương (thuộc Trang sức), nhưng danh mục đi kèm là "Bất động sản" (khác trống và khác Khác)
        $fakeRow = [
            'category' => 'Bất động sản',
            'name' => 'Nhẫn kim cương cao cấp'
        ];

        $this->assertEquals('Bất động sản', getCategory($fakeRow));
    }
}
?>
