<?php
// Include the database connection file
require_once __DIR__ . '/../config/database.php';

class MerchantRegister {
    public static function restaurantRegistrationfunction($data) {
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

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

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
            $partnerStmt = $conn->prepare("INSERT INTO restaurant_owners (
                user_id, dob, phone, address,
                national_id_front, national_id_back,
                bank_name, account_name, account_number
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )");

            $partnerStmt->bind_param("issssssss",
                $user_id,
                $data['dob'],
                $data['phone'],
                $data['address'],
                $data['national_id_front'],
                $data['national_id_back'],
                $data['bank_name'],
                $data['account_name'],
                $data['account_number'],
            );

            $result = $partnerStmt->execute();
            $partnerStmt->close();
            $conn->commit();
            return $result;

        } catch (Exception $e) {
            $conn->rollback();
            return false;
        }
    }//end of function restaurant owners Registrationfunction

    public static function getRestaurantOwnerDetail($restaurant_owners_id) {
        global $conn;

        try {
            $stmt = $conn->prepare("
                SELECT ro.*, u.name AS user_name, u.image AS user_image, u.email AS user_email
                FROM restaurant_owners ro
                JOIN users u ON ro.user_id = u.user_id
                WHERE ro.user_id = ?
            ");
            $stmt->bind_param("i", $restaurant_owners_id);
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
    }//end of function return restaurant owner detail
}//end of class
?>
