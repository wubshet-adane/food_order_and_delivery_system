<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Sample data sent from Telebirr (in a real-world scenario, you'd get this from Telebirr via HTTP POST or SMS)
$telebirr_response = $_GET; // Normally this would be $_POST for a real API, but here we use GET for simulation

$order_id = $telebirr_response['order_id'] ?? null;
$payment_status = $telebirr_response['status'] ?? null;

if ($order_id && $payment_status == "success") {
    // Update order status to 'Paid'
    $stmt = $conn->prepare("UPDATE orders SET status = 'Paid' WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Redirect to payment success page
    header("Location: /views/customer/payment_success.php?order_id=$order_id");
} else {
    // Handle failed payment
    header("Location: /views/customer/payment_failed.php?error=Payment failed or canceled");
}
