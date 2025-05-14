<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class User {

    public static function login($email, $password) {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'customer' AND email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && $result['password']) {
            return $result;
        } else {
            return false;
        }
    }

}
?>
