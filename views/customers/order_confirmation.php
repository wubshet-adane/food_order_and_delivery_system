<?php
// controllers/OrderController.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: /views/auth/login.php");
    exit();
}

class OrderController {

    public static function placeOrder() {
        global $conn;

        $customer_id = $_SESSION['user']['user_id'];
        $restaurant_id = $_POST['restaurant_id'];
        $menu_ids = $_POST['menu_id'];
        $quantities = $_POST['quantity'];

        // Calculate total price
        $total_price = 0;
        foreach ($menu_ids as $index => $menu_id) {
            $stmt = $conn->prepare("SELECT price FROM menu WHERE menu_id = ?");
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $total_price += $result['price'] * $quantities[$menu_id];
        }

        // Insert order into the database
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, restaurant_id, total_price, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iii", $customer_id, $restaurant_id, $total_price);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Insert order items into order_items table
        foreach ($menu_ids as $index => $menu_id) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $menu_id, $quantities[$menu_id], $result['price']);
            $stmt->execute();
        }

        // Redirect to the order confirmation page
        header("Location: /views/customer/order_confirmation.php?order_id=$order_id");
    }
}
?>
