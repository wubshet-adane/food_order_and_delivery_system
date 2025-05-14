<?php
// Include the database connection file
require_once __DIR__ . '/../config/database.php';

class Customer_registration {
    public static function customerRegisterFunction($data) {
        global $conn;
    
        try {
            // Step 1: Check if email already exists
            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $data['email']);
            $checkStmt->execute();
            $resultCheck = $checkStmt->get_result();
            if ($resultCheck->num_rows > 0) {
                $checkStmt->close();
                // Return the error message
                //die($conn->error);
            }
            $checkStmt->close();

            // Step 2: Insert into `users` table
            $userStmt = $conn->prepare("INSERT INTO users (name, image, email, password, role, phone) VALUES (?, ?, ?, ?, ?, ?)");

            //$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $userStmt->bind_param("sssss",
                $data['fullname'],
                $data['profile_image'],
                $data['email'],
                $data['password'],
                $data['role'],
                $data['phone']
            );
            if($userStmt->execute()){
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }//end of function restaurant owners Registrationfunction
}//end of class
?>
