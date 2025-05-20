<?php
// Set security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Database configuration
require_once '../config/database.php';
//get email function
require_once 'email_sender_of_password_reset_link.php';

// Process the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    // Create MySQLi connection

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection error']);
        exit;
    }

    // Prepare the statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $role = $user['role'];

        // Generate secure token
        $token = bin2hex(random_bytes(32)); // 64-character token
        $expires = (new DateTime('+5 minutes'))->format('Y-m-d H:i:s'); // valid for 5 minutes
        $hashedToken = password_hash($token, PASSWORD_BCRYPT); // securely hashed

        // Update token and expiration
        $updateStmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE user_id = ?");
        $updateStmt->bind_param("ssi", $hashedToken, $expires, $user['user_id']);
        $updateStmt->execute();

        // Send reset link (or log for demo)
        $resetLink = "http://localhost:8081/food_ordering_system/public/reset-password-form.php?token=$token&role=$role&email=" . urlencode($email);
        $sendtEmail = sendPasswordResetLink($email, $resetLink, $role); // Implement this function to send the email
        if ($sendtEmail !== true) {
            error_log("Email sending failed: " . $sendtEmail);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $sendtEmail]);
            header('Location: ../public/forgot_password.php?error=Email sending failed');
            exit;
        }
        else {
            // Log the email sending for demo purposes
            error_log("Password reset link sent to: " . $email);
            if($role == 'customer'){
                header('Location: ../views/auth/customer_login.php?success=Password reset link sent to your email open the link from your email and create new password then login here');
                exit;

            }
            else if($role == 'restaurant'){
                header('Location: ../views/auth/restaurant_login.php?success=Password reset link sent to your email open the link from your email and create new password then login here');
                exit;
            }
            else if($role == 'delivery'){
                header('Location: ../views/auth/delivery_login.php?success=Password reset link sent to your email open the link from your email and create new password then login here');
                exit;
            }
            else if($role == 'admin'){
                header('Location: ../views/auth/admin_login.php?success=Password reset link sent to your email open the link from your email and create new password then login here');
                exit;
            }
        }
    }else {
        // Log the email existence check for demo purposes
        error_log("Password reset link requested for non-existent email: " . $email);
        header('Location: ../public/forgot_password.php?error=Email not found');
        exit;
    }

    // Always return success regardless of email existence  
    // echo json_encode([
    //     'success' => true,
    //     'message' => 'If this email exists in our system, you will receive a reset link'
    // ]);

    $stmt->close();
    if (isset($updateStmt)) $updateStmt->close();
    $conn->close();

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
