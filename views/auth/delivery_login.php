<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Worker Login</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="../../public/css/login_new.css">
    <link rel="stylesheet" href="../../public/css/footer.css">
    <script src="../delivery/js/login_validation.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div style="border-radius: 50%; padding: 20px; margin: 0;">
            <img src="../../public/images/logo-icon.png" alt="" width="100px" height="100px">
            <h2 style="padding: o; margin: 0; text-transform: capitalize; font-weight: 400; font-family: cursive; color:#ff9900;"><strong class="G3">G3</strong> online food</h2>
        </div>

        <p id="responseMessage"></p>
        <form id="loginForm">
            <div class="input-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <span class="error-message" id="emailError"></span>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
                <span class="error-message" id="passwordError"></span>
            </div>

            <div class="options">
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
            
            <p class="signup-text">Don't have an account? <a href="delivery_registration.php">Sign Up</a></p>
            
        </form>
        
        <div class="google-login">
            <button id="googleLogin">
                <i class="fab fa-google"></i> Continue with Google
            </button>
        </div>
    
    </div>
        
    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 G3 online food ordering system. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
