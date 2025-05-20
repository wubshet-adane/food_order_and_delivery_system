<?php
class Order {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn; // mysqli connection
    }

    public function getUserOrders($userId) {
        $sql = "
            SELECT o.*, r.name as restaurant_name, COUNT(oi.order_item_id) as item_count, 
                   u.name as delivery_person
            FROM orders o
            LEFT JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            LEFT JOIN order_items oi ON o.order_id = oi.order_item_id
            LEFT JOIN delivery_persons d ON o.delivery_person_id = d.id
            WHERE o.customer_id = ?
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
        ";

        if(!$sql)die($this->conn->error);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserOrderStats($userId) {
        $stats = [];

        // Total orders
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();

        // Total spent
        $stmt = $this->conn->prepare("SELECT SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $spent = $result->fetch_assoc();
        $stats = array_merge($stats, $spent);

        // Favorite restaurant
        $stmt = $this->conn->prepare("
            SELECT r.name, COUNT(*) as order_count 
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.user_id = ?
            GROUP BY o.restaurant_id
            ORDER BY order_count DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $favRestaurant = $result->fetch_assoc();
        $stats['favorite_restaurant'] = $favRestaurant['name'] ?? null;

        // Monthly data
        $stmt = $this->conn->prepare("
            SELECT DATE_FORMAT(order_date, '%Y-%m') as month, COUNT(*) as count
            FROM orders
            WHERE user_id = ?
            GROUP BY month
            ORDER BY month
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $monthlyData = [];
        while ($row = $result->fetch_assoc()) {
            $monthlyData[$row['month']] = $row['count'];
        }

        $stats['monthly_data'] = $monthlyData;

        return $stats;
    }
}
