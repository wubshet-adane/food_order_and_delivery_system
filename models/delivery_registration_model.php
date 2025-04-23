<?php
// Include the database connection file
require_once __DIR__ . '/../config/database.php';

class DeliverRegister {
    public static function deliveryRegistrationfunction($data) {
        global $conn;
    
        // Begin transaction
        $conn->begin_transaction();
        try {
            // step 1 check email exist or not
            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $data['email']);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $checkStmt->close();
                $conn->close();
                $result = "1";
                return $result;
            }
            $checkStmt->close();
            
            // Step 2: Insert into `users` table
            $userStmt = $conn->prepare("INSERT INTO users (name, image, email, password, role) VALUES (?, ?, ?, ?, ?)");
            
            // Hash password before storing
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
            $userStmt->bind_param("sssss",
                $data['fullname'],
                $data['profile_image'],
                $data['email'],
                $hashedPassword,
                $data['role']
            );
        
            // Get inserted user_id
            $user_id = $conn->insert_id;
            $userStmt->close();
        
            // Step 3: Insert into `delivery_partners` table
            $partnerStmt = $conn->prepare("INSERT INTO delivery_partners (
                user_id, dob, phone, address,
                vehicle_type, license_number, plate_number,
                id_proof, address_proof, license_copy,
                bank_name, account_name, account_number, status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )");
        
            $partnerStmt->bind_param("isssssssssssss",
                $user_id,
                $data['dob'],
                $data['phone'],
                $data['address'],
                $data['vehicle_type'],
                $data['license_number'],
                $data['plate_number'],
                $data['id_proof'],
                $data['address_proof'],
                $data['license_copy'],
                $data['bank_name'],
                $data['account_name'],
                $data['account_number'],
                $data['status']
            );
        
            $result = $partnerStmt->execute();
        
            $partnerStmt->close();
            $conn->close();
        
            return $result;

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            return false;
        } finally {
            // Commit transaction if no errors occurred
            $conn->commit();
        }
    }
}
?>
