<?php
$deliveryPersonId = $_SESSION['user_id'];
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
    $sql = "
        SELECT o.*, p.amount AS amount, p.payment_method AS payment_method, p.payment_file AS screenshot, 
               u.name as customer_name, r.name as restaurant_name 
        FROM orders o
        JOIN users u ON o.customer_id = u.user_id
        JOIN payments p ON o.order_id = p.order_id
        JOIN restaurants r ON o.restaurant_id = r.restaurant_id
        WHERE o.status = ? AND o.delivery_person_id = ?";
        
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $status, $deliveryPersonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($orders as &$order) {
        $itemStmt = $conn->prepare("
            SELECT m.name, oi.quantity, m.price 
            FROM order_items oi
            JOIN menu m ON oi.menu_id = m.menu_id
            WHERE oi.order_id = ?
        ");
        if (!$itemStmt) {
            die("Inner prepare failed: " . $conn->error);
        }
        $itemStmt->bind_param("i", $order['order_id']);
        $itemStmt->execute();
        $itemResult = $itemStmt->get_result();
        $order['items'] = $itemResult->fetch_all(MYSQLI_ASSOC);
        $itemStmt->close(); // Always close prepared statements
    }

    $stmt->close(); // Close outer statement too
    return $orders;
}

?>


<div class="order_control_container">
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

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => el.style.display = 'none');
        }, 5000);
    </script>