<?php

use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    public function testReturnsCategoryWhenNotKhac(): void
    {
        $row = ['name' => 'ROLEX Datejust', 'category' => 'Đồng hồ'];
        $this->assertSame('Đồng hồ', resolveCategory($row));
    }

    public function testDetectsWatchWithUppercaseInput(): void
    {
        $row = ['name' => 'ROLEX Datejust', 'category' => 'Khác'];
        $this->assertSame('Đồng hồ', resolveCategory($row));
    }

    public function testDetectsJewelry(): void
    {
        $row = ['name' => 'Nhẫn kim cương cao cấp', 'category' => 'Khác'];
        $this->assertSame('Trang sức', resolveCategory($row));
    }

    public function testDetectsPlateNumber(): void
    {
        $row = ['name' => 'Biển số 30A cực đẹp', 'category' => 'Khác'];
        $this->assertSame('Biển số', resolveCategory($row));
    }

    public function testFallsBackToDefaultWhenNameEmpty(): void
    {
        $row = ['name' => '', 'category' => 'Khác'];
        $this->assertSame('Xe sang', resolveCategory($row));
    }
}
