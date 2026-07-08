<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApiHelperTest extends TestCase
{
    public function testGetCategoryReturnsCategoryFromRow(): void
    {
        $row = ['category' => 'Trang sức', 'name' => 'Nhẫn vàng'];
        $this->assertSame('Trang sức', getCategory($row));
    }

    public function testGetCategoryDetectsDongHoFromName(): void
    {
        $row = ['category' => 'Khác', 'name' => 'Đồng hồ Rolex'];
        $this->assertSame('Đồng hồ', getCategory($row));
    }

    public function testGetCategoryDetectsBấtĐộngSảnFromName(): void
    {
        $row = ['category' => '', 'name' => 'Biệt thự biển'];
        $this->assertSame('Bất động sản', getCategory($row));
    }

    public function testBuildApiResponseIncludesCodeAndMessage(): void
    {
        $response = buildApiResponse(false, 'Lỗi dữ liệu', ['foo' => 'bar'], 422);

        $this->assertSame(false, $response['success']);
        $this->assertSame('Lỗi dữ liệu', $response['message']);
        $this->assertSame(['foo' => 'bar'], $response['data']);
        $this->assertSame(422, $response['code']);
    }

    public function testGetCategoryDetectsTrangSucFromName(): void
    {
        $row = ['category' => 'Khác', 'name' => 'Nhẫn kim cương PNJ'];
        $this->assertSame('Trang sức', getCategory($row));
    }

    public function testGetCategoryDetectsBienSoFromName(): void
    {
        $row = ['category' => '', 'name' => 'Biển số ngũ quý 30a-9999'];
        $this->assertSame('Biển số', getCategory($row));
    }

    public function testBuildApiResponseSuccessCase(): void
    {
        $response = buildApiResponse(true, 'Thành công', ['id' => 1]);
        $this->assertSame(true, $response['success']);
        $this->assertSame('Thành công', $response['message']);
        $this->assertSame(['id' => 1], $response['data']);
        $this->assertSame(200, $response['code']);
    }
}
