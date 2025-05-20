<?php
session_start();
include "../../config/database.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Please log in to add items to your cart."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$menu_id = $_POST['menu_id'];
$quantity = $_POST['quantity'] ?? 1; // Default quantity to 1 if not provided
$discount = $_POST['discount'] ?? 0; // Default discount to 0 if not provided

if (!$menu_id) {
    echo json_encode(["message" => "Menu ID is missing!"]);
    exit();
}

// Check if item already exists in the cart
$checkQuery = "SELECT * FROM cart WHERE user_id = ? AND menu_id = ?";
$stmt = $conn->prepare($checkQuery);
// // Check if the prepare statement failed
// if ($stmt === false) {
//     die('MySQL prepare error: ' . $conn->error); // Die and display MySQL error
// }
$stmt->bind_param("ii", $user_id, $menu_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    // Update quantity if item exists
    $updateQuery = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND menu_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iii", $quantity, $user_id, $menu_id);
} else {
    // Insert new item
    $insertQuery = "INSERT INTO cart (user_id, menu_id, quantity, discount) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiii", $user_id, $menu_id, $quantity, $discount);
}

if ($stmt->execute()) {
    echo json_encode(["message" => "Item added to cart successfully!"]);
} else {
    echo json_encode(["message" => "Failed to add item to cart."]);
}

$stmt->close();
$conn->close();
?>
