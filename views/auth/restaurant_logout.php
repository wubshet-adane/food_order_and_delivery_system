<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Clear session cookies
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page with message
header("Location: restaurant_login.php?logout=success");
exit();
?>
