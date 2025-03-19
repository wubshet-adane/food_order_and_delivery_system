<?php
require_once __DIR__ . '/../config/database.php';
class Restaurant {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fetch all restaurants
    public function getAllRestaurants($ownerId) {
        $sql = "SELECT * FROM restaurants WHERE owner_id = ?";
        $stmt = $this->conn->prepare($sql);
    
        // Check if `prepare()` failed
        if (!$stmt) {
            die("SQL Prepare Error: " . $this->conn->error);
        }
    
        $stmt->bind_param("i", $ownerId);
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
    
    //to get specific restaurant
    public function getOneRestaurant($ownerId, $resId) {
        $sql = "SELECT * FROM restaurants WHERE owner_id = ? AND restaurant_id = ?";
        $stmt = $this->conn->prepare($sql);
        // Check if `prepare()` failed
        if (!$stmt) {
            die("SQL Prepare Error: " . $this->conn->error);
        }
        $stmt->bind_param("ii", $ownerId,$resId);
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
    

    // Add a new restaurant
    public function addRestaurant($name, $location, $contact) {
        $sql = "INSERT INTO restaurants (name, location, contact) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $name, $location, $contact);
        return $stmt->execute();
    }

    // Update restaurant details
    public function updateRestaurant($id, $name, $location, $contact, $status) {
        $sql = "UPDATE restaurants SET name=?, location=?, contact=?, status=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $location, $contact, $status, $id);
        return $stmt->execute();
    }

    // Delete a restaurant
    public function deleteRestaurant($id) {
        $sql = "DELETE FROM restaurants WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
