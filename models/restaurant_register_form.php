<?php
require_once __DIR__ . '/../config/database.php';

class Restaurant {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function registerRestaurant($data) {
        $query = "INSERT INTO restaurants (
            owner_id, 
            name, 
            image, 
            banner, 
            license, 
            location, 
            latitude, 
            longitude, 
            phone, 
            status, 
            description, 
            tiktokAccount, 
            telegramAccount, 
            instagramAccount, 
            facebook, 
            website, 
            opening_and_closing_hour
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        
        // Check if the statement was prepared successfully
        if ($stmt === false) {
            // Show detailed error message if prepare fails
            die("Error preparing query: " . $this->conn->error);
        }

        // Bind the parameters
        $stmt->bind_param(
            "issssssssssssssss",
            $data['owner_id'],
            $data['name'],
            $data['image'],
            $data['banner'],
            $data['license'],
            $data['location'],
            $data['latitude'],
            $data['longitude'],
            $data['phone'],
            $data['status'],
            $data['description'],
            $data['tiktok'],
            $data['telegram'],
            $data['instagram'],
            $data['facebook'],
            $data['website'],
            $data['opening_and_closing_hour']
        );

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            return true;
        } else {
            // Show detailed error message if execute fails
            die("Error executing query: " . $stmt->error);
        }
    }

    public function updateRestaurant($restaurantId, $data){
        $query = "INSERT INTO restaurants (
            owner_id, 
            name, 
            image, 
            banner, 
            license, 
            location, 
            latitude, 
            longitude, 
            phone, 
            status, 
            description, 
            tiktokAccount, 
            telegramAccount, 
            instagramAccount, 
            facebook, 
            website, 
            opening_and_closing_hour
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        
        // Check if the statement was prepared successfully
        if ($stmt === false) {
            // Show detailed error message if prepare fails
            die("Error preparing query: " . $this->conn->error);
        }

        // Bind the parameters
        $stmt->bind_param(
            "issssssssssssssss",
            $data['owner_id'],
            $data['name'],
            $data['image'],
            $data['banner'],
            $data['license'],
            $data['location'],
            $data['latitude'],
            $data['longitude'],
            $data['phone'],
            $data['status'],
            $data['description'],
            $data['tiktok'],
            $data['telegram'],
            $data['instagram'],
            $data['facebook'],
            $data['website'],
            $data['opening_and_closing_hour']
        );

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            return true;
        } else {
            // Show detailed error message if execute fails
            die("Error executing query: " . $stmt->error);
        }
        
    }
}
?>
