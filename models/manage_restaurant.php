<?php
require_once('C:/wamp64/www/food_ordering_system/config/database.php');

class Restaurant {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all restaurants
    public function getAllRestaurants() {
        $sql = "SELECT * FROM restaurants ";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
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
