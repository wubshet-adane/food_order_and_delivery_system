



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register G-3 food order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/forgot_password.css">
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/x-icon">

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

        .input-with-icon .toggle-password:hover{
            cursor: pointer;
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
                <i class="fas fa-id-card"></i>
            </div>
            <h1>register here</h1>
            <p class="subtext">Join us now! Enter your correct information to create your customer account.</p>
                
            <form action="../../controllers/customer_registration_controler.php" method="POST">
                
                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="address">Profile image</label>
                    <div class="input-with-icon">
                        <input type="file" id="image" name="image">
                    </div>
                </div>

                <div class="input-group">
                    <label for="new_password">New Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new_password" name="new_password" required 
                               pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,}"
                               title="Must contain at least 8 characters, including letters and numbers">
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
                    <span class="btn-text">Register</span>
                </button>
            </form>
            <p class="login-link">
                Already have an account? <a href="customer_login.php">Login here</a>
            </p>
            
            <div class="back-to-login">
                <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
        
        <div class="illustration">
            <!-- Instruction Box -->
            <div class="forgot-password-box">
                <h2><i class="fas fa-id-card"></i> New Here? Sign Up as a Customer!</h2>
                <p class="intro-text">Welcome to <strong>G-3 Online Food Ordering System.</strong> Follow these easy steps to create your customer account and start ordering delicious meals in minutes.</p>

                <ol class="steps">
                    <li>Fill in your <strong>name, email address, and password</strong> in the form on the left.</li>
                    <li>Enter <strong>atleast 8 size password it must contains atleast 1 letter and 1 number</strong>.</li>
                    <li>Click the <strong>Sign Up</strong> button to create your account.</li>
                    <li>Once verified, you’ll be redirected to your <strong>customer login page</strong>.</li>
                </ol>

                <div class="note">
                    <strong>📌 Note:</strong> If you don’t redirect to login page or never get error directive message ? <em><a href="../../public/support.php">Contact Support</a> </em>.
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