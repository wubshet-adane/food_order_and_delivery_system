<?php
session_start();
require_once __DIR__ . '/../models/admin_login.php';
// use database connection

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    
    // Check if user exists in database
    $user = Admin::login($email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['password'] = $user['password'];
        $_SESSION['profile_image'] = $user['image'];
        $_SESSION['loggedIn'] = true;
        $_SESSION['userType'] = "customer";
        $_SESSION['name'] = $user['name'];
        //set response message and redirect url
        $response["success"] = true;
        $response["message"] = "Login successful";
        $response['redirect_url'] = "../customers/menu.php?message=successfuly logged in";
        header('Location:  ../views/auth/admin_login.php?error=' . urlencode($success));
        exit;
    } else {
        header('Location:  ../views/auth/admin_login.php?error=' . urlencode($success));
        exit;
    }
}
else {
    exit;
}

