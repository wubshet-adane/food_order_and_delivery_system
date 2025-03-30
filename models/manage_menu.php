<?php
require_once __DIR__ . '/../config/database.php';

class Menu {
    public static function getAllItems( $res_id) {
        global $conn;
      
        // Check if request method is GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $search = $_GET['search'] ?? '';
            $sort = $_GET['sort'] ?? 'name ASC';
    
            // SQL query with placeholders
            $sql = "SELECT * FROM menu WHERE restaurant_id = ? 
                    AND (name LIKE ? OR description LIKE ? OR catagory LIKE ?) 
                    ORDER BY $sort";
    
            $stmt = $conn->prepare($sql);
    
            if (!$stmt) {
                die("SQL Prepare Error: " . $conn->error);
            }
    
            // Add wildcard % for partial search
            $search = "%$search%";
    
            $stmt->bind_param('isss', $res_id, $search, $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Check for errors
            if (!$result) {
                die("Error executing query: " . $conn->error);
            }
        
        }else{
            
            // Run the query
            $sql = "SELECT * FROM menu ORDER BY name ASC";
            $result = $conn->query($sql);
        
            // Check if the query was successful
            if ($result === false) {
                die("Error executing query: " . $conn->error); // Optional: Log error to file instead of die() in production
            }
        }
            // Return the result as an associative array
        return $result->fetch_all(MYSQLI_ASSOC);
        
    }
    
    //add new item
    public static function addItem($resId, $name, $catagory, $content, $description, $price, $image) {
        global $conn;
        $sql = "INSERT INTO menu (restaurant_id, name, catagory, content, description, price, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssds", $resId, $name, $catagory, $content, $description, $price, $image);
        return $stmt->execute();
    }

    //update item
    public static function updateItem($id, $name, $description, $catagory, $content, $price, $image) {
        global $conn;
        $sql = "UPDATE menu SET name = ?, description = ?, catagory = ?, content = ?, price = ?, image = ? WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdsi", $name, $description, $catagory, $content, $price, $image, $id);
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

    //delete item
    public static function deleteAllItem($resId) {
        global $conn;
        $sql = "DELETE FROM menu WHERE restaurant_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $resId);
        return $stmt->execute();
    }

    
}
?>
