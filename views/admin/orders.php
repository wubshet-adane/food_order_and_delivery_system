<?php
require_once __DIR__ . "/../../models/update_orders_restaurant.php";
$owner_id = $_SESSION['user_id'];

// Get all restaurants owned by this user
$restaurants_stmt = $conn->prepare("SELECT * FROM restaurants");
//$restaurants_stmt->bind_param("i", $owner_id);
$restaurants_stmt->execute();
$restaurants_result = $restaurants_stmt->get_result();

// Initialize arrays to store orders
$pending_orders = [];
$active_orders = [];
$delivered_orders = [];

if ($restaurants_result->num_rows > 0) {
    while ($restaurant = $restaurants_result->fetch_assoc()) {
        
        // Get pending orders for this restaurant
        $pending_stmt = $conn->prepare("
            SELECT o.*, p.*, cda.name AS customer_name, cda.phone AS customer_phone, cda.email AS customer_email, cda.delivery_address 
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
            JOIN payments p ON o.order_id = p.order_id
            WHERE o.status = 'Pending'
            ORDER BY o.order_date DESC
        ");
        if ($pending_stmt === false) {
            die("Prepare failed: " . $conn->error); // helpful debugging message
        }
        $pending_stmt->execute();
        $pending_result = $pending_stmt->get_result();
        
        while ($order = $pending_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $pending_orders[] = $order;
        }
        
        // Get active orders (Accepted, Preparing, Ready_for_Delivery) for this restaurant
        $active_stmt = $conn->prepare("
            SELECT o.*, p.*, cda.name AS customer_name, cda.phone AS customer_phone, cda.email AS customer_email, cda.delivery_address
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
            JOIN payments p ON o.order_id = p.order_id
            WHERE o.status IN ('Accepted', 'Preparing', 'Ready_for_Delivery', 'Delivering')
            ORDER BY
                CASE
                    WHEN o.status = 'Accepted' THEN 1
                    WHEN o.status = 'Preparing' THEN 2
                    WHEN o.status = 'Ready_for_Delivery' THEN 3
                    WHEN o.status = 'Delivering' THEN 4
                    ELSE 5
                END,
                o.order_date DESC
        ");
        $active_stmt->execute();
        $active_result = $active_stmt->get_result();
        
        while ($order = $active_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $active_orders[] = $order;
        }
        
        // Get delivered orders for this restaurant
        $delivered_stmt = $conn->prepare("
            SELECT o.*, p.*, cda.name AS customer_name, cda.phone AS customer_phone, cda.email AS customer_email, cda.delivery_address 
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
            JOIN payments p ON o.order_id = p.order_id
            WHERE o.status = 'Delivered'
            ORDER BY o.order_date DESC
        ");
        $delivered_stmt->execute();
        $delivered_result = $delivered_stmt->get_result();
        
        while ($order = $delivered_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $delivered_orders[] = $order;
        }

        // Get delivered orders for this restaurant
        $cancelled_stmt = $conn->prepare("
            SELECT o.*, p.*, cda.name AS customer_name, cda.phone AS customer_phone, cda.email AS customer_email, cda.delivery_address 
            FROM orders o
            JOIN users u ON o.customer_id = u.user_id
            JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
            JOIN payments p ON o.order_id = p.order_id
            WHERE o.status = 'Cancelled'
            ORDER BY o.order_date DESC
        ");
        $cancelled_stmt->execute();
        $cancelled_result = $cancelled_stmt->get_result();
        
        while ($order = $cancelled_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $cancelled_orders[] = $order;
        }
    }
}
?>

    <div class="order_container">
        <div class="tabs_header">
            <h1>Orders Management</h1>
            
            <div class="responce_message">
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="tabs" id="ordersTab">
                <button class="tab-button active" onclick="openTab('pending')">
                    New Orders <span class="badge bg-secondary"><?php echo count($pending_orders); ?></span>
                </button>
                <button class="tab-button" onclick="openTab('active')">
                    Active Orders <span class="badge bg-primary"><?php echo count($active_orders); ?></span>
                </button>
                <button class="tab-button" onclick="openTab('delivered')">
                    Delivered Orders <span class="badge bg-success"><?php echo count($delivered_orders); ?></span>
                </button>
                <button class="tab-button" onclick="openTab('cancelled')">
                    Canceled Orders <span class="badge bg-danger"><?php echo count($cancelled_orders); ?></span>
                </button>
            </div>
        </div>
        
        <!-- Pending Orders Tab -->
        <div id="pending" class="tab-content" style="display: block;">
            <?php if (empty($pending_orders)): ?>
                <div class="alert alert-info"><img src="../../public/images/no order.jpg" alt="No delivered orders yet."></div>
            <?php else: ?>
                <div class="accordion" id="pendingAccordion">
                    <?php foreach ($pending_orders as $index => $order): ?>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" onclick="toggleAccordion('pendingCollapse<?php echo $index; ?>')">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <div>
                                            <span class="fw-bold">Order #<?php echo $order['order_id']; ?></span>
                                            <span class="ms-3 badge status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
                                            <span class="new_order">New</span>
                                        </div>
                                        <div>
                                            <span class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div id="pendingCollapse<?php echo $index; ?>" class="accordion-collapse">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h5>Customer Information</h5>
                                            <p><strong>Name:</strong> &nbsp;&nbsp;<span><?php echo $order['customer_name']; ?></span></p>
                                            <p><strong>Phone:</strong> &nbsp;&nbsp;<span><?php echo $order['customer_phone']; ?></span></p>
                                            <p><strong>Email:</strong> &nbsp;&nbsp;<span><?php echo $order['customer_email']; ?></span></p>
                                            <p><strong>Delivery address:</strong> &nbsp;&nbsp;<span><?php echo $order['delivery_address']; ?></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Restaurant</h5>
                                            <p>&nbsp;&nbsp;<span><?php echo $order['restaurant_name']; ?></span></p>
                                            <p><strong>Total:</strong> &nbsp;&nbsp; <span><?php echo number_format($order['amount'], 2); ?></span> ETB</p>
                                            <p><strong>Payment method</strong>&nbsp;&nbsp; <span><?php echo $order['payment_method']?></span></p>
                                            <div class="resizable_payment_screenshot" id="resizable"><img src="../../uploads/payments/<?php echo $order['payment_file']?>" alt="<?php echo $order['payment_file'] . 'payment screenshot'?>"></div>
                                            <p><strong>Transaction id:</strong>&nbsp;&nbsp; <span style="font-family: 'Courier New', Courier, monospace;"> <?php echo $order['transaction_id']; ?></span></p>
                                        </div>
                                    </div>
                                    
                                    <h5 class="mt-3">Order Items</h5>
                                    <?php
                                    $items_stmt = $conn->prepare("
                                        SELECT m.name, m.price, oi.quantity 
                                        FROM order_items oi
                                        JOIN menu m ON oi.menu_id = m.menu_id
                                        WHERE oi.order_id = ?
                                    ");
                                    $items_stmt->bind_param("i", $order['order_id']);
                                    $items_stmt->execute();
                                    $items_result = $items_stmt->get_result();
                                    
                                    while ($item = $items_result->fetch_assoc()):
                                    ?>
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <?php echo $item['name']; ?>
                                                    <span class="text-muted">x<?php echo $item['quantity']; ?></span>
                                                </div>
                                                <div>
                                                    <?php echo number_format($item['price'] * $item['quantity'], 2); ?> ETB
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    
                                    <div class="d-flex justify-content-end mt-3">
                                        <form method="post" class="me-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger">Reject Order</button>
                                        </form>
                                        <form method="post">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="btn btn-success">Accept Order</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Active Orders Tab -->
        <div id="active" class="tab-content" style="display: none;">
            <?php if (empty($active_orders)): ?>
                <div class="alert alert-info"><img src="../../public/images/no order.jpg" alt="No delivered orders yet."></div>
            <?php else: ?>
                <div class="accordion" id="activeAccordion">
                    <?php foreach ($active_orders as $index => $order): ?>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" onclick="toggleAccordion('activeCollapse<?php echo $index; ?>')">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <div>
                                            <span class="fw-bold">Order #<?php echo $order['order_id']; ?></span>
                                            <span class="ms-3 badge status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
                                        </div>
                                        <div>
                                            <span class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div id="activeCollapse<?php echo $index; ?>" class="accordion-collapse">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h5>Customer Information</h5>
                                            <p><strong>Name:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_name']; ?></span></p>
                                            <p><strong>Phone:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_phone']; ?></span></p>
                                            <p><strong>Email:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_email']; ?></span></p>
                                            <p><strong>Delivery address:</strong> &nbsp;&nbsp;<span><?php echo $order['delivery_address']; ?></span></p>
                                            <p><strong>Order description:</strong> &nbsp;&nbsp;<span><?php echo $order['o_description']; ?></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Restaurant</h5>
                                            <p><?php echo $order['restaurant_name']; ?></p>
                                            <p><strong>Total:</strong> &nbsp;&nbsp; <span><?php echo number_format($order['amount'], 2); ?></span> ETB</p>
                                            <p><strong>Payment method</strong>&nbsp;&nbsp; <span><?php echo $order['payment_method']?></span></p>
                                            <div class="resizable_payment_screenshot" id="resizable"><img src="../../uploads/payments/<?php echo $order['payment_file']?>" alt="<?php echo $order['payment_file'] . 'payment screenshot'?>"></div>
                                            <p><strong>Transaction id:</strong>&nbsp;&nbsp; <span style="font-family: 'Courier New', Courier, monospace;"> <?php echo $order['transaction_id']; ?></span></p>
                                        </div>
                                    </div>
                                    
                                    <h5 class="mt-3">Order Items</h5>
                                    <?php
                                    $items_stmt = $conn->prepare("
                                        SELECT m.name, m.price, oi.quantity 
                                        FROM order_items oi
                                        JOIN menu m ON oi.menu_id = m.menu_id
                                        WHERE oi.order_id = ?
                                    ");
                                    $items_stmt->bind_param("i", $order['order_id']);
                                    $items_stmt->execute();
                                    $items_result = $items_stmt->get_result();


                                    $i = 0;

                                    while ($item = $items_result->fetch_assoc()):
                                        $i++;
                                    ?>
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <?php echo $i. '. ' . $item['name']; ?>
                                                    <span class="text-muted">x<?php echo $item['quantity']; ?></span>&nbsp;&nbsp;&nbsp;
                                                    <span style="font-family:'Courier New', Courier, monospace;"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ETB</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Delivered Orders Tab -->
        <div id="delivered" class="tab-content" style="display: none;">
            <?php if (empty($delivered_orders)): ?>
                <div class="alert alert-info"><img src="../../public/images/no order.jpg" alt="No delivered orders yet."></div>
            <?php else: ?>
                <div class="accordion" id="deliveredAccordion">
                    <?php foreach ($delivered_orders as $index => $order): ?>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" onclick="toggleAccordion('deliveredCollapse<?php echo $index; ?>')">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <div>
                                            <span class="fw-bold">Order #<?php echo $order['order_id']; ?></span>
                                            <span class="ms-3 badge status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
                                        </div>
                                        <div>
                                            <span class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div id="deliveredCollapse<?php echo $index; ?>" class="accordion-collapse">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h5>Customer Information</h5>
                                            <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                                            <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                                            <p><strong>Email:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_email']; ?></span></p>
                                            <p><strong>Delivery address:</strong> &nbsp;&nbsp;<span><?php echo $order['delivery_address']; ?></span></p>
                                            <p><strong>Order description:</strong> &nbsp;&nbsp;<span><?php echo $order['o_description']; ?></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Restaurant</h5>
                                            <p><?php echo $order['restaurant_name']; ?></p>
                                            <p><strong>Total:</strong> &nbsp;&nbsp; <span><?php echo number_format($order['amount'], 2); ?></span> ETB</p>
                                            <p><strong>Payment method</strong>&nbsp;&nbsp; <span><?php echo $order['payment_method']?></span></p>
                                            <div class="resizable_payment_screenshot" id="resizable"><img src="../../uploads/payments/<?php echo $order['payment_file']?>" alt="<?php echo $order['payment_file'] . 'payment screenshot'?>"></div>
                                            <p><strong>Transaction id:</strong>&nbsp;&nbsp; <span style="font-family: 'Courier New', Courier, monospace;"> <?php echo $order['transaction_id']; ?></span></p>
                                        </div>
                                    </div>
                                    
                                    <h5 class="mt-3">Order Items</h5>
                                    <?php
                                    $items_stmt = $conn->prepare("
                                        SELECT m.name, m.price, oi.quantity 
                                        FROM order_items oi
                                        JOIN menu m ON oi.menu_id = m.menu_id
                                        WHERE oi.order_id = ?
                                    ");
                                    $items_stmt->bind_param("i", $order['order_id']);
                                    $items_stmt->execute();
                                    $items_result = $items_stmt->get_result();
                                    
                                    while ($item = $items_result->fetch_assoc()):
                                    ?>
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <?php echo $item['name']; ?>
                                                    <span class="text-muted">x<?php echo $item['quantity']; ?></span>
                                                </div>
                                                <div>
                                                    <?php echo number_format($item['price'] * $item['quantity'], 2); ?> ETB
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

         <!-- Active Orders Tab -->
        <div id="cancelled" class="tab-content" style="display: none;">
            <?php if (empty($cancelled_orders)): ?>
                <div class="alert alert-info"><img src="../../public/images/no order.jpg" alt="No delivered orders yet."></div>
            <?php else: ?>
                <div class="accordion" id="activeAccordion">
                    <?php foreach ($cancelled_orders as $index => $order): ?>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" onclick="toggleAccordion('activeCollapse<?php echo $index; ?>')">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <div>
                                            <span class="fw-bold">Order #<?php echo $order['order_id']; ?></span>
                                            <span class="ms-3 badge status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
                                        </div>
                                        <div>
                                            <span class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div id="activeCollapse<?php echo $index; ?>" class="accordion-collapse">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h5>Customer Information</h5>
                                            <p><strong>Name:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_name']; ?></span></p>
                                            <p><strong>Phone:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_phone']; ?></span></p>
                                            <p><strong>Email:</strong> <span>&nbsp;&nbsp;<?php echo $order['customer_email']; ?></span></p>
                                            <p><strong>Delivery address:</strong> &nbsp;&nbsp;<span><?php echo $order['delivery_address']; ?></span></p>
                                            <p><strong>Order description:</strong> &nbsp;&nbsp;<span><?php echo $order['o_description']; ?></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Restaurant</h5>
                                            <p><?php echo $order['restaurant_name']; ?></p>
                                            <p><strong>Total:</strong> &nbsp;&nbsp; <span><?php echo number_format($order['amount'], 2); ?></span> ETB</p>
                                            <p><strong>Payment method</strong>&nbsp;&nbsp; <span><?php echo $order['payment_method']?></span></p>
                                            <div class="resizable_payment_screenshot" id="resizable"><img src="../../uploads/payments/<?php echo $order['payment_file']?>" alt="<?php echo $order['payment_file'] . 'payment screenshot'?>"></div>
                                            <p><strong>Transaction id:</strong>&nbsp;&nbsp; <span style="font-family: 'Courier New', Courier, monospace;"> <?php echo $order['transaction_id']; ?></span></p>
                                        </div>
                                    </div>
                                    
                                    <h5 class="mt-3">Order Items</h5>
                                    <?php
                                    $items_stmt = $conn->prepare("
                                        SELECT m.name, m.price, oi.quantity 
                                        FROM order_items oi
                                        JOIN menu m ON oi.menu_id = m.menu_id
                                        WHERE oi.order_id = ?
                                    ");
                                    $items_stmt->bind_param("i", $order['order_id']);
                                    $items_stmt->execute();
                                    $items_result = $items_stmt->get_result();


                                    $i = 0;

                                    while ($item = $items_result->fetch_assoc()):
                                        $i++;
                                    ?>
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <?php echo $i. '. ' . $item['name']; ?>
                                                    <span class="text-muted">x<?php echo $item['quantity']; ?></span>&nbsp;&nbsp;&nbsp;
                                                    <span style="font-family:'Courier New', Courier, monospace;"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ETB</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
    </div>

    <script>
        // Tab functionality
        function openTab(tabName) {
            // Hide all tab content
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].style.display = 'none';
            }
            
            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName('tab-button');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show the current tab and add active class to the button
            document.getElementById(tabName).style.display = 'block';
            event.currentTarget.classList.add('active');
        }
        
        // Accordion functionality
        function toggleAccordion(collapseId) {
            const collapseElement = document.getElementById(collapseId);
            const button = event.currentTarget;
            
            // Toggle the collapsed class on the button
            button.classList.toggle('collapsed');
            
            // Toggle the show class on the collapse element
            collapseElement.classList.toggle('show');
        }
    </script>
