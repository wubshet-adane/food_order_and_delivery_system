<?php
class Order {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn; // mysqli connection
    }

    public function getUserOrders($userId) {
        $sql = "
            SELECT o.*, SUM(p.amount + p.delivery_person_fee + p.service_fee) AS total_amount, r.name as restaurant_name, COUNT(oi.order_item_id) as item_count, 
                   u.name as delivery_person
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            JOIN payments p ON o.order_id = p.order_id
            LEFT JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            LEFT JOIN order_items oi ON o.order_id = oi.order_item_id
            LEFT JOIN delivery_partners d ON o.delivery_person_id = d.id
            WHERE o.customer_id = ?
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
        ";

        if(!$sql)die($this->conn->error);
        $stmt = $this->conn->prepare($sql);
        if(!$stmt)die($this->conn->error);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

   public function getUserOrderStats($userId) {
    $stats = [];

    // Total orders
    $stmt = $this->conn->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE customer_id = ?");
    if (!$stmt) die($this->conn->error);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    // Total spent
    $stmt = $this->conn->prepare("
        SELECT SUM(p.amount) as total_spent 
        FROM orders o
        JOIN payments p ON p.order_id = o.order_id
        WHERE o.customer_id = ?");
    if (!$stmt) die($this->conn->error);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $spent = $result->fetch_assoc();
    $stats = array_merge($stats, $spent);

    // Favorite restaurant
    $stmt = $this->conn->prepare("
        SELECT r.name, COUNT(*) as order_count 
        FROM orders o
        JOIN restaurants r ON o.restaurant_id = r.restaurant_id
        WHERE o.customer_id = ?
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
        WHERE customer_id = ?
        GROUP BY month
        ORDER BY month
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthlyData = [];
    while ($row = $result->fetch_assoc()) {
        $monthlyData[$row['month']] = (int) $row['count'];
    }
    $stats['monthly_data'] = $monthlyData;

    // Weekly data (e.g., 2025-W18)
    $stmt = $this->conn->prepare("
        SELECT DATE_FORMAT(order_date, '%x-W%v') as week, COUNT(*) as count
        FROM orders
        WHERE customer_id = ?
        GROUP BY week
        ORDER BY week
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $weeklyData = [];
    while ($row = $result->fetch_assoc()) {
        $weeklyData[$row['week']] = (int) $row['count'];
    }
    $stats['weekly_data'] = $weeklyData;

    // Daily data (e.g., 2025-05-01)
    $stmt = $this->conn->prepare("
        SELECT DATE(order_date) as day, COUNT(*) as count
        FROM orders
        WHERE customer_id = ?
        GROUP BY day
        ORDER BY day
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $dailyData = [];
    while ($row = $result->fetch_assoc()) {
        $dailyData[$row['day']] = (int) $row['count'];
    }
    $stats['daily_data'] = $dailyData;

    return $stats;
}


}
