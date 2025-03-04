<?php
header('Content-Type: application/json'); // Important for JSON responses

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['rating'])) {
    $rating = intval($data['rating']);

    // Validate rating value (assuming it's 1 to 5)
    if ($rating < 1 || $rating > 5) {
        echo json_encode(["success" => false, "message" => "Invalid rating value."]);
        exit;
    }

    $conn = new mysqli("localhost", "root", "", "food_ordering_system");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Database connection failed."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO ratings (rating) VALUES (?)");
    $stmt->bind_param("i", $rating);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Rating saved successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error saving rating."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No rating received."]);
}
?>
