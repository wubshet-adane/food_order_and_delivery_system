<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: /views/auth/login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header("Location: /views/customer/order_history.php");
    exit();
}

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Telebirr payment URL (this is a simulated flow, you should replace with actual API)
$telebirr_payment_url = "https://paymentgateway.telebirr.et/confirm?order_id=" . $order['order_id'] . "&amount=" . $order['total_price'];

// Redirecting to Telebirr payment page (simulated)
header("Location: $telebirr_payment_url");
exit();
?>
