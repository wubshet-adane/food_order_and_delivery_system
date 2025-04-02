<?php
session_start();
require '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['quantity'];

    if ($new_quantity <= 0) {
        echo json_encode(["status" => "error", "message" => "Quantity must be greater than zero"]);
        exit();
    }

    $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_quantity, $cart_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Quantity updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update"]);
    }
    $stmt->close();
}
?>
