<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery Partner Registration</title>
    <link rel="stylesheet" href="../../public/css/delivery_registration_form.css">
    <!--font awsome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="registration-container">
        <h1>Become a Food Delivery Partner</h1>
        <form id="deliveryForm" action="../../controllers/delivery registration controller.php" method="POST" enctype="multipart/form-data">
            <?php 
                if (isset($_GET['error'])) {
                    echo '<p style="color:red; text-align:center;">' . htmlspecialchars($_GET['error']) . '</p>';
                }
            ?>
            <!-- Personal Information -->
            <fieldset>
                <legend>Personal Information</legend>
                <div class="form-group">
                    <label for="fullname">Full Name*</label>
                    <input type="text" id="fullname" name="fullname" oninput="this.value = this.value.toUpperCase()" required required placeholder="Abebe bikella beyene">
                    <span class="error-message" id="name-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth*</label>
                    <input type="date" id="dob" name="dob" required>
                    <span class="error-message" id="dob-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-message" id="email-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number*</label>
                    <input type="tel" id="phone" name="phone" required>
                    <span class="error-message" id="phone-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="address">Full Address*</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
            </fieldset>
            
            <!-- Vehicle Information -->
            <fieldset>
                <legend>Delivery Vehicle Information</legend>
                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type*</label>
                    <select id="vehicle_type" name="vehicle_type" required>
                        <option value="" disabled>Select Vehicle</option>
                        <option value="bicycle">Bicycle</option>
                        <option value="motorcycle">Motorcycle/Scooter</option>
                        <option value="car">Car</option>
                        <option value="walking">Walking</option>
                    </select>
                </div>
                
                <div class="form-group" id="license-group" style="display:none;">
                    <label for="license_number">Driver's License Number</label>
                    <input type="text" id="license_number" name="license_number" placeholder="wd2223858">
                    <span class="error-message" id="license-error"></span>
                </div>
                
                <div class="form-group" id="plate-group" style="display:none;">
                    <label for="plate_number">Vehicle Plate Number</label>
                    <input type="text" id="plate_number" name="plate_number" placeholder="002145">
                </div>
            </fieldset>
            
            <!-- Documents Upload -->
            <fieldset>
                <legend>Required Documents</legend>
                <div class="form-group">
                    <label for="profile_image">Profile image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*,.pdf">
                </div>

                <div class="form-group">
                    <label for="id_proof">Government ID (Front)*</label>
                    <input type="file" id="id_proof" name="id_proof" accept="image/*,.pdf" required>
                </div>
                
                <div class="form-group">
                    <label for="address_proof">Address Proof*</label>
                    <input type="file" id="address_proof" name="address_proof" accept="image/*,.pdf" required>
                </div>
                
                <div class="form-group" id="license-upload-group" style="display:none;">
                    <label for="license_copy">Driver's License Copy</label>
                    <input type="file" id="license_copy" name="license_copy" accept="image/*,.pdf">
                </div>
            </fieldset>
            
            <!-- Bank Details -->
            <fieldset>
                <legend>Payment Information</legend>
                <div class="form-group">
                    <label for="bank_name">Bank Name*</label>
                    <input type="text" id="bank_name" name="bank_name" required placeholder="commercial bank of ethiopia">
                </div>

                <div class="form-group">
                    <label for="owner_name">Account owner Name*</label>
                    <input type="text" id="owner_name" name="account_name" oninput="this.value = this.value.toUpperCase()" required placeholder="Abebe bikella beyene">
                    <span class="error-message" id="owner-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="account_number">Account Number*</label>
                    <input type="text" id="account_number" name="account_number" required placeholder="100035****202">
                    <span class="error-message" id="account-error"></span>
                </div>
            </fieldset>

             <!-- Bank Details -->
             <fieldset>
                <legend>Password Section</legend>
                <div class="form-group">
                    <label for="password">Password*</label>
                    <input type="password" id="password" name="password" required placeholder="********">
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    <span class="error-message" id="password-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm password*</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="confirm password">
                    <span class="error-message" id="confirm-password-error"></span>
                </div>
            </fieldset>

            <!-- Terms and Conditions -->
            <div class="form-group checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the Terms and Conditions and Privacy Policy*</label>
                <span class="error-message" id="terms-error"></span>
            </div>
            
            <button type="submit" id="submit-btn" class="submit-btn">Register</button>
        </form>
    </div>
    
    <script src="../../public/js/delivery_registration_form_validatio.js"></script>
</body>
</html>