<?php
// Include the database connection file
require_once __DIR__ . '/../config/database.php';

class DeliverRegister {
    public static function deliveryRegistrationfunction($data) {
        global $conn;
    
        $conn->begin_transaction();
        try {
            // Step 1: Check if email already exists
            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $data['email']);
            $checkStmt->execute();
            $resultCheck = $checkStmt->get_result();
            if ($resultCheck->num_rows > 0) {
                $checkStmt->close();
                $conn->rollback();
                $error = "Email already exists";
                // Return the error message
                return $error;
            }
            $checkStmt->close();

            // Step 2: Insert into `users` table
            $userStmt = $conn->prepare("INSERT INTO users (name, image, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            $userStmt->bind_param("sssss",
                $data['fullname'],
                $data['profile_image'],
                $data['email'],
                $hashedPassword,
                $data['role']
            );
            $userStmt->execute();
            $user_id = $conn->insert_id;
            $userStmt->close();

            // Step 3: Insert into `delivery_partners` table
            $partnerStmt = $conn->prepare("INSERT INTO delivery_partners (
                user_id, dob, phone, address,
                vehicle_type, license_number, plate_number,
                id_front, id_back, license_copy,
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
                $data['id_front'],
                $data['id_back'],
                $data['license_copy'],
                $data['bank_name'],
                $data['account_name'],
                $data['account_number'],
                $data['status']
            );

            $result = $partnerStmt->execute();
            $partnerStmt->close();
            $conn->commit();
            return $result;

        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
    }

    public static function C($delivery_partner_id) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT * FROM delivery_partners WHERE id = ?");
            $stmt->bind_param("i", $delivery_partner_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows <= 0) {
                $stmt->close();
                return false;
            }

            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;

        } catch (Exception $e) {
            return false;
        }
    }
}
?>
