<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Order.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['secret_code'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID and Secret Code are required.']);
    exit;
}

$order_id = $data['order_id'];
$secret_code = $data['secret_code'];

if (Order::validateSecretCode($order_id, $secret_code)) {
    if (Order::updateOrderStatus($order_id, 'Delivered')) {
        echo json_encode(['success' => true, 'message' => 'Delivery Confirmed!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Secret Code!']);
}
?>
