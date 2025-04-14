<?php
// Model file: save_customerdelivery_info.php

// Include database connection file (assuming you have a separate database connection)
require_once __DIR__ . '/../config/database.php';

class SaveCustomerDeliveryInfo {

    public static function saveCustDeliveryInfo($customer_id, $fullname, $phone, $email, $address, $latitude, $longitude) {
        // Get database connection
        global $conn;

        // First, check if a record with the given customer_id already exists
        $checkQuery = "SELECT * FROM customer_delivery_address WHERE user_id = ?";

        if ($stmt = $conn->prepare($checkQuery)) {
            // Bind the parameters to the query
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // If a record exists, update it
            if ($result->num_rows > 0) {
                $updateQuery = "UPDATE customer_delivery_address
                                SET name = ?, phone = ?, email = ?, delivery_address = ?, latitude = ?, longitude = ?
                                WHERE user_id = ?";

                if ($stmtUpdate = $conn->prepare($updateQuery)) {
                    // Bind the parameters to the update query
                    $stmtUpdate->bind_param("ssssddi", $fullname, $phone, $email, $address, $latitude, $longitude, $customer_id);

                    // Execute the update statement
                    if ($stmtUpdate->execute()) {
                        return ['status' => true, 'message' => 'Record updated successfully'];
                    } else {
                        return ['status' => false, 'message' => 'Error executing update query: ' . mysqli_error($conn)];
                    }
                } else {
                    return ['status' => false, 'message' => 'Error preparing update query: ' . mysqli_error($conn)];
                }
            } else {
                // If no record exists, insert a new one
                $insertQuery = "INSERT INTO customer_delivery_address (user_id, name, phone, email, delivery_address, latitude, longitude)
                                VALUES (?, ?, ?, ?, ?, ?, ?)";

                if ($stmtInsert = $conn->prepare($insertQuery)) {
                    // Bind the parameters to the insert query
                    $stmtInsert->bind_param("issssdd", $customer_id, $fullname, $phone, $email, $address, $latitude, $longitude);

                    // Execute the insert statement
                    if ($stmtInsert->execute()) {
                        return ['status' => true, 'message' => 'Record inserted successfully'];
                    } else {
                        return ['status' => false, 'message' => 'Error executing insert query: ' . mysqli_error($conn)];
                    }
                } else {
                    return ['status' => false, 'message' => 'Error preparing insert query: ' . mysqli_error($conn)];
                }
            }
        } else {
            return ['status' => false, 'message' => 'Error preparing check query: ' . mysqli_error($conn)];
        }
    }
}
?>
