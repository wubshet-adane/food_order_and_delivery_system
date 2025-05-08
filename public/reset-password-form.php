
<?php
require_once '../config/database.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (!$email || !$token) {
    header('Location: ../public/forgot_password.php?error=problem with your email address or token (INVALID)');
    exit;
}

$stmt = $conn->prepare("
    SELECT reset_token, reset_token_expires 
    FROM users 
    WHERE email = ?");
    if(!$stmt){
        die($conn->error);
    }
$stmt->bind_param("s", $email);
if(!$stmt){
    die($conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../public/forgot_password.php?error=problem with your email address or token');
    exit;
}

$user = $result->fetch_assoc();
$tokenValid = password_verify($token, $user['reset_token']);
$notExpired = new DateTime() < new DateTime($user['reset_token_expires']);

if (!$tokenValid || !$notExpired) {
    header('Location: ../public/forgot_password.php?error=problem with your email address or token');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | G-3 Online Food Ordering</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/forgot_password.css">
    <style>
 .forgot-password-box {
        max-width: 600px;
        margin: 30px auto;
        background: #ffffff;
        border: 1px solid #e6e6e6;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        padding: 30px 25px;
        border-radius: 15px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        animation: fadeIn 0.5s ease-in-out;
      }

      .forgot-password-box h2 {
        font-size: 24px;
        color: #ff6600;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .forgot-password-box .intro-text {
        font-size: 16px;
        margin: 15px 0 25px;
        color: #555;
      }

      .forgot-password-box .steps {
        margin-left: 20px;
        padding-left: 10px;
        color: #444;
        line-height: 1.8;
      }

      .forgot-password-box .note {
        margin-top: 25px;
        background: #fff4e5;
        border-left: 4px solid #ffa500;
        padding: 15px;
        border-radius: 8px;
        font-size: 15px;
        color: #7a5900;
      }

      .forgot-password-box a {
        color: #ff6600;
        text-decoration: none;
        transition: 0.3s ease;
      }

      .forgot-password-box a:hover {
        text-decoration: underline;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      /* Responsive */
      @media (max-width: 600px) {
        .forgot-password-box {
          padding: 20px 15px;
        }

        .forgot-password-box h2 {
          font-size: 20px;
        }

        .forgot-password-box .steps li {
          font-size: 14px;
        }
      }


    

        .input-with-icon .toggle-password{
            position: absolute;  
            left: auto; 
            right: 20px;                     
        }
        .password-rules {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
        }
        .password-rules ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        .password-match {
            color: green;
            display: none;
        }
        .password-mismatch {
            color: red;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-password-card">
            <div class="brand-logo">
                <i class="fas fa-key"></i>
            </div>
            <h1>Create New Password</h1>
            
            <form id="resetPasswordForm" action="proccess-reset.php" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                <input type="hidden" name="role" value="<?= htmlspecialchars($_GET['role'] ?? '') ?>">
                
                <div class="input-group">
                    <label for="new_password">New Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new_password" name="new_password" required 
                               pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,}"
                               title="Must contain at least 8 characters, including uppercase, lowercase and numbers">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                    </div>
                    <span id="passwordMatch" class="password-match">
                        <i class="fas fa-check-circle"></i> Passwords match
                    </span>
                    <span id="passwordMismatch" class="password-mismatch">
                        <i class="fas fa-times-circle"></i> Passwords don't match
                    </span>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span class="btn-text">Reset Password</span>
                    <i class="fas fa-sync-alt btn-icon"></i>
                </button>
            </form>
            <div class="back-to-login">
                <a href="../views/customers/home.php"><i class="fas fa-arrow-left"></i> Go to Home</a>
            </div>
        </div>
        <div class="illustration">
            <!-- Instruction Box -->
            <div class="forgot-password-box">
                <h2><i class="fas fa-key"></i> Create new password</h2>
                <p class="intro-text">No worries! Follow the steps and requirements below to create your new password and get back to enjoying delicious meals with our platform <strong>G-3 Online Fodd Ordering System.</strong></p>
                
                <strong>Password Requirements:</strong>
                <ol class="steps">
                    <li>Minimum 8 characters</li>
                    <li>At least one letter</li>
                    <li>At least one number</li>
                    <li>Two password fields must take the same valu only</li>
                    <li>You must complete with in <strong>5 minuties</strong></li>
                </ol>
        
                <div class="note">
                <strong>📌 Note:</strong> Be sure after successful form submition <em>you will be redirect to your login page</em> after suuccessful form submition if you can't redirect to login page contact us quickly <a href="contact.php">Contact Support</a>.
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = this.value;
            
            if (confirmPass.length > 0) {
                if (newPass === confirmPass) {
                    document.getElementById('passwordMatch').style.display = 'inline';
                    document.getElementById('passwordMismatch').style.display = 'none';
                } else {
                    document.getElementById('passwordMatch').style.display = 'none';
                    document.getElementById('passwordMismatch').style.display = 'inline';
                }
            } else {
                document.getElementById('passwordMatch').style.display = 'none';
                document.getElementById('passwordMismatch').style.display = 'none';
            }
        });
    </script>
</body>
</html>