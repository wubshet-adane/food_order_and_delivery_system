<?php
// controllers/AuthController.php
session_start();
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public static function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'];

            if (User::register($name, $email, $password, $role)) {
                header("Location: ../views/auth/login.php?success=1");
            } else {
                header("Location: ../views/auth/register.php?error=1");
            }
        }
    }

    public static function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = User::login($email, $password);

            if ($user) {
                $_SESSION['user'] = $user;
                header("Location: ../public/index.php");
            } else {
                header("Location: ../views/auth/login.php?error=1");
            }
        }
    }

    public static function logout() {
        session_destroy();
        header("Location: ../views/auth/login.php");
    }
}
?>
