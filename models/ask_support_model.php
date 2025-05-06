<?php
ob_start(); // 🛡️ Output buffering starts

require_once __DIR__ . "/../config/database.php";
class Faqs {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to submit a support request
    public function submitSupportRequest($user_id, $user_type, $name, $email, $subject, $message) {
                   $stmt = $this->conn->prepare("
                                        INSERT INTO support 
                                        (asker_id, asker_role, name, email,	subject, message, created_at) 
                                        VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssss", $user_id, $user_type, $name, $email, $subject, $message);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'success_message' => 'Your support question has been submitted successfully!'
                ];
            } else {
                return [
                    'error' => true,
                    'error_message' => 'Error submitting your message. Please try again.'
                ];
            }
        }
    

    // Function to fetch all support requests for a user
    public function getSupportResponse($asker_role, $status) {
        $query = "
            SELECT u.name, s.* 
            FROM support s
            JOIN users u ON u.user_id = s.asker_id
            WHERE s.asker_role = ? AND s.status = ?
            ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $asker_role, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Function to fetch a specific support request by ID
    public function getSupportRequestById($requestId) {
        $query = "SELECT * FROM support_requests WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$requestId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>