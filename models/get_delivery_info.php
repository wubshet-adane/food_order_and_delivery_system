<?php
require_once __DIR__ . '/../config/database.php';
class Delivery_info {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fetch all restaurants
    public function getDeliveryInfo($user_Id) {
        $sql = "SELECT * FROM customer_delivery_address WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
    
        // Check if `prepare()` failed
        if (!$stmt) {
            die("SQL Prepare Error: " . $this->conn->error);
        }
    
        $stmt->bind_param("i", $user_Id);
        $stmt->execute();
    
        // Get the result set
        $queryResult = $stmt->get_result();
    
        // Fetch data correctly
        $delivery = [];
        while ($row = $queryResult->fetch_assoc()) {
            $delivery[] = $row;
        }
    
        return $delivery; // Always return an array
    }
}