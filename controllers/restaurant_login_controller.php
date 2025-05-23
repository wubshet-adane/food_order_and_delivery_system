
<?php
session_start();
require_once __DIR__ . '/../models/restaurant.php';

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
    
    $restaurant = Restaurant::login($email, $password);

    if ($restaurant) {
        $_SESSION['user_id'] = $restaurant['user_id'];
        $_SESSION['user_email'] = $restaurant['email'];
        $_SESSION['password'] = $restaurant['password'];
        $_SESSION['loggedIn'] = true;
        $_SESSION['userType'] = "restaurant";
        $_SESSION['profile_image'] = $restaurant['image'];
        $_SESSION['name'] = $restaurant['name'];
        $_SESSION['status'] = $restaurant['status'];

        //set response message and redirect url
        $response["success"] = true;
        $response["message"] = "Login successful";
        $response['redirect_url'] = "../restaurant/dashboard.php?message=successfuly logged in";
        echo json_encode($response);
        exit();
    } else {
        $response["message"] = "Incorrect email or passwor4d!";
        echo json_encode($response);
        exit();
    }
    }
    else {
    echo json_encode($response);
    exit();
    }
?>
