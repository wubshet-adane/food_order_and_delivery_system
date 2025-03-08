<?php
$host = 'localhost';
$db = 'food_ordering_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}



class Menu {
    public static function getAllItems() {
        global $conn;
        
        // Run the query
        $sql = "SELECT * FROM menu ORDER BY name aSC";
        $result = $conn->query($sql);
    
        // Check if the query was successful
        if ($result === false) {
            die("Error executing query: " . $conn->error); // Optional: Log error to file instead of die() in production
        }
    
        // Return the result as an associative array
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    //add new item
    public static function addItem($name, $description, $price, $image) {
        global $conn;
        $sql = "INSERT INTO menu (name, description, price, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $name, $description, $price, $image);
        return $stmt->execute();
    }

    //update item
    public static function updateItem($id, $name, $description, $price, $image) {
        global $conn;
        $sql = "UPDATE menu SET name = ?, description = ?, price = ?, image = ? WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $description, $price, $image, $id);
        return $stmt->execute();
    }

    //delete item
    public static function deleteItem($id) {
        global $conn;
        $sql = "DELETE FROM menu WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
