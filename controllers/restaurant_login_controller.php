
<?php
session_start();
require_once __DIR__ . '/../models/restaurant.php';
// use database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $restaurant = Restaurant::login($email, $password);

    if ($restaurant) {
        $_SESSION['user_id'] = $restaurant['user_id'];
        $_SESSION['user_email'] = $restaurant['email'];
        $_SESSION['password'] = $restaurant['password'];
        $_SESSION['loggedIn'] = true;
        $_SESSION['userType'] = "restaurant";
        header("Location: ../views/restaurant/dashboard.php?message=successfuly logged in");
    } else {
        header("Location: ../views/auth/restaurant_login.php?message=incorrect email or password!");
    }
}


