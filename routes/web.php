<?php
// routes/web.php
require_once '../controllers/AuthController.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        AuthController::login();
        break;
    case 'register':
        AuthController::register();
        break;
    case 'logout':
        AuthController::logout();
        break;
    default:
        echo "404 Not Found";
}
?>
