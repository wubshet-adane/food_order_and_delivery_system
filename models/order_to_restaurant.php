<?php 
require_once '../config/database.php';


class Order {
    public static function getOrdersByRestaurant($restaurant_id) {
        global $conn;
        $sql = "SELECT * FROM orders WHERE restaurant_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function updateOrderStatus($order_id, $status) {
        global $conn;
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }
}
?>