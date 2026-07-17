<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/ApiHelper.php';

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
	$input = [];
	parse_str($rawInput, $input);
}

$response = loginRequest($conn, $input, $_SESSION);
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
$wantsJson = stripos($contentType, 'application/json') !== false;

if ($wantsJson) {
	header('Content-Type: application/json');
	http_response_code($response['code']);
	echo json_encode(['success' => $response['success'], 'message' => $response['message']]);
	exit();
}

if ($response['success']) {
	header('Location: ../index.php');
	exit();
}

http_response_code($response['code']);
header('Content-Type: application/json');
echo json_encode(['success' => $response['success'], 'message' => $response['message']]);
