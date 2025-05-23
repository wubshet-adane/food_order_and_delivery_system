<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class User {

    public static function login($email, $password) {
        global $conn;

        // Select user by email only
        $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'customer' AND email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // Verify hashed password
        if ($result && password_verify($password, $result['password'])) {
            return $result;
        } else {
            return false;
        }
    }

}

?>
