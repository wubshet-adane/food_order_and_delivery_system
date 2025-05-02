<?php
$deliveryPersonId = $_SESSION['user_id'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['start_delivery'])) {
        $orderId = $_POST['order_id'];
        updateOrderStatus($conn, $orderId, 'Delivering', $deliveryPersonId);
    } elseif (isset($_POST['confirm_delivery'])) {
        $orderId = $_POST['order_id'];
        $secretCode = $_POST['secret_code'];
        
        // Verify secret code
        $stmt = $conn->prepare("SELECT secret_code FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        if ($order && $order['secret_code'] === $secretCode) {
            updateOrderStatus($conn, $orderId, 'Delivered', $deliveryPersonId);
            $success = "Delivery confirmed successfully!";
        } else {
            $error = "Invalid secret code. Please try again.";
        }
    }
}

function updateOrderStatus($conn, $orderId, $status, $deliveryPersonId) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ? AND delivery_person_id = ?");
    $stmt->bind_param("sii", $status, $orderId, $deliveryPersonId);
    $stmt->execute();
}

// Get orders for this delivery person
$outForDelivery = getOrdersByStatus($conn, 'Out for Delivery', $deliveryPersonId);
$delivering = getOrdersByStatus($conn, 'Delivering', $deliveryPersonId);
$delivered = getOrdersByStatus($conn, 'Delivered', $deliveryPersonId);

function getOrdersByStatus($conn, $status, $deliveryPersonId) {
    $sql = "SELECT o.*, p.*, u.name as customer_name, r.name as restaurant_name 
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            JOIN payments p ON o.order_id = p.order_id
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE o.status = ? AND o.delivery_person_id = ?";
            if(!$sql){
                die($conn->error);
            }
    $stmt = $conn->prepare($sql);
    if(!$stmt){
        die($conn->error);
    }
    $stmt->bind_param("si", $status, $deliveryPersonId);
    if(!$stmt){
        die($conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    
    foreach ($orders as &$order) {
        $stmt = $conn->prepare("
                SELECT m.name, oi.quantity, m.price 
                FROM order_items oi
                JOIN menu m ON oi.menu_id = m.menu_id
                WHERE oi.order_id = ?
            ");    
        $stmt->bind_param("i", $order['order_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $order['items'] = $result->fetch_all(MYSQLI_ASSOC);
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --dark: #212529;
            --light: #f8f9fa;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --white: #ffffff;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .tabs {
            display: flex;
            margin-bottom: 25px;
            border-bottom: 1px solid #ddd;
        }

        .tab {
            padding: 12px 24px;
            cursor: pointer;
            font-weight: 500;
            color: var(--gray);
            position: relative;
            transition: var(--transition);
        }

        .tab.active {
            color: var(--primary);
            font-weight: 600;
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .order-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .order-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-out {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .status-delivering {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .status-delivered {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .order-details {
            margin-bottom: 15px;
        }

        .order-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .order-details strong {
            font-weight: 500;
        }

        .order-items {
            margin-top: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .order-items p {
            font-weight: 500;
            margin-bottom: 10px;
        }

        .order-items ul {
            margin: 0;
            padding-left: 20px;
        }

        .order-items li {
            margin-bottom: 5px;
            font-size: 13px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-start {
            background-color: var(--primary);
            color: white;
        }

        .btn-start:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-confirm {
            background-color: #4caf50;
            color: white;
        }

        .btn-confirm:hover {
            background-color: #3d8b40;
            transform: translateY(-2px);
        }

        .confirmation-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: #f5f7fa;
            border-radius: 8px;
        }

        .confirmation-form p {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .confirmation-form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 200px;
            margin-right: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--gray-light);
            margin-bottom: 15px;
        }

        .empty-state p {
            color: var(--gray);
            margin: 0;
        }

        @media (max-width: 768px) {
            .order-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .tab {
                padding: 10px 15px;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-motorcycle"></i> Delivery Dashboard</h1>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="openTab('out-for-delivery')">
                <i class="fas fa-box-open"></i> Out for Delivery
            </div>
            <div class="tab" onclick="openTab('delivering')">
                <i class="fas fa-truck"></i> Delivering
            </div>
            <div class="tab" onclick="openTab('delivered')">
                <i class="fas fa-check-circle"></i> Delivered
            </div>
        </div>
        
        <div id="out-for-delivery" class="tab-content active">
            <h2>Orders Out for Delivery</h2>
            <?php if (empty($outForDelivery)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No orders currently out for delivery</p>
                </div>
            <?php else: ?>
                <div class="order-grid">
                    <?php foreach ($outForDelivery as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                                <span class="status-badge status-out">Out for Delivery</span>
                            </div>
                            <div class="order-details">
                                <p><strong><i class="fas fa-user"></i> Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong><i class="fas fa-store"></i> Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong><i class="fas fa-money-bill-wave"></i> Total:</strong> ETB <?php echo number_format($order['amount'], 2); ?></p>
                                
                                <div class="order-items">
                                    <p><strong><i class="fas fa-utensils"></i> Items:</strong></p>
                                    <ul>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <li><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['name']); ?> - ETB <?php echo number_format($item['price'], 2); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit" name="start_delivery" class="btn btn-start">
                                    <i class="fas fa-play"></i> Start Delivery
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="delivering" class="tab-content">
            <h2>Orders Being Delivered</h2>
            <?php if (empty($delivering)): ?>
                <div class="empty-state">
                    <i class="fas fa-truck"></i>
                    <p>No orders currently being delivered</p>
                </div>
            <?php else: ?>
                <div class="order-grid">
                    <?php foreach ($delivering as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                                <span class="status-badge status-delivering">Delivering</span>
                            </div>
                            <div class="order-details">
                                <p><strong><i class="fas fa-user"></i> Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong><i class="fas fa-store"></i> Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong><i class="fas fa-money-bill-wave"></i> Total:</strong> ETB <?php echo number_format($order['amount'], 2); ?></p>
                                
                                <div class="order-items">
                                    <p><strong><i class="fas fa-utensils"></i> Items:</strong></p>
                                    <ul>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <li><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['name']); ?> - ETB <?php echo number_format($item['price'], 2); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-confirm" onclick="showConfirmationForm(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-check-circle"></i> Confirm Delivery
                                </button>
                            </div>
                            <div class="confirmation-form" id="confirmation-form-<?php echo $order['order_id']; ?>">
                                <form method="POST">
                                    <p>Please enter the secret code provided by the customer:</p>
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <input type="text" name="secret_code" placeholder="Secret Code" required>
                                    <button type="submit" name="confirm_delivery" class="btn btn-confirm">
                                        <i class="fas fa-paper-plane"></i> Submit
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="delivered" class="tab-content">
            <h2>Delivered Orders</h2>
            <?php if (empty($delivered)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No orders have been delivered yet</p>
                </div>
            <?php else: ?>
                <div class="order-grid">
                    <?php foreach ($delivered as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                                <span class="status-badge status-delivered">Delivered</span>
                            </div>
                            <div class="order-details">
                                <p><strong><i class="fas fa-user"></i> Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong><i class="fas fa-store"></i> Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong><i class="fas fa-money-bill-wave"></i> Total:</strong> ETB <?php echo number_format($order['total_price'], 2); ?></p>
                                
                                <div class="order-items">
                                    <p><strong><i class="fas fa-utensils"></i> Items:</strong></p>
                                    <ul>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <li><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['name']); ?> - ETB <?php echo number_format($item['price'], 2); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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