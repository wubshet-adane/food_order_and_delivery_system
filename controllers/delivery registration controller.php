<?php
require_once __DIR__ . '/../models/delivery_registration_model.php';

// File upload directory
$upload_dir = "../uploads/user_profiles/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $role = 'delivery';
    $password = $_POST['password'] ?? null;
    $dob = htmlspecialchars(trim($_POST['dob']));
    $email = filter_var(trim( $_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $vehicle_type = htmlspecialchars(trim($_POST['vehicle_type']));
    $license_number = htmlspecialchars(trim($_POST['license_number']));
    $plate_number = htmlspecialchars(trim($_POST['plate_number']));
    $bank_name = htmlspecialchars(trim($_POST['bank_name']));
    $account_name = htmlspecialchars(trim($_POST['account_name']));
    $account_number = htmlspecialchars(trim($_POST['account_number']));
    $status = 'pending';
    
    // Handle file uploads
    $profile_image = isset($_FILES['profile_image']) ? handleFileUpload('profile_image', $upload_dir) : "none.jpg";
    $id_front = handleFileUpload('id_front', $upload_dir);
    $id_back = handleFileUpload('id_back', $upload_dir);
    $license_copy = isset($_FILES['license_copy']) ? handleFileUpload('license_copy', $upload_dir) : "none.jpg";

    $result = DeliverRegister::deliveryRegistrationfunction([
        'fullname' => $fullname,
        'role' => $role,
        'password' => $password,
        'dob' => $dob,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'vehicle_type' => $vehicle_type,
        'license_number' => $license_number,
        'plate_number' => $plate_number,
        'profile_image' => $profile_image,
        'id_front' => $id_front,
        'id_back' => $id_back,
        'license_copy' => $license_copy,
        'bank_name' => $bank_name,
        'account_name' => $account_name,
        'account_number' => $account_number,
        'status' => $status
    ]);
    
    if ($result) {
        // redirect or show success message
        $_SESSION['email'] = $email;
        header("Location: ../views/delivery/index.php");
        exit;
    }elseif ($error) {
        // Email already registered
        header("Location: ../views/auth/delivery_registration.php?error=Email already registered.");
        exit;
    }else {
        header("Location: ../views/auth/delivery_registration.php?error=Registration failed. Please try again.");
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
        $thefilename = $new_filename;
        
        // Check file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'ico'];
        if (!in_array($file_ext, $allowed_types)) {
            die("Error: forbidden image file type.");
        }
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $destination)) {
            return $thefilename;
        } else {
            die("Error uploading file.");
        }
    } else {
        return "0";
    }
}
?>