<?php
// Controller file
session_start();

if (!isset($_SESSION['user_id'])) {
    header ('Location: ../views/auth/customer_login.php');
    exit();
}

// Include model file
require_once '../models/save_customerdelivery_info.php';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize data
    $fullname = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['delivery_address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $customer_id = $_SESSION['user_id'];

    // Check if all required fields are provided
    if (empty($customer_id) || empty($fullname) || empty($phone) || empty($email) || empty($address) || empty($latitude) || empty($longitude)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Call model function
    $result = SaveCustomerDeliveryInfo::saveCustDeliveryInfo($customer_id, $fullname, $phone, $email, $address, $latitude, $longitude);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Something went wrong while saving']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
