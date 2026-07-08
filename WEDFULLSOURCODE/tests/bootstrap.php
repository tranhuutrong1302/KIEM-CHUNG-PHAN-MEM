<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!defined('PHPUNIT')) {
    define('PHPUNIT', 1);
}

$envHost = getenv('TEST_DB_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$envUser = getenv('TEST_DB_USER') ?: getenv('DB_USER') ?: 'root';
$envPass = getenv('TEST_DB_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$envName = getenv('TEST_DB_NAME') ?: getenv('DB_NAME') ?: 'dau_gia';

putenv("DB_HOST={$envHost}");
putenv("DB_USER={$envUser}");
putenv("DB_PASSWORD={$envPass}");
putenv("DB_NAME={$envName}");

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

try {
    $conn = mysqli_connect($envHost, $envUser, $envPass, $envName) ?: null;
} catch (Throwable $e) {
    $conn = null;
}

if (file_exists(__DIR__ . '/../backend/config/db.php')) {
    require_once __DIR__ . '/../backend/config/db.php';
}

require_once __DIR__ . '/../backend/api/ApiHelper.php';

if (file_exists(__DIR__ . '/../backend/domain/AuctionRules.php')) {
    require_once __DIR__ . '/../backend/domain/AuctionRules.php';
}

if (file_exists(__DIR__ . '/../backend/api/BidHandler.php')) {
    require_once __DIR__ . '/../backend/api/BidHandler.php';
}

function skipIfNoDatabase(?mysqli $conn): void
{
    if (!$conn || $conn->connect_errno !== 0) {
        throw new \PHPUnit\Framework\SkippedTestError('Không thể kết nối tới cơ sở dữ liệu kiểm thử: ' . ($conn ? $conn->connect_error : ''));
    }
}
