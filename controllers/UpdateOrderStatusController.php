

<?php
session_start();
require_once '../models/order_to_restaurant';
/*
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: /views/auth/login.php");
    exit();
}
*/
$order_id = $_GET['order_id'] ?? null;
$customer_id = $_SESSION['user']['user_id'];

if ($order_id) {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ? AND customer_id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $order_id, $customer_id);
    $stmt->execute();
}

header("Location: /views/customer/order_history.php");
exit();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        if (Order::updateOrderStatus($order_id, $status)) {
            header("Location: ../../views/orders.php?success=Order Updated");
        } else {
            header("Location: ../../views/orders.php?error=Failed to update order");
        }
    }
}
?>


