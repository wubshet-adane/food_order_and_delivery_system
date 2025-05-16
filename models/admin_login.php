<?php
    // models/User.php
    require_once __DIR__ . '/../config/database.php';

    class Admin {
        public static function login($email, $password) {
            global $conn;

            // Fetch user by email and role
            $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'admin' AND email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            // Use password_verify to check the password
            if ($result && password_verify($password, $result['password'])) {
                return $result;
            } else {
                return false;
            }
        }
    }
?>