<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class DeliveryUser {
    public static function login($email, $password) {
        global $conn;

        // Step 1: Fetch user by email and role
        $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'delivery' AND email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // Step 2: Verify the password hash
        if ($result && password_verify($password, $result['password'])) {
            return $result;
        } else {
            return false;
        }
    }
}

?>
