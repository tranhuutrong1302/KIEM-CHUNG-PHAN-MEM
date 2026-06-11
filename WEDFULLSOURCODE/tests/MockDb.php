<?php
// Mock database connection for Unit Testing
if (!function_exists('mysqli_connect')) {
    function mysqli_connect(...$args) { return new stdClass(); }
    function mysqli_connect_error() { return "Mock Error"; }
    function mysqli_query($conn, $sql) { return true; }
    function mysqli_fetch_assoc($result) { return []; }
    function mysqli_real_escape_string($conn, $str) { return addslashes($str); }
}

$conn = new stdClass();
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!function_exists('normalizeImageUrl')) {
    function normalizeImageUrl($path) {
        if (empty($path)) return '';
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        return '/uploads/' . basename($path);
    }
}

if (!function_exists('checkLogin')) {
    function checkLogin() { return true; }
}
