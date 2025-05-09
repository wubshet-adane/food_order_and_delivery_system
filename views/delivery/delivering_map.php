<?php

$delivery_person_id = $_SESSION['user_id'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL query
$sql = "
SELECT  cda.name as customer_name,
        cda.phone as customer_phone,
        cda.email as customer_email,
        cda.delivery_address,
        cda.latitude as delivery_latitude,
        cda.longitude as delivery_longitude,
        o.order_id,
        o.status as order_status,
        o.order_date,
        o.o_description,
        r.name as restaurant_name,
        r.location as restaurant_address,
        r.phone as restaurant_phone,
        r.status as restaurant_status,
        r.latitude as restaurant_latitude,
        r.longitude as restaurant_longitude
    FROM orders o
    JOIN users u ON u.user_id = o.customer_id
    JOIN restaurants r ON o.restaurant_id = r.restaurant_id
    JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
    WHERE o.delivery_person_id = ? AND o.status IN ('preparing', 'out_for_delivery')
    ORDER BY o.order_date ASC
";
if(!$sql){
    die($conn->error);
}
// Prepare the statement
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $delivery_person_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all rows as an associative array
    $deliveries = [];
    while ($row = $result->fetch_assoc()) {
        $deliveries[] = $row;
    }

    // Free result and close statement
    $result->free();
    $stmt->close();
} else {
    die("Query preparation failed: " . $conn->error);
}

// Close connection
$conn->close();
?>
   <div class="map_container">
        <!-- <div id="map"></div> -->
        <div class="delivery-list">
            <div class="header">
                <h2>Your Delivery Assignments</h2>
                <p>You have <?php echo count($deliveries); ?> active deliveries</p>
            </div>
            
            <?php if (empty($deliveries)): ?>
                <div class="delivery-card">
                    <h3>No Active Deliveries</h3>
                    <p>You currently don't have any assigned deliveries.</p>
                </div>
            <?php else: ?>
                <?php foreach ($deliveries as $delivery): ?>
                    <div class="delivery-card" data-lat="<?php echo $delivery['delivery_latitude']; ?>">
                        <h3>Order #<?php echo $delivery['order_id']; ?></h3>
                        <span class="status <?php echo $delivery['order_status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $delivery['order_status'])); ?>
                        </span>
                        
                        <div class="customer-info">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($delivery['customer_name']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($delivery['customer_phone']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($delivery['delivery_address']); ?></p>
                        </div>
                        
                        <button class="action-btn"
                            onclick="navigateTo(
                                <?php echo $delivery['delivery_latitude']; ?>,
                                <?php echo $delivery['delivery_longitude']; ?>,
                                <?php echo $delivery['restaurant_latitude']; ?>,
                                <?php echo $delivery['restaurant_longitude']; ?>
                            )">
                            Get Directions
                        </button>
                        
                        <?php if ($delivery['order_status'] == 'preparing'): ?>
                            <form action="update_status.php" method="post" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $delivery['order_id']; ?>">
                                <input type="hidden" name="status" value="out_for_delivery">
                                <button type="submit" class="action-btn">Start Delivery</button>
                            </form>
                        <?php else: ?>
                            <form action="update_status.php" method="post" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $delivery['order_id']; ?>">
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="action-btn">Mark as Delivered</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        function navigateTo(deliveryLat, deliveryLng, restaurantLat, restaurantLng) {
            const url = `https://www.google.com/maps/dir/?api=1&origin=${restaurantLat},${restaurantLng}&destination=${deliveryLat},${deliveryLng}&travelmode=driving`;
            window.open(url, '_blank');
        }
    </script>
