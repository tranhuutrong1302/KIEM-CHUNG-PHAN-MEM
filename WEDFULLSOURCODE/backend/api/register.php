<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/ApiHelper.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$response = registerRequest($conn, $input);
http_response_code($response['code']);
echo json_encode(['success' => $response['success'], 'message' => $response['message']]);
