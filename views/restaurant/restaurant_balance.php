<?php
require 'C:\xampp\htdocs\food_order_and_delivery_system-main\config/database.php'; // Ensure you have your database connection here

// Get the balance for a specific restaurant
function getRestaurantBalance($restaurant_id) {
    global $conn;

    $sql = "SELECT SUM(oi.price * oi.quantity) AS total_sold
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.restaurant_id = ? AND o.status = 'Delivered'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['total_sold'] ?? 0;
    }

    return 0;
}

// Display balance for a specific restaurant
function displayRestaurantBalance($restaurant_id) {
    global $conn;

    $sql = "SELECT name FROM restaurants WHERE restaurant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $balance = getRestaurantBalance($restaurant_id);
        echo "<h2 style='text-align: center; color: #2c3e50;'>Balance for {$row['name']}</h2>";
        echo "<table style='width: 60%; margin: 20px auto; border-collapse: collapse; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>";
        echo "<tr style='background-color:rgb(240, 160, 12); color: white;'>";
        echo "<th style='padding: 12px; text-align: left;'>Restaurant Name</th>";
        echo "<th style='padding: 12px; text-align: left;'>Total Sold (ETB)</th>";
        echo "</tr>";
        echo "<tr style='border-bottom: 1px solid #ddd;'>";
        echo "<td style='padding: 12px;'>{$row['name']}</td>";
        echo "<td style='padding: 12px; color: #27ae60; font-weight: bold;'>" . number_format($balance, 2) . " ETB</td>";

        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p style='text-align: center; color: red;'>Restaurant not found.</p>";
    }
}

// Get restaurant_id from session or request
session_start();
if (isset($_SESSION['restaurant_id'])) {
    $restaurant_id = $_SESSION['restaurant_id'];
    displayRestaurantBalance($restaurant_id);
} else {
    $restaurant_id = '2';
    displayRestaurantBalance($restaurant_id);
     "<p style='text-align: center; color: red;'>Access Denied. Please log in as a restaurant.</pecho>";
    
}
?>
