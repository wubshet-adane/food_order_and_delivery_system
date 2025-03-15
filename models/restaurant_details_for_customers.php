<?php
require_once('C:/wamp64/www/food_ordering_system/config/database.php');

class Restaurant
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    //to get specific restaurant
    public function getOneRestaurant($resId)
    {
        $sql = "SELECT * FROM restaurants WHERE restaurant_id = ?";
        $stmt = $this->conn->prepare($sql);
        // Check if `prepare()` failed
        if (!$stmt) {
            die("SQL Prepare Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $resId);
        $stmt->execute();
        // Get the result set
        $queryResult = $stmt->get_result();
        // Fetch data correctly
        $restaurants = [];
        while ($row = $queryResult->fetch_assoc()) {
            $restaurants[] = $row;
        }
        return $restaurants; // Always return an array
    }
}
?>