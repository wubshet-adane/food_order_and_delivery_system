<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/customer_login.php?error=Please login to access this page.");
    exit();
}

// Database connection
require_once '../../config/database.php';

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Fetch order history
    $order_stmt = $conn->prepare("
        SELECT o.*, p.amount
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
        WHERE customer_id = ? ORDER BY order_date DESC LIMIT 5");
    $order_stmt->bind_param("i", $user_id);
    $order_stmt->execute();
    $orders = $order_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FoodOrder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/customer_profile_page.css"> 
    <link rel="stylesheet" href="css/footer.css">
   
</head>
<body>
    <?php include_once 'topbar.php'; ?>
    <div class="container">
        <div class="profile-header">
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success" id="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="profile-card">
            <form method="POST" action="">
                <div style="text-align: center;">
                    <img src="<?php echo !empty($user['image']) ? '../../uploads/user_profiles/' . $user['image'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=200'; ?>" 
                         alt="<?=$user['image']?>" class="profile-picture">
                    <a href="change_picture.php" style="color: var(--primary-color); text-decoration: none;">Change Picture</a>
                </div>
                
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
                
                <button type="submit" class="btn">Update Profile</button>
                <a href="change_password.php" class="btn btn-secondary" style="margin-left: 10px;">Change Password</a>
            </form>
        </div>
        
        <div class="order-history">
            <h2>Order History</h2>
            
            <?php
            
            if ($orders->num_rows > 0) {
                while ($order = $orders->fetch_assoc()) {
                    $status_class = '';
                    switch ($order['status']) {
                        case 'Delivered': 
                            $status_class = 'status-delivered'; 
                            break;
                        case 'Cancelled': 
                            $status_class = 'status-cancelled'; 
                            break;
                        default: 
                        $status_class = 'status-pending';
                    }
                    ?>
                    <div class="order-card">
                        <div class="order-info">
                            <h3>Order #<?php echo $order['order_id']; ?></h3>
                            <p><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                            <p>Total: $<?php echo number_format($order['amount'], 2); ?></p>
                        </div>
                        <div class="order-status <?php echo $status_class; ?>">
                            <?php echo $order['status']; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>You haven't placed any orders yet.</p>";
            }
            ?>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="orders.php" class="btn">View All Orders</a>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>
    
    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
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
        },3000
    )
    </script>
</body>
</html>