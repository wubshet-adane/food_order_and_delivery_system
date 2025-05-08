        <?php
        ob_start(); // 🛡️ Output buffering starts

        // Start session and check authentication
        session_start();
        if (!isset($_SESSION['user_id']) && $_SESSION['userType'] !== 'restaurant') {
            header("Location: ../auth/restaurant_login.php?error=Please login to access this page.");
            exit();
        }

        // Database connection
        require_once '../../config/database.php';

        // Fetch user data
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("
                        SELECT u.name as name, u.email as email, u.image as image, u.password as password, u.role as role, ro.*
                        FROM users u
                        JOIN restaurant_owners ro ON ro.user_id = u.user_id
                        WHERE ro.user_id = ? && u.user_id = ?");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['update_profile'])) {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                
                //password hash
                $update_stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE user_id=?");
                $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);
                $update_stmt->execute();
                
                if ($update_stmt->affected_rows > 0) {
                    $success = "Profile updated successfully!";
                    
                    // Refresh user data
                    $user['name'] = $name;
                    $user['email'] = $email;
                    $user['phone'] = $phone;
                    //
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['name'] = $user['name'];
                } else {
                    $error = "Failed to update profile.";
                }
            }
            if (isset($_POST['detail_information'])) {
                $dob = $_POST['date_of_birth'];
                $address = $_POST['full_address'];
                $bank_name = $_POST['bank_name'];
                $account_owner_name = $_POST['account_owner_name'];
                $account_number = $_POST['account_number'];
                
                //password hash
                $update_stmt = $conn->prepare("UPDATE restaurant_owners SET address=?, dob=?, bank_name=?, account_name=?, account_number=? WHERE user_id=?");
                $update_stmt->bind_param("sssssi", $address, $dob, $bank_name, $account_owner_name, $account_number, $user_id);
                $update_stmt->execute();
                
                if ($update_stmt->affected_rows > 0) {
                    $success = "Information updated successfully!";
                    
                    // Refresh user data
                    $user['dob'] = $dob;
                    $user['address'] = $address;
                    $user['bank_name'] = $bank_name;
                    $user['account_name'] = $account_owner_name;
                    $user['account_number'] = $account_number;
                } else {
                    $error = "Failed to update Information.";
                }
            }

            // Handle password change
            if (isset($_POST['change-password']) && isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
                $user_id = $_SESSION['user_id'];
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                // Fetch current password from database
                $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();

                if (password_verify($current_password, $user_data['password'])) {
                    if ($new_password === $confirm_password) {
                        // Hash new password and update in database
                        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                        $update_stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
                        $update_stmt->bind_param("si", $hashed_password, $user_id);
                        $update_stmt->execute();

                        if ($update_stmt->affected_rows > 0) {
                            $success = "Password changed successfully!";
                        } else {
                            $error = "Failed to change password.";
                        }
                    } else {
                        $error = "New passwords do not match.";
                    }
                } else {
                    $error = "Current password is incorrect.";
                }
            }



            // Handle profile picture upload
            if (isset($_POST['update_picture']) && isset($_FILES['profile_picture'])) {
                $file = $_FILES['profile_picture'];
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'bmp', 'svg', 'tiff', 'ico', 'heic', 'heif'];

                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, $allowed_types)) {
                    $new_filename = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
                    $upload_path = '../../uploads/user_profiles/' . $new_filename;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $update_img_stmt = $conn->prepare("UPDATE users SET image=? WHERE user_id=?");
                        $update_img_stmt->bind_param("si", $new_filename, $user_id);
                        $update_img_stmt->execute();

                        if ($update_img_stmt->affected_rows > 0) {
                            $success = "Profile picture updated successfully!";
                            $user['image'] = $new_filename;
                        } else {
                            $error = "Failed to update profile picture.";
                        }
                    } else {
                        $error = "Failed to upload file.";
                    }
                } else {
                    $error = "Invalid file type. Please upload JPG, PNG, or GIF images.";
                }
            }

        }
    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Profile - G-3 online food ordering system</title>
        <!-- search icon -->
        <link rel="icon" href="../../public/images/logo-icon.png" type="image/x-icon">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/restaurant_profile_page.css"> 
        <link rel="stylesheet" href="../customers/css/footer.css">
    
    </head>
    <body>
        <!-- <?php include_once 'topbar.php'; ?> -->
        <div class="container">
            <div class="profile-header">
                <h1>My Profile</h1>
                <p>Manage your account information</p>
                <a href="javascript:history.back()" class="picture-btn"><i class="fa-solid fa-arrow-left"></i> &nbsp;Go back<span> to Dashboard</span></a>                     
            </div>
           
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" id="alert-danger">
                    <p><strong>Error!</strong> <?php echo $error; ?></p>
                    <button class="close" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>                
                </div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success" id="alert-success">
                    <p><strong>Success!</strong> <?php echo $success; ?></p>
                    <button class="close" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>
            
            <div class="profile-card">
                <!--profile image update section-->
                <div style="text-align: center;">
                    <img src="<?php echo !empty($user['image']) ? '../../uploads/user_profiles/' . $user['image'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=200'; ?>" 
                        alt="<?=$user['image']?>" class="profile-picture">
                    <a href="javascript:void(0);" class="picture-btn" id="changePictureBtn"><i class="fa-solid fa-file-pen"></i> &nbsp;<span>Change Profile Picture</span></a>                     <!-- Hidden image upload form initially -->
                    <div id="uploadSection" style="display: none; margin-top: 10px;">
                        <form action="" method="post" enctype="multipart/form-data">
                            <input class="input-file" type="file" name="profile_picture" accept="image/*" required>
                            <button type="submit" name="update_picture" class="btn" id="update_picture_btn">Upload</button>
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('uploadSection').style.display='none'; document.getElementById('changePictureBtn').style.display = 'inline-block';">Cancel</button>
                        </form>
                    </div>
                </div>

                <!--general information update form-->
                <form method="POST" action="" id="profileForm" class="profile-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    
                    <button type="submit" class="btn" name="update_profile">Update Profile</button>
                    <a href="javascript:void(0);" class="btn btn-secondary" id="change_password_toggle_btn" style="margin-left: 10px;">Change Password</a>
                    <a href="javascript:void(0);" class="btn btn-secondary" id="change_detail_information_toggle_btn" style="margin-left: 10px;">Update detail Information</a>
                </form>
                <!--detail information update form section-->
                <form method="POST" action="" id="detailInformationForm" class="profile-form">
                    <h1>Update detail Information:</h1>
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_address">Full Address</label>
                        <input type="text" id="full_address" name="full_address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                    </div>
                    
                    <fieldset  style="border: none; border-top: 1px solid #ff9900; border-radius:5px; padding: 1rem 0 0 2rem;">
                        <legend style="color: #ff9900;  font-weight: bold;">Payment Information</legend>
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($user['bank_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="account_owner_name">Account Owner Name</label>
                            <input type="text" id="account_owner_name" name="account_owner_name" class="form-control" value="<?php echo htmlspecialchars($user['account_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="account_number">Account Number</label>
                            <input type="text" id="account_number" name="account_number" class="form-control" value="<?php echo htmlspecialchars($user['account_number']); ?>" required>
                        </div>                       
                    </fieldset>
                    
                    <button type="submit" class="btn" name="detail_information" id="detail_information_submit_btn">Submit Information</button>
                    <a href="javascript:void(0);" class="btn btn-secondary" id="close_detail_information_toggle_btn" style="margin-left: 10px; background-color: red;">Cancel to Update</a>
                </form>
            </div>
        </div>
            <!-- Change Password Modal -->
            <div id="changePasswordModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" id="closeModal">&times;</span>
                    <h2>Change Password</h2>
                    <p class="update_password_error" id="update_password_error"></p>
                    <form action="" method="POST" id="changePasswordForm">
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" placeholder="********" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn-submit" name="change-password" id="change-password">Change Password</button>
                    </form>
                </div>
            </div>

        <?php include_once '../customers/footer.php'; ?>
        
        <script>
            // Simple form validation
            document.getElementById('profileForm').addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                
                if (!validateEmail(email)) {
                    alert('Please enter a valid email address');
                    e.preventDefault();
                    return;
                }
                
                if (phone && !validatePhone(phone)) {
                    alert('Please enter a valid phone number');
                    e.preventDefault();
                    return;
                }
            });
            
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function validatePhone(phone) {
                const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
                return re.test(phone);
            }
            setTimeout(()=>{
                    if(document.getElementById('alert-danger')){
                        document.getElementById('alert-danger').style.display = 'none';
                    }
                    if(document.getElementById('alert-success')){
                        document.getElementById('alert-success').style.display = 'none';
                    }
                },6000   //close after 6 seconds
            );


            document.getElementById('changePictureBtn').addEventListener('click', function() {
                const uploadSection = document.getElementById('uploadSection');
                uploadSection.style.display = (uploadSection.style.display === 'none') ? 'block' : 'none';
                this.style.display = 'none';
            });
        </script>
        <script>
            // Open Password Modal when "Change Password" is clicked
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('changePasswordModal');
                const closeBtn = document.getElementById('closeModal');
                const changePasswordLink = document.getElementById('change_password_toggle_btn');
                const changePassword = document.getElementById('change-password');

                if(changePasswordLink){
                    changePasswordLink.addEventListener('click', function(e) {
                        modal.style.display = 'block';
                    });
                }

                // Close Modal when X button clicked
                closeBtn.onclick = function() {
                    modal.style.display = "none";
                }

                // Close Modal when clicking outside the modal content
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }

                
                //check if the password is strong
                changePassword.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent form submission for validation
                    const currentPassword = document.getElementById('current_password').value;
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    const changePasswordForm = document.getElementById('changePasswordForm');  

                    document.getElementById('update_password_error').style.display = 'none'; // Hide error message
                    document.getElementById('current_password').style.border = 'none'; // Reset border style
                    document.getElementById('new_password').style.border = 'none'; // Reset border style
                    document.getElementById('update_password_error').innerText = ''; // Clear previous error message

                    //atleast 1 number and 1 letter and 8 characters
                    const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
                    if (currentPassword < 8){
                        document.getElementById('update_password_error').innerText = 'Current password must be at least 8 characters long.';
                        document.getElementById('update_password_error').style.display = 'block';
                        document.getElementById('current_password').focus();
                        document.getElementById('current_password').style.border = '2px solid red';
                        return;
                    } else if (!passwordRegex.test(newPassword)) {
                        document.getElementById('update_password_error').innerText = 'Password must be at least 8 characters long and contain at least one letter and one number.';
                        document.getElementById('update_password_error').style.display = 'block';
                        document.getElementById('new_password').focus();
                        document.getElementById('new_password').style.border = '2px solid red';
                        return;
                    } else if (currentPassword === newPassword) {
                        document.getElementById('update_password_error').innerText = 'New password cannot be the same as the current password.';
                        document.getElementById('update_password_error').style.display = 'block';
                        document.getElementById('new_password').focus();
                        document.getElementById('new_password').style.border = '2px solid red';
                        return;
                    } else if (newPassword !== confirmPassword) {
                        document.getElementById('update_password_error').innerText = 'New passwords do not match.';
                        document.getElementById('update_password_error').style.display = 'block';
                        document.getElementById('confirm_password').focus();
                        document.getElementById('confirm_password').style.border = '2px solid red';
                        return;
                    } else {
                        // If validation passes, submit the form
                        changePasswordForm.submit();
                    }
                });
            });
        </script>
        <script>
            //open detail information section and hide general default profile section when "upload detail information button clicked"
            document.addEventListener('DOMContentLoaded', function() {
                const detailForm = document.getElementById('detailInformationForm');
                const profileForm = document.getElementById('profileForm');
                const closeBtn = document.getElementById('close_detail_information_toggle_btn');
                const detail_info_toggle_btn = document.getElementById('change_detail_information_toggle_btn');
                //const info_submit_btn = document.getElementById('change-detail_information_submit_btn');

                //hide detail information form when document loaded
                detailForm.style.display = 'none';
                profileForm.style.display = 'block';

                //click detail information toggler
                detail_info_toggle_btn.addEventListener('click', function(){
                    detailForm.style.display = 'block';
                    profileForm.style.display = 'none';
                })

                //close detail information update form section and display general information form section
                closeBtn.addEventListener('click', function(){
                    detailForm.style.display = 'none';
                    profileForm.style.display = 'block';
                })                
            });
        </script>

        <script src="../customers/javaScript/scroll_up.js"></script>
    </body>
    </html>