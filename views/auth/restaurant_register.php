<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../public/css/login.css">
    <style>
        .group{
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .input-group{
            width: 45%;
        }

        input:type[checkbox]{
            width: 100px;
        }
        .submit-group{
            display: flex;
            justify-content: center;
            flex-direction: column;
        }
        .checkbox{
            display: inline-block;
            width: 100%;
            flex-direction: row;
            color: #99f;
        }

        @media (max-width: 768px) {
            .input-group{
                min-width: 90%;
            }
        }
    </style>
</head>
<body style="background: linear-gradient(rgba(50, 50, 50, 0.9), rgba(50, 57, 40, .7)), url('../../public/images/restaurant_login_bg.jpg');">
<div class="full-container">
        <div class="login-container">
            <h2> create merchant account</h2>
            <hr>
            <!--form and image setion container-->
            <div class="register-content">
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
                    <form class="form-login" action="../../controllers/restaurant_register_controller.php?action=register" method="POST" enctype="multipart/form-data">
                        <div class="group">
                            <div class="input-group">
                                <label for="Fname">First Name</label>
                                <input type="text" id="Fname" name="Fname" placeholder="First Name" required>
                            </div>
                            <div class="input-group">
                                <label for="Lname">Last Name</label>
                                <input type="text" id="Lname" name="Lname" placeholder="Last Name" required>
                            </div>
                            <div class="input-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="yahoo@gmail.com" required>
                            </div>
                            <div class="input-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" placeholder="0912345678" required>
                            </div>
                        </div>
                        <div class="group">
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" placeholder="********" required>
                            </div>
                            <div class="input-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="********" required>
                                <p class="checkbox"><input type="checkbox" id="showPassword" style="border:1px solid blue;"> Show password</p>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="profile_image">Upload Profile Picture</label>
                            <input type="file"  name="profile_image">
                        </div>
                        <div class="input-group">
                            <p class="checkbox register-link"><input type="checkbox" name="terms" required>I agree to the <a href="#">Terms</a> & <a href="">Conditions</a></p>
                        </div>
                        <div class="submit-group">
                            <button type="submit">Register</button>
                            <p class="register-link">
                                Have an account? <a href="restaurant_login.php">Login</a>
                            </p>
                        </div>
                    </form>
                </div>
                <!--right side image section
                <div class="login-image" style="background-image: url('../../public/images/restaurant.jpg');">

                </div>
                    -->
            </div>
        </div>
    </div>
    <script src="../customers/javaScript/show_password"></script></body>
</html>