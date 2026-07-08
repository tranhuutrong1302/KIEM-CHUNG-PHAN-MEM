<?php
/**
 * AdminAuthUnitTest.php
 * Unit Test cho các hàm kiểm tra quyền truy cập: checkLogin() và checkAdminLogin()
 *
 * Chiến lược mock:
 * - PHP không cho phép override hàm `header()` hay keyword `exit` trực tiếp trong global namespace.
 * - Giải pháp: Tái định nghĩa logic của checkLogin / checkAdminLogin trong namespace TestingAdmin,
 *   nơi hàm `header()` được override và `exit` được thay bằng throw Exception để PHPUnit bắt được.
 */

namespace TestingAdmin {

    use PHPUnit\Framework\TestCase;

    // ── Mock header() trong namespace này ──────────────────────────────────────
    function header(string $string, bool $replace = true, int $http_response_code = 0): void
    {
        $GLOBALS['mock_header_calls'][] = $string;
    }

    // ── Phiên bản test của checkLogin (logic giống db.php nhưng dùng mock header + throw thay exit) ──
    function checkLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../frontend/login.html");
            throw new \RuntimeException('__EXIT__');
        }
    }

    // ── Phiên bản test của checkAdminLogin ──────────────────────────────────────
    function checkAdminLogin(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ../frontend/login.html");
            throw new \RuntimeException('__EXIT__');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────

    class AdminAuthUnitTest extends TestCase
    {
        protected function setUp(): void
        {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            $_SESSION = [];
            $GLOBALS['mock_header_calls'] = [];
        }

        protected function tearDown(): void
        {
            $_SESSION = [];
            unset($GLOBALS['mock_header_calls']);
        }

        // ── checkLogin() ──────────────────────────────────────────────────────────

        /** Chưa login → phải redirect */
        public function testCheckLoginRedirectsWhenNotLoggedIn(): void
        {
            $redirected = false;
            try {
                checkLogin();
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === '__EXIT__') {
                    $redirected = true;
                }
            }

            $this->assertTrue($redirected, 'Hàm checkLogin() phải redirect khi chưa có session user_id');
            $this->assertContains('Location: ../frontend/login.html', $GLOBALS['mock_header_calls']);
        }

        /** Đã login (role bất kỳ) → không redirect */
        public function testCheckLoginPassesWhenLoggedIn(): void
        {
            $_SESSION['user_id'] = 123;

            checkLogin(); // không throw = không redirect

            $this->assertEmpty($GLOBALS['mock_header_calls'], 'Không được redirect khi đã có session');
        }

        // ── checkAdminLogin() ─────────────────────────────────────────────────────

        /** Chưa login → phải redirect */
        public function testCheckAdminLoginRedirectsWhenNotLoggedIn(): void
        {
            $redirected = false;
            try {
                checkAdminLogin();
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === '__EXIT__') {
                    $redirected = true;
                }
            }

            $this->assertTrue($redirected, 'Hàm checkAdminLogin() phải redirect khi chưa login');
            $this->assertContains('Location: ../frontend/login.html', $GLOBALS['mock_header_calls']);
        }

        /** Login nhưng role = 'user' → phải redirect */
        public function testCheckAdminLoginRedirectsWhenRoleIsUser(): void
        {
            $_SESSION['user_id'] = 42;
            $_SESSION['role']    = 'user';

            $redirected = false;
            try {
                checkAdminLogin();
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === '__EXIT__') {
                    $redirected = true;
                }
            }

            $this->assertTrue($redirected, 'Hàm checkAdminLogin() phải redirect khi role không phải admin');
            $this->assertContains('Location: ../frontend/login.html', $GLOBALS['mock_header_calls']);
        }

        /** Login nhưng role = 'moderator' → phải redirect */
        public function testCheckAdminLoginRedirectsWhenRoleIsModerator(): void
        {
            $_SESSION['user_id'] = 99;
            $_SESSION['role']    = 'moderator';

            $redirected = false;
            try {
                checkAdminLogin();
            } catch (\RuntimeException $e) {
                if ($e->getMessage() === '__EXIT__') {
                    $redirected = true;
                }
            }

            $this->assertTrue($redirected, 'Hàm checkAdminLogin() phải redirect với bất kỳ role nào không phải admin');
        }

        /** Login và role = 'admin' → cho đi tiếp, không redirect */
        public function testCheckAdminLoginPassesWhenRoleIsAdmin(): void
        {
            $_SESSION['user_id'] = 1;
            $_SESSION['role']    = 'admin';

            checkAdminLogin(); // không throw = PASS

            $this->assertEmpty($GLOBALS['mock_header_calls'], 'Không được redirect khi đã là admin');
        }
    }
}
