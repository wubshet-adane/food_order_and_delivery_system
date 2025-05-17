<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/forgot_password.php?error=Invalid request");
    exit;
}

$email = $_POST['email'] ?? '';
$token = $_POST['token'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? '';

if (!$email || !$token || !$new_password || !$confirm_password || !$role) {
    header("Location: ../public/forgot_password.php?error=Missing data (email, token, role)");
    exit;
}

if ($new_password !== $confirm_password) {
    header("Location: ../public/reset-password-form.php?token=$token&email=" . urlencode($email) . "&error=Passwords do not match");
    exit;
}

// Password strength check (at least 8 chars, alphabet, number)
if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/', $new_password)) {
    header("Location: ../public/reset-password-form.php?token=$token&email=" . urlencode($email) . "&error=Weak password");
    exit;
}

$stmt = $conn->prepare("
                SELECT user_id, reset_token, reset_token_expires 
                FROM users 
                WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../public/forgot_password.php?error=Email not found");
    exit;
}

$user = $result->fetch_assoc();

// Validate token and expiration
if (!password_verify($token, $user['reset_token']) || new DateTime() > new DateTime($user['reset_token_expires'])) {
    header("Location: ../public/reset-password-form.php?error=Invalid or expired token");
    exit;
}

// Hash and update password
$hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
$updateStmt = $conn->prepare("
                    UPDATE users 
                    SET password = ?, reset_token = NULL, reset_token_expires = NULL 
                    WHERE user_id = ?");
$updateStmt->bind_param("si", $hashedPassword, $user['user_id']);

if ($updateStmt->execute()) {
    // Password reset success
    if ($role == 'customer'){
        header("Location: ../views/auth/customer_login.php?success=Password reset successful. use new password and login here");
        exit;
    }else if ($role == 'delivery'){
        header("Location: ../views/auth/delivery_login.php?success=Password reset successful. use new password and login here");
        exit;
    }else if ($role == 'restaurant'){
        header("Location: ../views/auth/restaurant_login.php?success=Password reset successful. use new password and login here");
        exit;
    }else{
        header("Location: ../public/forgot_password.php?error=invalid role   but your role is $role");
        exit;
    }
} else {
    header("Location: ../public/reset-password-form.php?token=$token&email=" . urlencode($email) . "&error=Could not update password");
    exit;
}
?>
