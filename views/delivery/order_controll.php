<?php
$deliveryPersonId = $_SESSION['user_id'];
// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['start_delivery'])) {
        $orderId = $_POST['order_id'];
        startDelivery($conn, $orderId, 'Delivering', $deliveryPersonId);
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

// confirm order to delivered by scanning secret code QR code from customers phone
// if( isset($_GET['request_name']) && $_GET['request_name'] == 'Ajax') {

//     $scanData = json_decode(file_get_contents("php://input"), true);
//     if($scanData){
//         $orderId = $scanData['order_id'] ?? null;
//         $secretCode = $scanData['secret_code'] ?? null;

//         // Validation and logic
//         if ($orderId && $secretCode) {
//             $stmt = $conn->prepare("SELECT secret_code FROM orders WHERE order_id = ?");
//             $stmt->bind_param("i", $orderId);
//             $stmt->execute();
//             $result = $stmt->get_result();
//             $order = $result->fetch_assoc();

//             if ($order && $order['secret_code'] === $secretCode) {
//                 updateOrderStatus($conn, $orderId, 'Delivered', $deliveryPersonId);
//                 $success = true;
//             } else {
//                 $error = "❌ Invalid secret code or order ID.";
//             }
//         } else {
//             $error = "❌ Missing order_id or secret_code.";
//         }

//         // Send JSON response
//         header('Content-Type: application/json');
//         echo json_encode([
//             "success" => $success,
//             "error" => $error
//         ]);
//     }
// }

function updateOrderStatus($conn, $orderId, $status, $deliveryPersonId) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Step 1: Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ?, delivered_at = NOW() WHERE order_id = ? AND delivery_person_id = ?");
        $stmt->bind_param("sii", $status, $orderId, $deliveryPersonId);
        $stmt->execute();

        // Step 2: Only continue if status is 'delivered'
        if ($status === 'delivered') {
            // Step 3: Get delivery fee
            $feeStmt = $conn->prepare("SELECT delivery_person_fee FROM payments WHERE order_id = ?");
            $feeStmt->bind_param("i", $orderId);
            $feeStmt->execute();
            $feeResult = $feeStmt->get_result();

            if ($feeRow = $feeResult->fetch_assoc()) {
                $deliveryFee = $feeRow['delivery_person_fee'];

                // Step 4: Update delivery partner's balance
                $updateStmt = $conn->prepare("UPDATE delivery_partners SET balance = balance + ? WHERE user_id = ?");
                $updateStmt->bind_param("di", $deliveryFee, $deliveryPersonId);
                $updateStmt->execute();
            } else {
                throw new Exception("Delivery fee not found for order_id = $orderId");
            }
        }

        // If everything went fine, commit the transaction
        $conn->commit();
        return true;

    } catch (Exception $e) {
        // Something went wrong, rollback
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        return false;
    }
}


//create function to to start order to delivery
function startDelivery($conn, $orderId, $status, $deliveryPersonId) {
    // Check if there is already an ongoing delivery for this person
    $stmtCheck = $conn->prepare("
        SELECT 1 FROM orders
        WHERE delivery_person_id = ? AND status = ?
    ");
    $stmtCheck->bind_param("is", $deliveryPersonId, $status);
    $stmtCheck->execute();
    $stmtCheck->store_result(); // Needed to get num_rows
    if ($stmtCheck->num_rows > 0) {
        echo "<div id='alertBox' style='display: flex; max-width: 800px; justify-content: space-between; background-color:#FFB7B7FF; padding: 1rem 3rem; margin: auto; border-radius: 5px;'>
        <span>Update failed, another delivery in progress.</span>
        <span style='background-color:#ff990006; padding: 3px; border-radius: 3px; cursor: pointer;' onclick=\"this.parentElement.style.display='none'\">
            <i class='fa-solid fa-xmark'></i>
        </span>
      </div>
      <script>
        setTimeout(() => {
            const alert = document.getElementById('alertBox');
            if (alert) alert.style.display = 'none';
        }, 5000);
      </script>";
       // return $error;
    } else {
        // Proceed with updating the order
        $stmt = $conn->prepare("UPDATE orders SET status = ?, delivery_person_id = ?, assigned_at = now() WHERE order_id = ?");
        $stmt->bind_param("sii", $status, $deliveryPersonId, $orderId);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "<div style='display: flex; max-width: 800px; justify-content: space-between; background-color:#65BD6DFF; padding: 1rem 3rem; margin: auto; border-radius: 5px;'>
                <span>You are accept the order seccessfully.</span>
                <span style='background-color:#ff990006; padding: 3px; border-radius: 3px;cursor: pointer;' onclick=\"this.parentElement.style.display='none'\"><i class='fa-solid fa-xmark'></i></span>
            </div>";
        } else {
            echo "Update failed.";
            //return $error;
        }
        $stmt->close();
    }
    $stmtCheck->close();
}
// Get orders for this delivery person
$outForDelivery = allReadyForDeliveryOrders($conn, 'Ready_for_delivery');
$delivering = getOrdersByStatus($conn, 'Delivering', $deliveryPersonId);
$delivered = getOrdersByStatus($conn, 'Delivered', $deliveryPersonId);

function getOrdersByStatus($conn, $status, $deliveryPersonId) {
    $sql = "
        SELECT
            o.*,
            p.amount AS amount, 
            p.payment_method AS payment_method, 
            p.payment_file AS screenshot, 
            p.delivery_person_fee,
            cda.name as customer_name,
            cda.phone as customer_phone,
            cda.email as customer_email,
            cda.delivery_address as customer_address,
            cda.latitude as delivery_latitude,
            cda.longitude as delivery_longitude,
            r.name as restaurant_name,
            r.location as restaurant_address,
            r.phone as restaurant_phone,
            r.status as restaurant_status,
            r.latitude as restaurant_latitude,
            r.longitude as restaurant_longitude
        FROM orders o
        JOIN users u ON o.customer_id = u.user_id
        JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
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
function allReadyForDeliveryOrders($conn, $status) {
    $sql = "
        SELECT
            o.*,
            p.amount AS amount, 
            p.payment_method AS payment_method, 
            p.payment_file AS screenshot, 
            p.delivery_person_fee,
            cda.name as customer_name,
            cda.phone as customer_phone,
            cda.email as customer_email,
            cda.delivery_address as customer_address,
            cda.latitude as delivery_latitude,
            cda.longitude as delivery_longitude,
            r.name as restaurant_name,
            r.location as restaurant_address,
            r.phone as restaurant_phone,
            r.status as restaurant_status,
            r.latitude as restaurant_latitude,
            r.longitude as restaurant_longitude
        FROM orders o
        JOIN users u ON o.customer_id = u.user_id
        JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
        JOIN payments p ON o.order_id = p.order_id
        JOIN restaurants r ON o.restaurant_id = r.restaurant_id
        WHERE o.status = ?";
        
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $status);
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
                <i class="fas fa-box-open"></i> Ready for Delivery
            </div>
            <div class="tab" onclick="openTab('delivering')">
                <i class="fas fa-truck"></i> Delivering
            </div>
            <div class="tab" onclick="openTab('delivered')">
                <i class="fas fa-check-circle"></i> Delivered
            </div>
        </div>
        
        <div id="out-for-delivery" class="tab-content active">
            <h2>Orders Ready for Delivery</h2>
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
                                <span class="status-badge status-out">Ready for Delivery</span>
                            </div>
                            <div class="order-details">
                                <p><strong><i class="fas fa-user"></i> Customer Details:</strong> 
                                    <ul class="customer_detail">
                                        <li>Name: <?php echo htmlspecialchars($order['customer_name']); ?></li>
                                        <li>Email: <?php echo htmlspecialchars($order['customer_email']); ?></li>
                                        <li>Phone: <?php echo htmlspecialchars($order['customer_phone']); ?></li>
                                        <li>Address: <?php echo htmlspecialchars($order['customer_address']); ?></li>
                                    </ul>
                                </p>
                                <p><strong><i class="fas fa-store"></i> Restaurant Details: 
                                    <button class="action-btn"
                                        onclick="navigateTores(
                                            <?php echo $order['restaurant_latitude']; ?>,
                                            <?php echo $order['restaurant_longitude']; ?>
                                        )">
                                        <i class="fa-solid fa-location-dot" title="get restaurant location"></i>
                                    </button></strong> 
                                    <script>
                                        function navigateTores(restaurantLat, restaurantLng) {
                                            if (navigator.geolocation) {
                                                navigator.geolocation.getCurrentPosition(function (position) {
                                                    const currentLat = position.coords.latitude;
                                                    const currentLng = position.coords.longitude;

                                                    const url = `https://www.google.com/maps/dir/?api=1&origin=${currentLat},${currentLng}&destination=${restaurantLat},${restaurantLng}&travelmode=driving`;
                                                    window.open(url, '_blank');
                                                }, function (error) {
                                                    alert("Unable to get your current location. Please enable GPS.");
                                                    console.error(error);
                                                });
                                            } else {
                                                alert("Geolocation is not supported by this browser.");
                                            }
                                        }
                                    </script>
                                    <ul class="customer_detail">
                                        <li><strong>Restaurant name:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></li>
                                        <li>Restaurant address: <?php echo htmlspecialchars($order['restaurant_address']); ?></li>
                                        <li>Restaurant status: <?php echo htmlspecialchars($order['restaurant_status']); ?></li>
                                        <li>Restaurant contact: <?php echo htmlspecialchars($order['restaurant_phone']); ?></li>
                                    </ul>
                                </p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong><i class="fas fa-money-bill-wave"></i> Total Delivery fee:</strong> ETB <?php echo number_format($order['delivery_person_fee'], 2); ?></p>
                                
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
                                <button class="action-btn"
                                    onclick="navigatingTo(
                                        <?php echo $order['delivery_latitude']; ?>,
                                        <?php echo $order['delivery_longitude']; ?>,
                                        <?php echo $order['restaurant_latitude']; ?>,
                                        <?php echo $order['restaurant_longitude']; ?>
                                    )">
                                    <i class="fa-solid fa-location-dot"></i> Customer Location
                                </button>
                                <script>
                                    function navigatingTo(deliveryLat, deliveryLng, restaurantLat, restaurantLng) {
                                        const url = `https://www.google.com/maps/dir/?api=1&origin=${restaurantLat},${restaurantLng}&destination=${deliveryLat},${deliveryLng}&travelmode=driving`;
                                        window.open(url, '_blank');
                                    }
                                </script>
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
                                <p><strong><i class="fas fa-user"></i> Customer Details:</strong> 
                                    <ul class="customer_detail">
                                        <li>Name: <?php echo htmlspecialchars($order['customer_name']); ?></li>
                                        <li>Email: <?php echo htmlspecialchars($order['customer_email']); ?></li>
                                        <li>Phone: <?php echo htmlspecialchars($order['customer_phone']); ?></li>
                                        <li>Address: <?php echo htmlspecialchars($order['customer_address']); ?></li>
                                    </ul>
                                </p>
                                <p><strong><i class="fas fa-store"></i> Restaurant Details:</strong> 
                                    <ul class="customer_detail">
                                        <li>Restaurant name: <?php echo htmlspecialchars($order['restaurant_name']); ?></li>
                                        <li>Restaurant address: <?php echo htmlspecialchars($order['restaurant_name']); ?></li>
                                        <li>Restaurant contact: <?php echo htmlspecialchars($order['restaurant_phone']); ?></li>
                                        <li>Restaurant status: <?php echo htmlspecialchars($order['restaurant_status']); ?></li>
                                    </ul>
                                </p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong><i class="fas fa-money-bill-wave"></i> Total delivery fee:</strong> ETB <?php echo number_format($order['delivery_person_fee'], 2); ?></p>
                                
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
                                <button class="action-btn"
                                    onclick="navigateTo(
                                        <?php echo $order['delivery_latitude']; ?>,
                                        <?php echo $order['delivery_longitude']; ?>,
                                        <?php echo $order['restaurant_latitude']; ?>,
                                        <?php echo $order['restaurant_longitude']; ?>
                                    )">
                                    <i class="fa-solid fa-location-dot"></i> Customer Location
                                </button>
                                <script>
                                    function navigateTo(deliveryLat, deliveryLng, restaurantLat, restaurantLng) {
                                        const url = `https://www.google.com/maps/dir/?api=1&origin=${restaurantLat},${restaurantLng}&destination=${deliveryLat},${deliveryLng}&travelmode=driving`;
                                        window.open(url, '_blank');
                                    }
                                </script>
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
                                    <!-- Each row gets its own scan button with a unique order_id -->
                                    <button type="button" class="scanBtn btn-confirm btn" data-id="<?php echo $order['order_id']; ?>">Scan QR code</button>
                                    <!-- Place this only once in your page -->
                                    <div id="reader" style="display:none; margin: auto; width: 300px; height: 300px;"></div>
                                    <div id="result"></div>                                 
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
                                <p><strong><i class="fas fa-user"></i> Customer Details:</strong> 
                                    <ul class="customer_detail">
                                        <li>Name: <?php echo htmlspecialchars($order['customer_name']); ?></li>
                                        <li>Email: <?php echo htmlspecialchars($order['customer_email']); ?></li>
                                        <li>Phone: <?php echo htmlspecialchars($order['customer_phone']); ?></li>
                                        <li>Address: <?php echo htmlspecialchars($order['customer_address']); ?></li>
                                    </ul>
                                </p>                                
                                <p><strong><i class="fas fa-store"></i> Restaurant Details:</strong> 
                                    <ul class="customer_detail">
                                        <li>Restaurant name: <?php echo htmlspecialchars($order['restaurant_name']); ?></li>
                                        <li>Restaurant address: <?php echo htmlspecialchars($order['restaurant_name']); ?></li>
                                        <li>Restaurant contact: <?php echo htmlspecialchars($order['restaurant_phone']); ?></li>
                                        <li>Restaurant status: <?php echo htmlspecialchars($order['restaurant_status']); ?></li>
                                    </ul>
                                </p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                <p><strong><i class="fas fa-money-bill-wave"></i> Total Delivery fee:</strong> ETB <?php echo number_format($order['delivery_person_fee'], 2); ?></p>
                                
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
   <script src="js/html5-qrcode.min.js"></script>
   <script src="js/QR code scanner.js"></script>
