<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Restaurant Owner Registration</title>
  <!--font awsome-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../public/css/restaurant_owner_registration.css">
  <link rel="stylesheet" href="../customers/css/footer.css">

    <style>
        .logo_container {
            position: relative;
            border-radius: 50%;
            padding: 20px;
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #FFFFFFFF 0%, #838382 50%, #FFFFFFFF 100%);
            box-shadow: 
                0 0 10px rgba(255, 255, 255, 0.2),
                0 0 20px rgba(255, 255, 255, 0.15),
                0 0 30px rgba(255, 255, 255, 0.1);
            transition: all 0.5s ease;
            z-index: 1;
            max-width: 100%;
        }

        .logo_container::before {
            content: "";
            position: absolute;
            top: -8px;
            left: -8px;
            right: -8px;
            bottom: -8px;
            background: linear-gradient(to right, #FFFFFFFF 0%, #FFFFFF4C 50%, #FFFFFFFF 100%);    border-radius: 50%;
            z-index: -1;
        }
          
        .G3{
            background:
                linear-gradient(to top, #ffffff9b, #000, #ffffff9b),
                linear-gradient(to right, #ffffff9b,  #000, #ffffff9b),
                linear-gradient(to bottom, #ffffff9b,  #000, #ffffff9b),
                linear-gradient(to left, #ffffff9b,  #000, #ffffff9b);
            font-size: 35px;
            font-weight: 500; 
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif; 
            color: #fff;
            padding: 0 2px;
            border-radius: 6px;
        }
        @media (max-width: 768px) {
            .logo_container {
                padding: 10px;
                margin: auto;
                width: 450px;
            }
            .G3 {
                font-size: 25px; 
            }

        }
            
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="logo_container">
            <img src="../../public/images/logo-icon.png" alt="" width="100px" height="100px">
            <h2 style="padding: o; margin: auto; text-transform: capitalize; font-weight: 400; font-family: cursive; color:#ff9900;"><strong class="G3">G3</strong> online food</h2>
        </div>
        <h1>Register as a Restaurant Owner</h1>
        <form id="deliveryForm" action="../../controllers/restaurant owner registration controller.php" method="POST" enctype="multipart/form-data">
              <?php if (isset($_GET['error'])): ?>
                  <div class="alert-text">
                      <span ><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></span>
                      <span><button class="alert-close">&times;</button> </span>
                  </div>
              <?php endif; ?>

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
                      <textarea id="address" name="address" rows="3" required placeholder="Debremarkos,Amhara, Ethiopia"></textarea>
                  </div>
              </fieldset>
              <!-- Documents Upload -->
              <fieldset>
                  <legend>Required Documents</legend>
                  <div class="form-group">
                      <label for="profile_image">Profile image(jpg, png, jpeg)</label>
                      <input type="file" id="profile_image" name="profile_image" accept="image/*,.pdf">
                  </div>

                  <div class="form-group">
                      <label for="id_proof_front">National ID (Front)*</label>
                      <input type="file" id="id_proof_front" name="id_proof_front" accept="image/*,.pdf" required>
                  </div>

                  <div class="form-group">
                      <label for="id_proof_back">National ID (back)*</label>
                      <input type="file" id="id_proof_back" name="id_proof_back" accept="image/*,.pdf" required>
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

              <!-- password Details -->
              <fieldset>
                  <legend>Password Section</legend>
                  <div class="form-group">
                      <label for="password">Password*</label>
                      <input type="password" id="password" name="password" required placeholder="********">
                      <i class="fas fa-eye password-toggle" data-id="password" onclick="togglePasword(password)"></i>
                      <span class="error-message" id="password-error"></span>
                  </div>
                  
                  <div class="form-group">
                      <label for="confirm_password">Confirm password*</label>
                      <input type="password" id="confirm_password" name="confirm_password" required placeholder="confirm password">
                      <i class="fas fa-eye password-toggle" data-id="confirm_password" onclick="togglePasword(confirm_password)"></i>
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
              <p class="login-link">Already have an account? <a href="restaurant_login.php">Login here</a></p>
              <p class="login-link">Go back to <a href="../customers/home.php">Home</a></p>
          </form>
      </div>

    <?php include "../customers/footer.php";?>
    <script src="../../public/js/restaurant_owner_registration_form_validation.js"></script>
    <script src="../customers/javaScript/scroll_up.js" defer loading="async"></script>
    <script>
        // Auto-hide alert with fade-out animation
        function autoHideAlert() {
            const alertElement = document.querySelector('.alert-text');
            if (alertElement) {
                // Show alert (in case it's hidden by default)
                alertElement.style.display = 'block';
                alertElement.style.opacity = '1';
                alertElement.style.transition = 'opacity 0.5s ease';
                
                // Start hide timer
                setTimeout(() => {
                    alertElement.style.opacity = '0';
                    
                    // Remove element after fade out completes
                    setTimeout(() => {
                        alertElement.style.display = 'none';
                        
                        // Optional: Remove from DOM completely
                        // alertElement.remove();
                    }, 500); // Match this with transition time
                }, 5000); // 3 seconds before starting fade
            }
        }

        //pasword toggler
        function togglePasword(Id){
            const passwordInput = document.getElementById(Id.id);
            const passwordIcon = document.querySelector(`.password-toggle[data-id="${Id.id}"]`);
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove("fa-eye");
                passwordIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove("fa-eye-slash");
                passwordIcon.classList.add("fa-eye");
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            autoHideAlert();
            
            // Optional: Close button functionality
            const closeButtons = document.querySelectorAll('.alert-close');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.alert-text').style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>
