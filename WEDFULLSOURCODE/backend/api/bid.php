<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/ApiHelper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = [];
}

$response = processBidRequest($conn, $input, $_SESSION);
http_response_code($response['code']);
echo json_encode(['success' => $response['success'], 'message' => $response['message']]);
