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

    // Test trường hợp mật khẩu để trống
    public function testLoginWithEmptyPassword() {
        $userFromDb = ['password' => password_hash('password123', PASSWORD_DEFAULT)];
        $result = getLoginErrorMessage('admin', '', $userFromDb);
        $this->assertEquals("Mật khẩu không chính xác!", $result);
    }

    // Test trường hợp tài khoản để trống (không có trong DB)
    public function testLoginWithEmptyUsername() {
        $userFromDb = null;
        $result = getLoginErrorMessage('', 'some_password', $userFromDb);
        $this->assertEquals("Tài khoản không tồn tại!", $result);
    }

    // Test trường hợp đầu vào chứa ký tự đặc biệt (giả lập SQL Injection)
    public function testLoginWithSqlInjectionInput() {
        $userFromDb = null; // Kịch bản xấu: không tìm thấy user do đã được xử lý an toàn
        $result = getLoginErrorMessage("' OR '1'='1", "some_pass", $userFromDb);
        $this->assertEquals("Tài khoản không tồn tại!", $result);
    }
}
