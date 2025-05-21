<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password | AppName</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/forgot_password.css">
    <link rel="icon" href="images/logo-icon.png" type="image/x-icon">

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

    </style>
</head>
<body>
    <div class="container">

           <div class="forgot-password-card">

           <?php
            $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
            $success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
            if (isset($error)): ?>
              <div class="alert alert-danger" id="alert-danger" style="display: flex; justify-content: space-between;">
                  <p><strong>Error!</strong> <?php echo $error; ?></p>
                  <button class=".close" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>                
              </div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success" id="alert-success" style="display: flex; justify-content: space-between;">
                    <p><strong>Success!</strong> <?php echo $success; ?></p>
                    <button class=".close" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>


            <div class="brand-logo">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Forgot Password?</h1>
            <p class="subtext">Enter your email and we'll send you a link to reset your password</p>
            
            <form id="forgotPasswordForm" action="reset-password.php" method="POST">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span class="btn-text">Send Reset Link</span>
                    <i class="fas fa-paper-plane btn-icon"></i>
                </button>
            </form>
            
            <div class="back-to-login">
                <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
        
        <div class="illustration">
            <!-- Instruction Box -->
            <div class="forgot-password-box">
                <h2><i class="fas fa-unlock-alt"></i> Forgot Your Password?</h2>
                <p class="intro-text">No worries! Follow the steps below to reset your password and get back to enjoying delicious meals with our platform <strong>G-3 Online Fodd Ordering System.</strong></p>
                
                <ol class="steps">
                <li>Enter your <strong>registered email address</strong> in the form left side.</li>
                <li>Check your email for a <strong>password reset link</strong>.</li>
                <li>Click the link and create a <strong>new secure password</strong>.</li>
                <li>You’ll be redirected back to the <strong>login page</strong>.</li>
                </ol>
            
                <div class="note">
                <strong>📌 Note:</strong> Be sure to check your <em>spam or junk folder</em> if the email doesn’t appear within a few minutes. Need help? <a href="support.php">Contact Support</a>.
                </div>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>