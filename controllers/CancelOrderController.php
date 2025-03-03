<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: /views/auth/login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? null;
$customer_id = $_SESSION['user']['user_id'];

if ($order_id) {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ? AND customer_id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $order_id, $customer_id);
    $stmt->execute();
}

header("Location: /views/customer/order_history.php");
exit();
?>