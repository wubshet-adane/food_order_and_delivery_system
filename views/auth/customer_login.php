
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Login</title>
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/login.css">
</head>
<body>
    <div class="full-container">
        <div class="login-container">
            <h2>Customers Login</h2>
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
                    <form class="form-login" action="../../controllers/customer_login_controller.php?action=login" method="POST">
                        <div class="input-group">
                            <label for="email"><i class="fa-solid fa-invelope"> </i> Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="input-group">
                            <label for="password"><i class="fa-solid fa-lock"> </i> Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <span class="form-check-input">Show password</span><input type="checkbox" id="showPassword" style="border:1px solid blue;"> 

                        </div>

                        <button type="submit">Login</button>
                        <p class="register-link">
                            Don't have an account? <a href="customer_register.html">Register here</a>
                        </p>
                    </form>
                </div>
                <!--right side image section-->
                <div class="login-image">

                </div>
            </div>
        </div>
    </div>
    <script src="../customers/javaScript/show_password"></script>
</body>
</html>