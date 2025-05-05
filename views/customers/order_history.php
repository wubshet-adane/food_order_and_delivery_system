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
$limit = 8;
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
    SELECT o.*, p.amount AS total_amount, p.delivery_person_fee, r.name AS restaurant_name, r.location AS restaurant_address
    FROM orders o
    JOIN payments p ON o.order_id = p.order_id
    JOIN restaurants r ON o.restaurant_id = r.restaurant_id
    WHERE customer_id = ? ORDER BY order_date DESC LIMIT ? OFFSET ?";
$order_stmt = $conn->prepare($order_query);
if ($order_stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
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
            <a href="customer_profile_page.php" class="back-btn">
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
                                <div class="order-restaurant">From <span style="font-style: italic; color: #134D79FF; text-transform: capitalize; font-weight: bold; "><?=$order['restaurant_name']?></span> 
                                    <!--QR generate btn-->
                                    <button onclick="document.getElementById('qrModal_<?=$order['order_id']?>').classList.remove('QR_hide')" 
                                            class="QR_modal_expand ">
                                        <i class="fas fa-qrcode mr-2"></i> Generate QR Code
                                    </button>
                                       <!-- QR Code Modal -->
                                    <div id="qrModal_<?=$order['order_id']?>" class=" fixed QR_hide">
                                        <div id="relative">
                                        <button onclick="document.getElementById('qrModal_<?=$order['order_id']?>').classList.add('QR_hide')" 
                                                class="QR_modal_close">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <h3>Order Verification</h3>
                                        <div class="qrimagebox">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= htmlspecialchars($order['secret_code']) ?>&size=200x200" 
                                                alt="QR Code" class="w-48 h-48">
                                        </div>
                                        <p class=" qr_info text-center text-gray-600 mb-2">Scan this QR code to verify your order</p>
                                        <p class="orderid text-center text-sm text-gray-500">Order #<?= $order['order_id'] ?></p>
                                        <div class="downloadqrbtn ">
                                            <button onclick="downloadQRCode()" 
                                                    class=" dawnloadbtn ">
                                            <i class="fas fa-download mr-2"></i> Download QR
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
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
                            <div class="order-amount"><?php echo number_format((($order['total_amount'] / 0.95) + ($order['delivery_person_fee'] / 0.97 )), 2); ?> birr</div>        
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
                <?php endif; 
                // Calculate the range of page numbers to display (centered around current page)
                $start = max(1, $page - 1);
                $end = min($total_pages, $page + 1);

                if ($start > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                    if ($start > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item">
                        <a class="page-link <?php echo $i == $page ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor;
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                }
                if ($page < $total_pages): ?>
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
    <script src="javaScript/order_history_detail_toggler.js"></script>
    <script src="javaScript/cancel_order.js"></script>
</body>
</html>