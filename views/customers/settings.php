<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/customr_settings.php';
require_once '../../models/customer_Order_settings.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/customer_login.php');
    exit();
}

// Get user details
$userModel = new User($conn);
$user = $userModel->getUserById($_SESSION['user_id']);

// Get user orders
$orderModel = new Order($conn);
$orders = $orderModel->getUserOrders($_SESSION['user_id']);

// Calculate statistics
$stats = $orderModel->getUserOrderStats($_SESSION['user_id']);

// Handle form submissions
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        //password hash
        $update_stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE user_id=?");
        if(!$update_stmt)die($conn->error);
        $update_stmt->bind_param("sssi", $name, $email, $phone, $_SESSION['user_id']);
        if(!$update_stmt)die($conn->error);
        $update_stmt->execute();
        if(!$update_stmt)die($conn->error);
        
        if ($update_stmt->affected_rows > 0) {
            $success = "Profile updated successfully!";
            
            // Refresh user data
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
            //
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['phone'] = $user['name'];
        } else {
            if(!$update_stmt)die($conn->error);
            $error = "Failed to update profile.";
        }
    }

    // Handle password change
    if (isset($_POST['change_password']) && isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
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


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings - Food Ordering System</title>
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/settings.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'topbar.php'; ?>
    
        
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

    <div class="settings-wrapper">
        <div class="settings-sidebar">
            <div class="user-profile-card">
                <div class="avatar">
                    <?php if($_SESSION['profile_image']):?><img style="width: 80px; height: 80px; border-radius: 50%;" src="../../uploads/user_profiles/<?=$_SESSION['profile_image']?>" alt=""> <?php else:?> <i class="fas fa-user-circle"></i><?php endif;?>
                </div>
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>
            
            <nav class="settings-nav">
                <ul>
                    <li class="active"><a href="#profile"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="#orders"><i class="fas fa-history"></i> Order History</a></li>
                    <li><a href="#stats"><i class="fas fa-chart-line"></i> Statistics</a></li>
                    <li><a href="#password"><i class="fas fa-lock"></i> Change Password</a></li>
                </ul>
            </nav>
        </div>

        <div class="settings-content">
            <!-- Profile Section -->
            <section id="profile" class="settings-section active">
                <h2><i class="fas fa-user"></i> Profile Settings</h2>
                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Enter your full name">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Enter your email">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="Enter your phone number">
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn-primary">Save Changes</button>
                </form>
            </section>

            <!-- Order History Section -->
            <section id="orders" class="settings-section">
                <h2><i class="fas fa-history"></i> Order History</h2>
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <p>You haven't placed any orders yet.</p>
                        <a href="../restaurants/" class="btn-primary">Browse Restaurants</a>
                    </div>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <h3>Order #<?= $order['order_id'] ?></h3>
                                    <span class="order-date"><?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></span>
                                </div>
                                <div class="order-details">
                                    <div class="restaurant-info">
                                        <i class="fas fa-utensils"></i>
                                        <span><?= htmlspecialchars($order['restaurant_name']) ?></span>
                                    </div>
                                    <div class="order-meta">
                                        <!-- <span><i class="fas fa-box"></i> <?= $order['item_count'] ?> items</span> -->
                                        <span><i class="fas fa-receipt"></i> <?= number_format($order['total_amount'], 2) ?> birr</span>
                                    </div>
                                </div>
                                <div class="order-footer">
                                    <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                        <?= $order['status'] ?>
                                    </span>
                                    <a href="order_history.php?#order_<?= $order['order_id'] ?>" class="btn-outline">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Statistics Section -->
            <section id="stats" class="settings-section">
                <h2><i class="fas fa-chart-line"></i> Order Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['total_orders'] ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= number_format($stats['total_spent'], 2) ?> birr</h3>
                            <p>Total Spent</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['favorite_restaurant'] ?? 'N/A' ?></h3>
                            <p>Favorite Restaurant</p>
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>Monthly Order Activity</h3>
                    <canvas id="orderChart"></canvas>
                </div>
            </section>

            <!-- Change Password Section -->
            <section id="password" class="settings-section">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
                <form method="POST" class="password-form">
                    <div class="form-group">
                        <label>Current Password</label>
                        <div class="password-input">
                            <input type="password" name="current_password" id="current_password" required>
                            <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="password-input">
                            <input type="password" name="new_password" id="new_password" required>
                            <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                        </div>
                        <div class="password-strength">
                            <span class="strength-bar"></span>
                            <span class="strength-bar"></span>
                            <span class="strength-bar"></span>
                            <span class="strength-text"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <div class="password-input">
                            <input type="password" name="confirm_password" id="confirm_password" required>
                            <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn-primary">Update Password</button>
                </form>
            </section>
        </div>
    </div>

    <?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Smooth navigation between sections
        document.querySelectorAll('.settings-nav a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));

                // Update active states
                document.querySelectorAll('.settings-nav li').forEach(li => li.classList.remove('active'));
                this.parentElement.classList.add('active');

                document.querySelectorAll('.settings-section').forEach(section => section.classList.remove('active'));
                target.classList.add('active');

                // Smooth scroll to section inside .settings-content
                const container = document.querySelector('.settings-content');
                container.scrollTo({
                    top: target.offsetTop - 20,
                    behavior: 'smooth'
                });
            });
        });

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function () {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this;

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password strength indicator
        const passwordInput = document.getElementById('new_password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function () {
                const password = this.value;
                const strengthBars = document.querySelectorAll('.strength-bar');
                const strengthText = document.querySelector('.strength-text');

                // Reset
                strengthBars.forEach(bar => bar.classList.remove('weak', 'medium', 'strong'));
                strengthText.classList.remove('weak', 'medium', 'strong');
                strengthText.textContent = '';

                if (password.length === 0) return;

                // Calculate strength
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;

                // Update UI
                if (strength <= 2) {
                    strengthBars.forEach((bar, index) => {
                        if (index < 1) bar.classList.add('weak');
                    });
                    strengthText.classList.add('weak');
                    strengthText.textContent = 'Weak';
                } else if (strength <= 4) {
                    strengthBars.forEach((bar, index) => {
                        if (index < 2) bar.classList.add('medium');
                    });
                    strengthText.classList.add('medium');
                    strengthText.textContent = 'Medium';
                } else {
                    strengthBars.forEach((bar, index) => {
                        if (index < 3) bar.classList.add('strong');
                    });
                    strengthText.classList.add('strong');
                    strengthText.textContent = 'Strong';
                }
            });
        }


<?php if (!empty($stats['daily_data']) || !empty($stats['weekly_data']) || !empty($stats['monthly_data'])): ?>
    const labels = Array.from(new Set([
        ...Object.keys(<?= json_encode($stats['daily_data'] ?? []) ?>),
        ...Object.keys(<?= json_encode($stats['weekly_data'] ?? []) ?>),
        ...Object.keys(<?= json_encode($stats['monthly_data'] ?? []) ?>)
    ])).sort();

    const dailyDataMap = <?= json_encode($stats['daily_data'] ?? []) ?>;
    const weeklyDataMap = <?= json_encode($stats['weekly_data'] ?? []) ?>;
    const monthlyDataMap = <?= json_encode($stats['monthly_data'] ?? []) ?>;

    const dailyData = labels.map(label => dailyDataMap[label] || 0);
    const weeklyData = labels.map(label => weeklyDataMap[label] || 0);
    const monthlyData = labels.map(label => monthlyDataMap[label] || 0);

    const ctx = document.getElementById('orderChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            responsive: true,
            labels: labels,
            datasets: [
                {
                    label: 'Daily Orders',
                    data: dailyData,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Weekly Orders',
                    data: weeklyData,
                    backgroundColor: 'rgba(54, 162, 2, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Monthly Orders',
                    data: monthlyData,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index' },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date / Week / Month'
                    }
                }
            },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });
<?php endif; ?>

    });
</script>
   
</body>
</html>