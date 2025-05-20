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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Handle profile update
        $updateData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ];
        $userModel->updateUser($_SESSION['user_id'], $updateData);
        $user = $userModel->getUserById($_SESSION['user_id']); // Refresh user data
    } elseif (isset($_POST['change_password'])) {
        // Handle password change
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $userModel->changePassword($_SESSION['user_id'], $_POST['current_password'], $_POST['new_password']);
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
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .settings-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .order-item {
            border-left: 4px solid #28a745;
            margin-bottom: 15px;
        }
        .nav-pills .nav-link.active {
            background-color: #28a745;
        }
        .nav-pills .nav-link {
            color: #495057;
        }
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="container mb-5">
        <div class="settings-container">
            <h2 class="mb-4">User Settings</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="profile-tab" data-toggle="pill" href="#profile" role="tab">Profile</a>
                        <a class="nav-link" id="orders-tab" data-toggle="pill" href="#orders" role="tab">Order History</a>
                        <a class="nav-link" id="stats-tab" data-toggle="pill" href="#stats" role="tab">Statistics</a>
                        <a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab">Change Password</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <h4>Personal Information</h4>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Delivery Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>

                        <!-- Order History Tab -->
                        <div class="tab-pane fade" id="orders" role="tabpanel">
                            <h4>Your Order History</h4>
                            <?php if (empty($orders)): ?>
                                <p>You haven't placed any orders yet.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($orders as $order): ?>
                                        <div class="list-group-item order-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">Order #<?= $order['id'] ?></h5>
                                                <small><?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></small>
                                            </div>
                                            <p class="mb-1">
                                                <strong>Restaurant:</strong> <?= htmlspecialchars($order['restaurant_name']) ?><br>
                                                <strong>Items:</strong> <?= $order['item_count'] ?><br>
                                                <strong>Total:</strong> $<?= number_format($order['total_amount'], 2) ?>
                                            </p>
                                            <small>
                                                <strong>Status:</strong> 
                                                <span class="badge badge-<?= 
                                                    $order['status'] === 'Delivered' ? 'success' : 
                                                    ($order['status'] === 'Cancelled' ? 'danger' : 'warning') 
                                                ?>">
                                                    <?= $order['status'] ?>
                                                </span>
                                                <?php if (!empty($order['delivery_person'])): ?>
                                                    | <strong>Delivered by:</strong> <?= htmlspecialchars($order['delivery_person']) ?>
                                                <?php endif; ?>
                                            </small>
                                            <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Statistics Tab -->
                        <div class="tab-pane fade" id="stats" role="tabpanel">
                            <h4>Your Order Statistics</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <h5>Total Orders</h5>
                                        <h2><?= $stats['total_orders'] ?></h2>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <h5>Total Spent</h5>
                                        <h2>$<?= number_format($stats['total_spent'], 2) ?></h2>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <h5>Favorite Restaurant</h5>
                                        <h4><?= $stats['favorite_restaurant'] ?? 'N/A' ?></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-card mt-4">
                                <h5>Recent Activity</h5>
                                <canvas id="orderChart" height="100"></canvas>
                            </div>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <h4>Change Password</h4>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>

    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/chart.min.js"></script>
    <script>
        // Chart for order statistics
        <?php if (!empty($stats['monthly_data'])): ?>
        const ctx = document.getElementById('orderChart').getContext('2d');
        const orderChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($stats['monthly_data'])) ?>,
                datasets: [{
                    label: 'Orders per Month',
                    data: <?= json_encode(array_values($stats['monthly_data'])) ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>