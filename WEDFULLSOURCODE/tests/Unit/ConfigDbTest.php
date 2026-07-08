<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ConfigDbTest extends TestCase
{
    public function testNormalizeImageUrlConvertsRelativePath(): void
    {
        $this->assertSame('/uploads/san%20ph%E1%BA%A9m.jpg', normalizeImageUrl('uploads/san phẩm.jpg'));
    }

    public function testNormalizeImageUrlKeepsAbsoluteUrl(): void
    {
        $this->assertSame('https://example.com/image.png', normalizeImageUrl('https://example.com/image.png'));
    }

    public function testNormalizeImageUrlHandlesEmptyPath(): void
    {
        $this->assertSame('', normalizeImageUrl(''));
    }

    public function testNormalizeImageUrlConvertsBackslash(): void
    {
        $this->assertSame('/uploads/image.jpg', normalizeImageUrl('uploads\\image.jpg'));
    }

    public function testNormalizeImageUrlHandlesNull(): void
    {
        $this->assertSame('', normalizeImageUrl(null));
    }

    public function testNormalizeImageUrlEncodesWhitespaceInSubdir(): void
    {
        $this->assertSame('/uploads/my%20folder/photo.png', normalizeImageUrl('uploads/my folder/photo.png'));
    }

    public function testNormalizeImageUrlWithMultipleSlashes(): void
    {
        $this->assertSame('/uploads///sub///file.jpg', normalizeImageUrl('///uploads///sub///file.jpg'));
    }
}
