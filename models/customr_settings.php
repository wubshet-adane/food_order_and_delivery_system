<?php
class User {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getUserById($id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id); // i = integer
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUser($id, $data) {
        $stmt = $this->mysqli->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $data['name'], $data['email'], $data['phone'], $data['address'], $id);
        return $stmt->execute();
    }

    public function changePassword($id, $currentPassword, $newPassword) {
        $user = $this->getUserById($id);
        if (password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $id);
            return $stmt->execute();
        }
        return false;
    }
}
?>
