<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true || $_SESSION['userType'] !== 'customer') {
    // Redirect to login page if not logged in or not a customer
    header("Location: ../auth/customer_login.php?error=not_logged_in");
    exit();
}

require_once '../../config/database.php';

$user_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 4;
$offset = ($page - 1) * $limit;

// Get total orders for pagination
$total_query = "SELECT COUNT(*) FROM orders WHERE customer_id = ?";
$total_stmt = $conn->prepare($total_query);
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_orders = $total_result->fetch_row()[0];
$total_pages = ceil($total_orders / $limit);
$total_stmt->close();

// Get orders with pagination
$order_query = "
    SELECT o.*, p.*, r.name AS restaurant_name, r.location AS restaurant_address
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN menu m ON oi.menu_id = m.menu_id
    JOIN payments p ON o.order_id = p.order_id
    JOIN restaurants r ON m.restaurant_id = r.restaurant_id
    WHERE customer_id = ? ORDER BY order_date DESC LIMIT ? OFFSET ?";
    if (!$order_query) {
        die("Query failed: " . $conn->error);
    }
$order_stmt = $conn->prepare($order_query);
if (!$order_stmt) {
    die("Query failed: " . $conn->error);
}
$order_stmt->bind_param("iii", $user_id, $limit, $offset);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$orders = $order_result->fetch_all(MYSQLI_ASSOC);
$order_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - G3 online food ordering system</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/order_history.css">
    <link rel="stylesheet" href="css/footer.css">
    
</head>
<body>

    <?php include_once __DIR__ . "/topbar.php";?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Order History</h1>
            <a href="profile.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
        
        <?php if (count($orders) > 0): ?>
            <div id="orders-container">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card" data-order-id="<?php echo $order['order_id']; ?>">
                        <div class="order-header" onclick="toggleOrderDetails(this)">
                            <div class="order-header-info">
                                <span class="order-id">Order #<?php echo $order['order_id']; ?></span>
                                <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>                                
                            </div>
                            <span class="order-date">
                                <?php echo date('M d, Y \a\t h:i A', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        
                        <div class="order-summary">
                            <div>
                                <div class="order-restaurant">From <span style="font-style: italic; color: #134D79FF; text-transform: capitalize; font-weight: bold; "><?=$order['restaurant_name']?></span></div>
                                <div class="order-items-count">
                                    <?php
                                    //get order items details fro this order
                                    $order_items_query = "
                                        SELECT oi.*, m.menu_id AS food_id, m.name AS menu_name, m.price AS menu_price, m.image AS menu_image,
                                        m.discount AS menu_discount
                                        FROM order_items oi
                                        JOIN menu m ON oi.menu_id = m.menu_id
                                        WHERE oi.order_id = ?";
                                    $order_items_stmt = $conn->prepare($order_items_query);    
                                    $order_items_stmt->bind_param("i", $order['order_id']);
                                    $order_items_stmt->execute();
                                    $order_items_result = $order_items_stmt->get_result();
                                    $order_items = $order_items_result->fetch_all(MYSQLI_ASSOC);
                                    $order_items_stmt->close();

                                    $items_count = count($order_items);
                                    echo $items_count . ' item' . ($items_count > 1 ? 's' : '');
                                    ?>
                                </div>
                            </div>
                            <div class="order-amount">$<?php echo number_format($order['amount'], 2); ?></div>
                        </div>
                        
                        <div class="order-details" id="details-<?php echo $order['order_id']; ?>">
                            <!-- Details will be loaded via AJAX -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item">
                        <a class="page-link <?php echo $i == $page ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="empty-title">No Orders Yet</h3>
                <p class="empty-text">You haven't placed any orders. Start exploring our delicious menu!</p>
                <a href="menu.php" class="btn btn-primary">
                    <i class="fas fa-utensils"></i> Browse Menu
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . "/footer.php";?>
    
    <script>
        // Toggle order details
        function toggleOrderDetails(header) {
            const orderCard = header.parentElement;
            const detailsDiv = orderCard.querySelector('.order-details');
            const orderId = orderCard.getAttribute('data-order-id');
            
            if (detailsDiv.classList.contains('active')) {
                detailsDiv.classList.remove('active');
            } else {
                // Load details via AJAX if not already loaded
                if (detailsDiv.innerHTML.trim() === '') {
                    loadOrderDetails(orderId, detailsDiv);
                }
                detailsDiv.classList.add('active');
            }
        }
        
        // Load order details via AJAX
        function loadOrderDetails(orderId, targetElement) {
            targetElement.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Loading order details...</p>
                </div>
            `;
            
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `ajax_get_order_details.php?order_id=${orderId}`, true);
            
            xhr.onload = function() {
                if (this.status === 200) {
                    targetElement.innerHTML = this.responseText;
                } else {
                    targetElement.innerHTML = `
                        <div class="empty-state" style="padding: 20px;">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Failed to load order details. Please try again.</p>
                        </div>
                    `;
                }
            };
            
            xhr.onerror = function() {
                targetElement.innerHTML = `
                    <div class="empty-state" style="padding: 20px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Network error. Please check your connection.</p>
                    </div>
                `;
            };
            
            xhr.send();
        }
        
        // Load details for first order automatically
        document.addEventListener('DOMContentLoaded', function() {
            const firstOrder = document.querySelector('.order-card');
            if (firstOrder) {
                const firstOrderId = firstOrder.getAttribute('data-order-id');
                const firstDetailsDiv = document.getElementById(`details-${firstOrderId}`);
                loadOrderDetails(firstOrderId, firstDetailsDiv);
                firstDetailsDiv.classList.add('active');
            }
        });
    </script>
</body>
</html>