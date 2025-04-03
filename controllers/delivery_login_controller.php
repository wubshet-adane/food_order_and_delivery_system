<?php
session_start();
require_once __DIR__ . '/../models/delivery.php';
// use database connection

$response = ["success" => false, "message" => "Invalid request", "redirect_url" => null];

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    // Validate email and password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Invalid email format!";
        echo json_encode($response);
        exit();
    }

    if (strlen($password) < 8) {
        $response["message"] = "Password must be at least 8 characters long!";
        echo json_encode($response);
        exit();
    }
    // Check if user exists in database
    $user = DeliveryUser::login($email, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['password'] = $user['password'];
        $_SESSION['loggedIn'] = true;
        $_SESSION['userType'] = "delivery";
        //set response message and redirect url
        $response["success"] = true;
        $response["message"] = "Login successful";
        $response['redirect_url'] = "../delivery/index.php";
        echo json_encode($response);
        exit();
    } else {
        $response["message"] = "Incorrect email or password!";
        echo json_encode($response);
        exit();
    }
}
else {
    echo json_encode($response);
    exit();
}
