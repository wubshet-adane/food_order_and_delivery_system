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

// Display balance for all restaurants
function displayRestaurantBalances() {
    global $conn;

    $sql = "SELECT restaurant_id, name FROM restaurants";
    $result = $conn->query($sql);

    echo "<h2 style='text-align: center; color: #2c3e50;'>Restaurant Balances</h2>";
    echo "<table style='width: 80%; margin: 20px auto; border-collapse: collapse; box-shadow: 0 4px 8px rgba(59, 55, 51, 0.93);'>";
    echo "<tr style='background-color:rgb(243, 103, 9); color: white;'>";
    echo "<th style='padding: 12px; text-align: left;'>Restaurant Name</th>";
    echo "<th style='padding: 12px; text-align: left;'>Total Sold (ETB)</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        $balance = getRestaurantBalance($row['restaurant_id']);
        echo "<tr style='border-bottom: 1px solid #ddd;'>";
        echo "<td style='padding: 12px;'>{$row['name']}</td>";
        echo "<td style='padding: 12px; color:rgb(13, 231, 49); font-weight: bold;'>" . number_format($balance, 2) . " ETB</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Call the function to display balances
displayRestaurantBalances();
?>
