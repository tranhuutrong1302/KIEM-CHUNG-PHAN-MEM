<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/ApiHelper.php';
header('Content-Type: application/json');

$products = fetchProductsData($conn);
echo json_encode($products);
