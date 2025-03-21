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

    public function updateRestaurant($restaurantId, $data) {
        // Use an UPDATE query to update the existing restaurant record
        $query = "UPDATE restaurants SET 
            name = ?, 
            image = ?, 
            banner = ?, 
            license = ?, 
            location = ?, 
            latitude = ?, 
            longitude = ?, 
            phone = ?, 
            status = ?, 
            description = ?, 
            tiktokAccount = ?, 
            telegramAccount = ?, 
            instagramAccount = ?, 
            facebook = ?, 
            website = ?, 
            opening_and_closing_hour = ?,
            updated_at = CURRENT_TIMESTAMP WHERE restaurant_id = ? AND owner_id = ?";  // Specify which record to update by restaurant_id
    
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
    
        // Check if the statement was prepared successfully
        if ($stmt === false) {
            // Show detailed error message if prepare fails
            die("Error preparing query: " . $this->conn->error);
        }
    
        // Bind the parameters to the prepared statement
        // Notice that the last parameter is the restaurant_id which identifies the record to update
        $stmt->bind_param(
            "ssssssssssssssssii",  // Added 'i' for the integer id at the end
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
            $data['opening_and_closing_hour'],
            $restaurantId,
            $data['owner_id']  // The restaurantId is needed to identify which record to update
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
