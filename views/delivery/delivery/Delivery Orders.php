<?php
session_start();
include '../../config/database.php'; // Database connection
 

// Check if user is logged in and is a delivery person
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'delivery') {
    header('Location: login.php');
    exit;
}

$deliveryPersonId = $_SESSION['user_id'];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['start_delivery'])) {
        $orderId = $_POST['order_id'];
        updateOrderStatus($pdo, $orderId, 'Delivering', $deliveryPersonId);
    } elseif (isset($_POST['confirm_delivery'])) {
        $orderId = $_POST['order_id'];
        $secretCode = $_POST['secret_code'];
        
        // Verify secret code
        $stmt = $pdo->prepare("SELECT secret_code FROM orders WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if ($order && $order['secret_code'] === $secretCode) {
            updateOrderStatus($pdo, $orderId, 'Delivered', $deliveryPersonId);
            $success = "Delivery confirmed successfully!";
        } else {
            $error = "Invalid secret code. Please try again.";
        }
    }
}

function updateOrderStatus($pdo, $orderId, $status, $deliveryPersonId) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ? AND delivery_id = ?");
    $stmt->execute([$status, $orderId, $deliveryPersonId]);
}

// Get orders for this delivery person
$outForDelivery = getOrdersByStatus($pdo, 'Out for Delivery', $deliveryPersonId);
$delivering = getOrdersByStatus($pdo, 'Delivering', $deliveryPersonId);
$delivered = getOrdersByStatus($pdo, 'Delivered', $deliveryPersonId);

function getOrdersByStatus($pdo, $status, $deliveryPersonId) {
    $sql = "SELECT o.*, c.name as customer_name, r.name as restaurant_name 
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE o.status = ? AND o.delivery_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $deliveryPersonId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get order items for each order
    foreach ($orders as &$order) {
        $stmt = $pdo->prepare("SELECT m.name, oi.quantity, oi.price 
                              FROM order_items oi
                              JOIN menu m ON oi.menu_id = m.menu_id
                              WHERE oi.order_id = ?");
        $stmt->execute([$order['order_id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $orders;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #333;
        }
        .order-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .order-details {
            margin-bottom: 10px;
        }
        .order-items {
            margin-left: 20px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-start {
            background-color: #4CAF50;
            color: white;
        }
        .btn-confirm {
            background-color: #2196F3;
            color: white;
        }
        .confirmation-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .confirmation-form input {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f1f1f1;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            background-color: white;
            border: 1px solid #ddd;
            border-bottom: 1px solid white;
            margin-bottom: -1px;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status-out {
            background-color: #FF9800;
        }
        .status-delivering {
            background-color: #2196F3;
        }
        .status-delivered {
            background-color: #4CAF50;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delivery Dashboard</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="openTab('out-for-delivery')">Out for Delivery</div>
            <div class="tab" onclick="openTab('delivering')">Delivering</div>
            <div class="tab" onclick="openTab('delivered')">Delivered</div>
        </div>
        
        <div id="out-for-delivery" class="tab-content active">
            <h2>Orders Out for Delivery</h2>
            <?php if (empty($outForDelivery)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <?php foreach ($outForDelivery as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                            <span class="status-badge status-out">Out for Delivery</span>
                        </div>
                        <div class="order-details">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Total Price:</strong> ETB <?php echo htmlspecialchars($order['total_price']); ?></p>
                            <div class="order-items">
                                <p><strong>Items:</strong></p>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['name']); ?> - ETB <?php echo htmlspecialchars($item['price']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" name="start_delivery" class="btn btn-start">Start Delivery</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div id="delivering" class="tab-content">
            <h2>Orders Being Delivered</h2>
            <?php if (empty($delivering)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <?php foreach ($delivering as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                            <span class="status-badge status-delivering">Delivering</span>
                        </div>
                        <div class="order-details">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Total Price:</strong> ETB <?php echo htmlspecialchars($order['total_price']); ?></p>
                            <div class="order-items">
                                <p><strong>Items:</strong></p>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['name']); ?> - ETB <?php echo htmlspecialchars($item['price']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <button class="btn btn-confirm" onclick="showConfirmationForm(<?php echo $order['order_id']; ?>)">Confirm Delivery</button>
                        <div class="confirmation-form" id="confirmation-form-<?php echo $order['order_id']; ?>">
                            <form method="POST">
                                <p>Please enter the secret code provided by the customer:</p>
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <input type="text" name="secret_code" placeholder="Secret Code" required>
                                <button type="submit" name="confirm_delivery" class="btn btn-confirm">Submit</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div id="delivered" class="tab-content">
            <h2>Delivered Orders</h2>
            <?php if (empty($delivered)): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <?php foreach ($delivered as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                            <span class="status-badge status-delivered">Delivered</span>
                        </div>
                        <div class="order-details">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Delivered On:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p><strong>Total Price:</strong> ETB <?php echo htmlspecialchars($order['total_price']); ?></p>
                            <div class="order-items">
                                <p><strong>Items:</strong></p>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['name']); ?> - ETB <?php echo htmlspecialchars($item['price']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function openTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Deactivate all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Activate selected tab and content
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        function showConfirmationForm(orderId) {
            const form = document.getElementById(`confirmation-form-${orderId}`);
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }
    </script>

</body>
</html>