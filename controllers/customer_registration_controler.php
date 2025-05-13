<?php
require_once __DIR__ . '/../models/customer_registration_Model.php';

// File upload directory
$upload_dir = "../uploads/user_profiles/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $role = 'customer';
    $password = $_POST['password'] ?? null;
    $email = filter_var(trim( $_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    // Handle file uploads
    $profile_image = isset($_FILES['profile_image']) ? handleFileUpload('profile_image', $upload_dir) : "none.jpg";

    $result = Customer_registration::customerRegisterFunction([
        'fullname' => $fullname,
        'role' => $role,
        'password' => $password,
        'email' => $email,
        'phone' => $phone,
        'profile_image' => $profile_image,
    ]);
    
    if ($result) {
        // redirect or show success message
        header("Location: ../views/auth/customer_login.php");
        exit;
    }else {
        header("Location: ../views/auth/customer_registeration.php?error=Registration failed. Please try again.");
        exit;
    }
}

// Function to handle file uploads
function handleFileUpload($fieldName, $uploadDir) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES[$fieldName]['tmp_name'];
        $file_name = basename($_FILES[$fieldName]['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $new_filename = uniqid() . '.' . $file_ext;
        $destination = $uploadDir . $new_filename;
        
        // Check file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'ico'];
        if (!in_array($file_ext, $allowed_types)) {
            die("Error: forbidden image file type.");
        }
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $destination)) {
            return $file_name;
        } else {
            die("Error uploading file.");
        }
    } else {
        return "0";
    }
}
?>