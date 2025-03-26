<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G3-Restaurant-Owner</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <!--font ausome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/login.css">
    <style>
        h2{
            color: #ff9900;
        }
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
        .terms_checkbox{
            display: flex;
            flex-direction: row;
            justify-content:flex-start;
            width: 100%;
            color: #99f;
        }
        .checkbox{
            color: #99f;
            width: 15px;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .input-group{
                min-width: 90%;
            }
        }
    </style>
</head>
<body style="background: linear-gradient(rgba(50, 50, 50, 0.0), rgba(0, 0, 0, 1)), url('../../public/images/restaurant_login_bg.jpg');">
<div class="full-container">
        <div class="login-container" style="background: linear-gradient(rgba(0, 0, 50, 0.7), rgba(0, 0, 0, 0))">
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
                                <label for="Fname"><i class="fa fa-solid fa-user-tie"></i> First Name</label>
                                <input type="text" id="Fname" name="Fname" placeholder="First Name" required>
                            </div>
                            <div class="input-group">
                                <label for="Lname"><i class="fa fa-solid fa-name"></i> Last Name</label>
                                <input type="text" id="Lname" name="Lname" placeholder="Last Name" required>
                            </div>
                            <div class="input-group">
                                <label for="email"><i class="fa fa-solid fa-envelope"></i> Email</label>
                                <input type="email" id="email" name="email" placeholder="yahoo@gmail.com" required>
                            </div>
                            <div class="input-group">
                                <label for="phone"><i class="fa fa-solid fa-phone"></i> Phone Number</label>
                                <input type="text" id="phone" name="phone" placeholder="0912345678" required>
                            </div>
                        </div>
                        <div class="group">
                            <div class="input-group">
                                <label for="password"><i class="fa fa-solid fa-lock"></i> Password</label>
                                <input type="password" id="password" name="password" placeholder="********" required>
                            </div>
                            <div class="input-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="********" required>
                                <div>
                                    <p class="terms_checkbox"> <input type="checkbox" class="checkbox" id="showPassword"> <span>show password</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="profile_image">Upload Profile Picture</label>
                            <input type="file"  name="profile_image">
                        </div>
                        <div class="input-group">
                            <p class="terms_checkbox register-link"><input type="checkbox" class="checkbox" name="terms" required><span>I agree to the <a href="#">Terms</a> & <a href="">Conditions</a></span></p>
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