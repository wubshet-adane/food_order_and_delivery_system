<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class User {

    public static function register($name, $email, $password, $role) {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
        return $stmt->execute();
    }

    public static function login($email, $password) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'customer' AND email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($password, $result['password'])) {
            return $result;
        } else {
            return false;
        }
    }
}
?>
