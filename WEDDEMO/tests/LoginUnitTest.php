<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/auth_helpers.php';

class LoginUnitTest extends TestCase {
    
    // Test trường hợp sai Username
    public function testLoginWithNonExistentUser() {
        $userFromDb = null; // Giả lập không tìm thấy user trong DB
        $result = getLoginErrorMessage('sai_user', '123456', $userFromDb);
        $this->assertEquals("Tài khoản không tồn tại!", $result);
    }

    // Test trường hợp sai mật khẩu
    public function testLoginWithWrongPassword() {
        // Giả lập user tồn tại nhưng pass đã hash là 'abc'
        $userFromDb = ['password' => password_hash('abc', PASSWORD_DEFAULT)];
        $result = getLoginErrorMessage('admin', 'wrong_pass', $userFromDb);
        $this->assertEquals("Mật khẩu không chính xác!", $result);
    }

    // Test trường hợp đúng hết
    public function testLoginSuccess() {
        $password = 'chuan_men';
        $userFromDb = ['password' => password_hash($password, PASSWORD_DEFAULT)];
        $result = getLoginErrorMessage('admin', $password, $userFromDb);
        $this->assertEquals("", $result);
    }
}
