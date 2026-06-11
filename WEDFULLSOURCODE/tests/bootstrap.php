<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', '1');

/**
 * Mock mysqli functions to avoid real database connection during testing.
 */
if (!function_exists('mysqli_connect')) {
    function mysqli_connect(...$args) {
        return new stdClass();
    }
    function mysqli_connect_error() {
        return "Mock Error";
    }
    function mysqli_query($conn, $sql) {
        return true;
    }
    function mysqli_fetch_assoc($result) {
        return [];
    }
    function mysqli_real_escape_string($conn, $str) {
        return addslashes($str);
    }
}

/**
 * Define the real normalizeImageUrl function (copy from db.php) to ensure we have the correct version.
 * We'll define it only if it doesn't already exist (to avoid conflicts if db.php was loaded).
 */
if (!function_exists('normalizeImageUrl')) {
    function normalizeImageUrl($path) {
        if (empty($path)) {
            return '';
        }
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        $path = str_replace('\\\\', '/', $path);
        $trimmed = ltrim($path, '/');
        $parts = explode('/', $trimmed);
        $encodedParts = array_map('rawurlencode', $parts);
        return '/' . implode('/', $encodedParts);
    }
}

/**
 * Override checkLogin to always return true for testing.
 */
if (!function_exists('checkLogin')) {
    function checkLogin() {
        return true;
    }
}

/**
 * Load MockDb.php for any other mocks it provides (e.g., maybe other functions).
 * It will not override normalizeImageUrl or checkLogin if they already exist.
 */
if (file_exists(__DIR__ . '/MockDb.php')) {
    require_once __DIR__ . '/MockDb.php';
}

/**
 * Load vendor autoload.
 */
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

/**
 * Load business logic.
 */
require_once __DIR__ . '/../backend/domain/AuctionRules.php';
require_once __DIR__ . '/../backend/api/ApiHelper.php';
if (file_exists(__DIR__ . '/../backend/api/BidHandler.php')) {
    require_once __DIR__ . '/../backend/api/BidHandler.php';
}