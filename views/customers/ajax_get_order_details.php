    <?php
    session_start();
    require_once '../../config/database.php';

    if (!isset($_SESSION['user_id'])) {
        die(json_encode(['error' => 'Unauthorized']));
    }

    $order_id = $_GET['order_id'] ?? 0;

    // Get order details
    $order_query = "
        SELECT o.*, p.*, r.name AS restaurant_name, r.location AS restaurant_address
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN menu m ON oi.menu_id = m.menu_id
        JOIN payments p ON o.order_id = p.order_id
        JOIN restaurants r ON m.restaurant_id = r.restaurant_id
        WHERE o.customer_id = ? AND o.order_id = ?";
        if (!$order_query) {
            die("Query failed: " . $conn->error);
        }
    $order_stmt = $conn->prepare($order_query);
    if (!$order_stmt) {
        die("Query failed: " . $conn->error);
    }
    $order_stmt->bind_param("ii", $_SESSION['user_id'], $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order = $order_result->fetch_assoc();
    $order_stmt->close();

    if (!$order) {
        die('<div class="empty-state" style="padding: 20px;">
            <i class="fas fa-exclamation-circle"></i>
            <p>Order not found or you don\'t have permission to view it.</p>
        </div>');
    }

    //get order items details fro this order
    $order_items_query = "
        SELECT oi.*, m.menu_id AS food_id, m.name AS menu_name, m.price AS menu_price, m.image AS menu_image, m.discount AS menu_discount
        FROM order_items oi
        JOIN menu m ON oi.menu_id = m.menu_id
        WHERE oi.order_id = ?";
    $order_items_stmt = $conn->prepare($order_items_query);
    $order_items_stmt->bind_param("i", $order['order_id']);
    $order_items_stmt->execute();
    $order_items_result = $order_items_stmt->get_result();
    $items = $order_items_result->fetch_all(MYSQLI_ASSOC);
    $order_items_stmt->close();

    // Get delivery address
    $address_stmt = $conn->prepare("
    SELECT * FROM customer_delivery_address
    WHERE user_id = ?
    ");
    $address_stmt->bind_param("i", $_SESSION['user_id']);
    $address_stmt->execute(); // Step 1: Execute the statement
    $result = $address_stmt->get_result(); // Step 2: Get the result
    $address = $result->fetch_assoc(); // Step 3: Fetch as associative array
    $address_stmt->close();

    ?>

    <div class="detail-section">
        <h3 class="detail-title"><i class="fas fa-utensils"></i> Order Items</h3>
        <table class="items-list">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $discount = 0;
                $total = 0;
                foreach ($items as $item):?>
                    <?php
                    // Calculate the total price for each item
                    $discount += $item['menu_discount'] / 100 * $item['quantity'];
                    $total += ($item['menu_price'] * $item['quantity']) - ($item['menu_discount'] / 100 * $item['quantity']);
                    ?>
                    <tr>
                        <td class="item-name"><img style="border-radius: 10px;" src="../../uploads/menu_images/<?php echo $item['menu_image']; ?>" alt="menu_img" width="60px" height="50px"></td>
                        <td class="item-name"><?php echo htmlspecialchars($item['menu_name']); ?></td>
                        <td class="item-price">ETB<?php echo number_format($item['menu_price'], 2); ?></td>
                        <td class="item-quantity"><?php echo $item['quantity']; ?></td>
                        <td class="item-total">ETB<?php echo number_format(($item['menu_price'] * $item['quantity']) - ($item['menu_discount'] / 100 * $item['quantity']), 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="detail-section">
        <h3 class="detail-title"><i class="fas fa-map-marker-alt"></i> Delivery Information</h3>
        <div class="address-info">
            <div class="address-card">
                <div class="address-type">Delivery information</div>
                <div class="address-details">
                    <?php if (!$address): ?>
                        <?php echo htmlspecialchars($delivery['full_name']); ?><br>
                        <?php echo htmlspecialchars($delivery['street']); ?><br>
                        <?php echo htmlspecialchars($delivery['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> <?php echo htmlspecialchars($address['zip']); ?><br>
                        <?php echo htmlspecialchars($delivery['phone']); ?>
                    <?php else: ?>
                        Address information not available
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="address-card">
                <div class="address-type">Delivery Instructions</div>
                <div class="address-details">
                    <i><?php echo $order['o_description'] ? nl2br(htmlspecialchars($order['o_description'])) : 'No special instructions'; ?></i>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h3 class="detail-title"><i class="fas fa-credit-card"></i> Payment Information</h3>
        <div class="payment-info">
            <div class="payment-card">
                <div class="payment-type">Payment Method</div>
                <div class="payment-details">
                    <?php if ($order): ?>
                        <?php if ($order['payment_method'] === 'card'): ?>
                            <i class="fas fa-credit-card"></i> Card ending in <?php echo substr($order['card_number'], -4); ?><br>
                            Exp: <?php echo $order['expiry']; ?>
                        <?php else: ?>
                            <i class="fas fa-money-bill-wave"></i> <?php echo ucfirst($order['payment_method']); ?>
                            <div class="screenshot-container">
                                <img class="screenshot-image" src="../../uploads/payments/<?=$order['payment_file']?>" alt="screenshot image">
                            </div>
                            <i><strong>Transaction Id: </strong> <?=$order['transaction_id']?></i>
                        <?php endif; ?>
                    <?php else: ?>
                        Payment information not available
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="payment-card">
                <div class="payment-type">Billing Address</div>
                <div class="payment-details">
                    <!-- <?php if ($order && $order['billing_address']): ?>
                        <?php echo nl2br(htmlspecialchars($payment['billing_address'])); ?>
                    <?php else: ?> -->
                        <i>
                            Same as delivery address
                        </i>
                    <!-- <?php endif; ?> -->
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h3 class="detail-title"><i class="fas fa-receipt"></i> Order Summary</h3>
        <div class="order-totals">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-value"> <?php echo number_format($total, 2); ?> birr</td>
                </tr>
                <tr>
                    <td class="total-label">Delivery Fee:</td>
                    <td class="total-value"> <?php echo number_format($order['amount'] - $total, 2); ?> birr</td>
                </tr>
                <!-- <tr>
                    <td class="total-label">Tax:</td>
                    <td class="total-value">ETB <?php echo number_format($order['tax_amount'], 2); ?></td>
                </tr> -->
                <tr>
                    <td class="total-label">Discount:</td>
                    <td class="total-value"> <?php echo number_format($discount, 2); ?> birr</td>
                </tr>
                <tr>
                    <td class="total-label">Total:</td>
                    <td class="total-value grand-total"> <?php echo number_format($order['amount'], 2); ?>birr</td>
                </tr>
                <tr>
                    <td>
                        <strong>Secret Code:</strong>
                    </td>
                    <td style="font-weight: bold; font-family: 'Courier New', Courier, monospace; letter-spacing: 2px; text-align: right;">
                        <i id="sc_code_value">
                            <?= $order['secret_code'] ?>
                            <i class="fa-solid fa-copy" onclick="copyToClipboard('sc_code_value')"></i>
                        </i>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="action-buttons">
        <button class="btn btn-outline">
            <i class="fas fa-print"></i> generate QR code
        </button>
        <button class="btn btn-outline" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <?php if ($order['status'] === 'pending' || $order['status'] === 'processing'): ?>
            <button class="btn btn-primary" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                <i class="fas fa-times"></i> Cancel Order
            </button>
        <?php endif; ?>
        <!-- <button class="btn btn-primary" onclick="reorderItems(<?php echo $order['order_id']; ?>)">
            <i class="fas fa-redo"></i> Reorder
        </button> -->
    </div>
