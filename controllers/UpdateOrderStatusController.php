

<?php
session_start();
require_once '../models/order_to_restaurant';

$order_id = $_GET['order_id'] ?? null;
$customer_id = $_SESSION['user']['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        if (Order::updateOrderStatus($order_id, $status)) {
            header("Location: ../views/restaurant/orders.php?success=Order Updated");
        } else {
            header("Location: ../views/restaurant/orders.php?error=Failed to update order");
        }
    }
}
?>


