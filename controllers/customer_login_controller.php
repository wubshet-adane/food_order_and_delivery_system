<?php
session_start();
require_once __DIR__ . '/../models/customer.php';
// use database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::login($email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['loggedIn'] = true;
        header("Location: ../views/customers/menu.php?message=successfuly logged in");
    } else {
        header("Location: ../views/auth/customer_login.php?message=incorrect email or password!");
    }
}


