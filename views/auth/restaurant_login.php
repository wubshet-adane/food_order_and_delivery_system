<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants Login</title>
    <link rel="stylesheet" href="../../public/css/login.css">
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>
<body style="background: linear-gradient(rgba(0, 0, 0, 1), rgba(9, 17, 0, 0.1)), url('../../public/images/restaurant_login_bg.jpg');">
    <div class="full-container">
        <div class="login-container" style="background: linear-gradient(rgba(0, 0, 0, 0), rgba(9, 17, 0, 0.9))">
            <h2> Login as Restaurants Owner</h2>
            <hr>
            <!--form and image setion container-->
            <div class="login-content">
            <!--left side form section-->
                <div class="form-section">
                    <?php if (isset($_GET['message'])):?>
                        <div class="responce_message_error">
                            <?php
                                $message = $_GET['message'];
                                echo "<p>".$message."</p>";
                            ?>
                        </div>
                    <?php endif?>
                    <!--form section-->
                    <form class="form-login" action="../../controllers/restaurant_login_controller.php?action=login" method="POST">
                        <div class="input-group">
                            <label for="email"><i class="fa-solid fa-envelope"> </i> Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="input-group">
                            <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                            <input type="password" id="password" name="password" placeholder="********" required>
                            <input type="checkbox" id="showPassword" style="border:1px solid blue;"> <span class="form-check-input">Show password</span> 

                        </div>

                        <button type="submit">Login</button>
                        <a class="forget" href="forgot_password.php" aria-label="Click to reset your password">forgot password?</a>
                        
                        <p class="register-link">
                            Don't have an account? <a href="restaurant_register.php">Register here</a>
                        </p>
                    </form>
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