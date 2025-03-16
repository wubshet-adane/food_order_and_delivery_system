<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../public/css/login.css">
</head>
<body style="background: linear-gradient(rgba(0, 0, 0, 0.9), rgba(9, 17, 0, 0.1)), url('../../public/images/restaurant_login_bg.jpg');">
<div class="full-container">
        <div class="login-container">
            <h2> create merchant account</h2>
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
                        <form class="form-login" action="../../controllers/restaurant_register_controller.php?action=register" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                            <label for="email">First Name</label>
                                <input type="text" name="owner_name" placeholder="Full Name" required>
                                <input type="text" name="owner_name" placeholder="Full Name" required>
                            </div>
                            <input type="email" name="owner_email" placeholder="Email" required>
                            <input type="text" name="owner_phone" placeholder="Phone Number" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <!--
                            <h3>Restaurant Details</h3>
                            <input type="text" name="restaurant_name" placeholder="Restaurant Name" required>
                            <input type="email" name="restaurant_email" placeholder="Restaurant Email" required>
                            <input type="text" name="restaurant_phone" placeholder="Restaurant Phone" required>
                            <textarea name="restaurant_address" placeholder="Address" required></textarea>
                            <input type="text" name="map_location" placeholder="Google Map Location">
                        
                            <h3>Legal & Payment</h3>
                            <input type="text" name="business_license" placeholder="Business License Number" required>
                            <input type="text" name="tax_id" placeholder="Tax ID" required>
                            <input type="text" name="bank_name" placeholder="Bank Name" required>
                            <input type="number" name="bank_account" placeholder="Bank Account Number" required>
                        -->
                            <h3>Upload Files</h3>
                            <input type="file" name="restaurant_logo" required>
                            
                            <input type="checkbox" name="terms" required> I agree to the Terms & Conditions
                            
                            <button type="submit">Register</button>

                        </form>
                        </div>
                <!--right side image section-->
                <div class="login-image" style="background-image: url('../../public/images/restaurant.jpg');">

                </div>
            </div>
        </div>
    </div>
    <script src="../customers/javaScript/show_password"></script></body>
</html>