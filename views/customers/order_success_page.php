<?php
session_start();
require '../../config/database.php'; // Include your database connection file


if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "restaurant" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    header("Location: ../auth/restaurant_login.php?message=Please enter correct credentials!");
    exit; // Stop execution after redirection
}

$owner_id = $_SESSION['user_id'];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['new_status'];
        
        // Validate status transition
        $valid_statuses = ['Accepted', 'Preparing', 'Out for Delivery'];
        $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ? AND restaurant_id IN (SELECT restaurant_id FROM restaurants WHERE owner_id = ?)");
        $stmt->bind_param("ii", $order_id, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            $current_status = $order['status'];
            
            // Check if the new status is valid
            if (in_array($new_status, $valid_statuses)) {
                // Update status
                $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
                $update_stmt->bind_param("si", $new_status, $order_id);
                $update_stmt->execute();
                
                if ($update_stmt->affected_rows > 0) {
                    $success_msg = "Order status updated successfully!";
                } else {
                    $error_msg = "Failed to update order status.";
                }
            } else {
                $error_msg = "Invalid status transition.";
            }
        } else {
            $error_msg = "Order not found or you don't have permission to update it.";
        }
    }
    
    // Handle accept/reject actions
    if (isset($_POST['action'])) {
        $order_id = $_POST['order_id'];
        $action = $_POST['action'];
        
        // Verify the order belongs to this owner's restaurant
        $stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND restaurant_id IN (SELECT restaurant_id FROM restaurants WHERE owner_id = ?)");
        $stmt->bind_param("ii", $order_id, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            if ($action == 'accept') {
                $new_status = 'Accepted';
            } elseif ($action == 'reject') {
                $new_status = 'Cancelled';
            }
            
            $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $update_stmt->bind_param("si", $new_status, $order_id);
            $update_stmt->execute();
            
            if ($update_stmt->affected_rows > 0) {
                $success_msg = "Order has been " . ($action == 'accept' ? "accepted" : "rejected") . "!";
            } else {
                $error_msg = "Failed to update order status.";
            }
        } else {
            $error_msg = "Order not found or you don't have permission to modify it.";
        }
    }
}

// Get all restaurants owned by this user
$restaurants_stmt = $conn->prepare("SELECT * FROM restaurants WHERE owner_id = ?");
$restaurants_stmt->bind_param("i", $owner_id);
$restaurants_stmt->execute();
$restaurants_result = $restaurants_stmt->get_result();

// Initialize arrays to store orders
$pending_orders = [];
$active_orders = [];
$delivered_orders = [];

if ($restaurants_result->num_rows > 0) {
    while ($restaurant = $restaurants_result->fetch_assoc()) {
        $restaurant_id = $restaurant['restaurant_id'];
        
        // Get pending orders for this restaurant
        $pending_stmt = $conn->prepare("
            SELECT o.*, c.name as customer_name, c.phone_number as customer_phone 
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.restaurant_id = ? AND o.status = 'Pending'
            ORDER BY o.order_date DESC
        ");
        $pending_stmt->bind_param("i", $restaurant_id);
        $pending_stmt->execute();
        $pending_result = $pending_stmt->get_result();
        
        while ($order = $pending_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $pending_orders[] = $order;
        }
        
        // Get active orders (Accepted, Preparing, Out for Delivery) for this restaurant
        $active_stmt = $conn->prepare("
            SELECT o.*, c.name as customer_name, c.phone_number as customer_phone 
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.restaurant_id = ? AND o.status IN ('Accepted', 'Preparing', 'Out for Delivery', 'Delivering')
            ORDER BY 
                CASE 
                    WHEN o.status = 'Accepted' THEN 1
                    WHEN o.status = 'Preparing' THEN 2
                    WHEN o.status = 'Out for Delivery' THEN 3
                    WHEN o.status = 'Delivering' THEN 4
                    ELSE 5
                END,
                o.order_date DESC
        ");
        $active_stmt->bind_param("i", $restaurant_id);
        $active_stmt->execute();
        $active_result = $active_stmt->get_result();
        
        while ($order = $active_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $active_orders[] = $order;
        }
        
        // Get delivered orders for this restaurant
        $delivered_stmt = $conn->prepare("
            SELECT o.*, c.name as customer_name, c.phone_number as customer_phone 
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.restaurant_id = ? AND o.status = 'Delivered'
            ORDER BY o.order_date DESC
        ");
        $delivered_stmt->bind_param("i", $restaurant_id);
        $delivered_stmt->execute();
        $delivered_result = $delivered_stmt->get_result();
        
        while ($order = $delivered_result->fetch_assoc()) {
            $order['restaurant_name'] = $restaurant['name'];
            $delivered_orders[] = $order;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Owner - Orders Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .status-Pending {
            background-color: #6c757d;
        }
        .status-Accepted {
            background-color: #0d6efd;
        }
        .status-Preparing {
            background-color: #fd7e14;
        }
        .status-Out for Delivery {
            background-color: #ffc107;
        }
        .status-Delivering {
            background-color: #20c997;
        }
        .status-Delivered {
            background-color: #198754;
        }
        .status-Cancelled {
            background-color: #dc3545;
        }
        .order-item {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Orders Management</h1>
        
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <ul class="nav nav-tabs mb-4" id="ordersTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    New Orders <span class="badge bg-secondary"><?php echo count($pending_orders); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                    Active Orders <span class="badge bg-primary"><?php echo count($active_orders); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab">
                    Delivered Orders <span class="badge bg-success"><?php echo count($delivered_orders); ?></span>
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="ordersTabContent">
            <!-- Pending Orders Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <?php if (empty($pending_orders)): ?>
                    <div class="alert alert-info">No pending orders at this time.</div>
                <?php else: ?>
                    <div class="accordion" id="pendingAccordion">
                        <?php foreach ($pending_orders as $index => $order): ?>
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="pendingHeading<?php echo $index; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pendingCollapse<?php echo $index; ?>">
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
                                </h2>
                                <div id="pendingCollapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="pendingHeading<?php echo $index; ?>">
                                    <div class="accordion-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h5>Customer Information</h5>
                                                <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                                                <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Restaurant</h5>
                                                <p><?php echo $order['restaurant_name']; ?></p>
                                                <p><strong>Total:</strong> <?php echo number_format($order['total_price'], 2); ?> ETB</p>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-3">Order Items</h5>
                                        <?php
                                        // Get order items
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
            <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
                <?php if (empty($active_orders)): ?>
                    <div class="alert alert-info">No active orders at this time.</div>
                <?php else: ?>
                    <div class="accordion" id="activeAccordion">
                        <?php foreach ($active_orders as $index => $order): ?>
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="activeHeading<?php echo $index; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#activeCollapse<?php echo $index; ?>">
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
                                </h2>
                                <div id="activeCollapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="activeHeading<?php echo $index; ?>">
                                    <div class="accordion-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h5>Customer Information</h5>
                                                <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                                                <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Restaurant</h5>
                                                <p><?php echo $order['restaurant_name']; ?></p>
                                                <p><strong>Total:</strong> <?php echo number_format($order['total_price'], 2); ?> ETB</p>
                                                <?php if ($order['secret_code']): ?>
                                                    <p><strong>Secret Code:</strong> <?php echo $order['secret_code']; ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-3">Order Items</h5>
                                        <?php
                                        // Get order items
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
                                        
                                        <div class="mt-4">
                                            <h5>Update Order Status</h5>
                                            <form method="post">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                
                                                <div class="btn-group" role="group">
                                                    <?php if ($order['status'] == 'Accepted'): ?>
                                                        <input type="hidden" name="new_status" value="Preparing">
                                                        <button type="submit" name="update_status" class="btn btn-warning">Mark as Preparing</button>
                                                    <?php elseif ($order['status'] == 'Preparing'): ?>
                                                        <input type="hidden" name="new_status" value="Out for Delivery">
                                                        <button type="submit" name="update_status" class="btn btn-info">Mark as Out for Delivery</button>
                                                    <?php elseif ($order['status'] == 'Out for Delivery' || $order['status'] == 'Delivering'): ?>
                                                        <button class="btn btn-secondary" disabled>Awaiting Delivery Completion</button>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Delivered Orders Tab -->
            <div class="tab-pane fade" id="delivered" role="tabpanel" aria-labelledby="delivered-tab">
                <?php if (empty($delivered_orders)): ?>
                    <div class="alert alert-info">No delivered orders yet.</div>
                <?php else: ?>
                    <div class="accordion" id="deliveredAccordion">
                        <?php foreach ($delivered_orders as $index => $order): ?>
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="deliveredHeading<?php echo $index; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#deliveredCollapse<?php echo $index; ?>">
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
                                </h2>
                                <div id="deliveredCollapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="deliveredHeading<?php echo $index; ?>">
                                    <div class="accordion-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h5>Customer Information</h5>
                                                <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                                                <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Restaurant</h5>
                                                <p><?php echo $order['restaurant_name']; ?></p>
                                                <p><strong>Total:</strong> <?php echo number_format($order['total_price'], 2); ?> ETB</p>
                                                <?php if ($order['secret_code']): ?>
                                                    <p><strong>Secret Code:</strong> <?php echo $order['secret_code']; ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-3">Order Items</h5>
                                        <?php
                                        // Get order items
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>