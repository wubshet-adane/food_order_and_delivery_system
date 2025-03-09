
<?php
require_once '../models/order_to_restaurant.php';

session_start();
if (!isset($_SESSION['user']['user_id']) || !filter_var($_SESSION['user']['user_id'], FILTER_VALIDATE_INT)) {
    header("Location: ../views/auth/restaurant_login.php?error=Unauthorized access");
    exit;
}
$customer_id = $_SESSION['user']['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!$order_id || !$status) {
        header("Location: ../views/restaurant/orders.php?error=Invalid input");
        exit;
    }

    if (Order::updateOrderStatus($order_id, $status)) {
        header("Location: ../views/restaurant/orders.php?success=Order Updated");
    } else {
        header("Location: ../views/restaurant/orders.php?error=Failed to update order");
    }
    exit;
}
?>
