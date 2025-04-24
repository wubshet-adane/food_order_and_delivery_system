<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants Login</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="../../public/css/login.css">
    <script src="../restaurant/javaScript/login_validation.js" defer></script>
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Input Groups */
.input-group {
    margin-bottom: 15px;
    text-align: left;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

/* Input Wrapper with Icons */
.input-wrapper {
    position: relative;
    width: 100%;
}

.input-wrapper i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

input {
    width: calc(100%); /* Adjusted width to account for icon */
    padding: 10px 10px 10px 35px;
    border: 2px solid #ccc;
    border-radius: 5px;
    outline: none;
    transition: border-color 0.3s ease-in-out;
}

input.valid {
    border-color: green;
}

input.invalid {
    border-color: red;
}

.input-wrapper i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

.error-message{
    color: red;
    font-size: 12px;
    margin-top: 5px;
}

/* Password Toggle Eye Icon */
.password-toggle {
    position: absolute;
    left: 100px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    cursor: pointer;
    color: #888;
}

.password-toggle:hover {
    color: #333;
}


/* Forgot Password & Signup */
.options {
    text-align: right;
    margin-bottom: 10px;
}

.forgot-password {
    color: #007bff;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

.signup-text {
    margin-top: 10px;
    font-size: 14px;
}

.signup-text a {
    color: #007bff;
    text-decoration: none;
}

.signup-text a:hover {
    text-decoration: underline;
}

/* Login Button */
.login-btn {
    width: 100%;
    padding: 10px;
    background-color: #ff9900;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.login-btn:hover {
    background-color: #ff5900;
}

/* Google Login Button */
.google-login button {
    width: 100%;
    padding: 10px;
    background-color: #c5c5c5;
    color: rgb(0, 38, 89);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

.google-login button i {
    margin-right: 5px;
}

.google-login button:hover {
    background-color: #8d8d8d;
}

    </style>
</head>
<body style="background: linear-gradient(rgba(0, 0, 0, 1), rgba(9, 17, 0, 0.1)), url('../../public/images/restaurant_login_bg.jpg');">
    <div class="full-container">
        <div class="login-container" style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(9, 17, 0, 0.9))">
            <div style="border-radius: 50%; padding: 20px; margin: 0; text-align: center;">
                <img src="../../public/images/logo-icon.png" alt="" width="100px" height="100px">
                <h2 style="padding: o; margin: 0; text-transform: capitalize; font-weight: 400; font-family: cursive; color:#ff9900;"><strong class="G3">G3</strong> online food</h2>
            </div>      
            <h2 style="text-align: center; margin-bottom: 4px; color: #ff9900;"> continue as Restaurants Owner</h2>
            <!--form and image setion container-->
            <div class="login-content">
            <!--left side form section-->
                <div class="form-section">
                    <!--form section-->
                    <form id="loginForm">
                        <p id="responseMessage" style="background-color: #DDFF00FF; font-size: 14px;"></p>

                        <div class="input-group">
                            <label for="email" style="color: #fff;">Email Address</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <span class="error-message" id="emailError"></span>
                        </div>
                        
                        <div class="input-group">
                            <label for="password" style="color: #fff;">Password</label>
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
                        
                        <p class="signup-text" style="color: #fff;">Don't have an account? <a href="restaurant_owner_registration.php">Sign Up</a></p>
                    </form>
                    
                    <div class="google-login">
                        <button id="googleLogin">
                            <i class="fab fa-google"></i> Continue with Google
                        </button>
                    </div>
                </div>
                <!--right side image section-->
                <div class="login-image" style="background-image: url('../../public/images/restaurant.jpg');">

                </div>
            </div>
        </div>
    </div>
    <script src="../customers/javaScript/show_password"></script>
</body>
</html>