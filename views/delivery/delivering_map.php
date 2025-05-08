<?php

$delivery_person_id = $_SESSION['user_id'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL query
$sql = "SELECT 
        cda.name,
        cda.phone,
        cda.email,
        cda.delivery_address,
        cda.latitude as delivery_latitude,
        cda.longitude as delivery_longitude,
        o.order_id,
        o.status,
        o.order_date,
        o.o_description
        r.name as restaurant_name,
        r.location as restaurant_address,
        r.phone as restaurant_phone,
        r.status as restaurant_status,
        r.latitude as restaurant_latitude,
        r.longitude as restaurant_longitude
    FROM orders o
    JOIN users u ON u.user_id = o.customer_id
    JOIN restaurants r ON o.restaurant_id = r.restaurant_id
    JOIN customer_delivery_address cda ON o.user_id = cda.user_id
    WHERE o.delivery_person_id = ? AND o.order_status IN ('preparing', 'out_for_delivery')
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
        <div id="map"></div>
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
                    <div class="delivery-card" data-lat="<?php echo $delivery['delivery_latitude']; ?>" 
                         data-lng="<?php echo $delivery['delivery_longitude']; ?>" 
                         data-order-id="<?php echo $delivery['order_id']; ?>">
                        <h3>Order #<?php echo $delivery['order_id']; ?></h3>
                        <span class="status <?php echo $delivery['order_status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $delivery['order_status'])); ?>
                        </span>
                        
                        <div class="customer-info">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($delivery['customer_name']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($delivery['customer_phone']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($delivery['delivery_address']); ?></p>
                        </div>
                        
                        <button class="action-btn" onclick="navigateTo(<?php echo $delivery['delivery_latitude']; ?>, <?php echo $delivery['delivery_longitude']; ?>)">
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
        // Initialize the map
        const map = L.map('map').setView([3.1390, 101.6869], 12); // Default to Kuala Lumpur coordinates
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add markers for each delivery
        const deliveryCards = document.querySelectorAll('.delivery-card');
        const markers = [];
        
        deliveryCards.forEach(card => {
            const lat = parseFloat(card.dataset.lat);
            const lng = parseFloat(card.dataset.lng);
            const orderId = card.dataset.orderId;
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(`Order #${orderId}`);
                markers.push(marker);
                
                // Center map when clicking on a delivery card
                card.addEventListener('click', () => {
                    map.setView([lat, lng], 15);
                    marker.openPopup();
                });
            }
        });

        // Fit map to show all markers if there are any
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.2));
        }

        // Function to open navigation in Google Maps
        function navigateTo(lat, lng) {
            window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`);
        }
    </script>
