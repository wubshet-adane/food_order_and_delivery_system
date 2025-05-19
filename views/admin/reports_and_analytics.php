<?php

// Get date ranges
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('-7 days'));
$month_start = date('Y-m-d', strtotime('-30 days'));
$year_start = date('Y-m-d', strtotime('-1 year'));


// Order count breakdowns
$order_counts = [
    'today' => $conn->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = '$today'")->fetch_row()[0],
    'week' => $conn->query("SELECT COUNT(*) FROM orders WHERE order_date >= '$week_start'")->fetch_row()[0],
    'month' => $conn->query("SELECT COUNT(*) FROM orders WHERE order_date >= '$month_start'")->fetch_row()[0],
    'year' => $conn->query("SELECT COUNT(*) FROM orders WHERE order_date >= '$year_start'")->fetch_row()[0],
    'Delivered' => $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Delivered'")->fetch_row()[0],
    'Cancelled' => $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Cancelled'")->fetch_row()[0],
    'pending' => $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetch_row()[0]
];

// Top-selling items
$top_items_result = $conn->query(
 "SELECT u.name AS item_name, count(r.restaurant_id) as numberoforders, sum(p.amount) as total_sold, r.name as restaurant_name 
        FROM orders o
        -- JOIN menu m ON oi.menu_id = m.menu_id
        -- JOIN orders o ON o.order_id = o.order_id
        JOIN restaurants r ON r.restaurant_id = o.restaurant_id
        JOIN payments p ON p.order_id = o.order_id
        JOIN users u ON r.owner_id = u.user_id
        WHERE o.status = 'Delivered'
        GROUP BY r.restaurant_id
        ORDER BY numberoforders DESC
        LIMIT 10"
    );
$top_items = [];
while ($row = $top_items_result->fetch_assoc()) {
    $top_items[] = $row;
}

// Canceled orders log
$canceled_orders_result = $conn->query(
    "SELECT o.order_id, o.order_date, p.service_fee as total_amount, u.name as customer_name, o.status
     FROM orders o
     JOIN users u ON o.customer_id = u.user_id
     JOIN payments p ON o.order_id = p.order_id
     WHERE o.status = 'Cancelled'
     ORDER BY o.order_date DESC
     LIMIT 20"
);
if(!$canceled_orders_result){
    die($conn->error);
}
$canceled_orders = [];
while ($row = $canceled_orders_result->fetch_assoc()) {
    $canceled_orders[] = $row;
}

$conn->close();
?>

    <div class="admin-container">
        <h3><i class="fas fa-chart-line"></i> Analytics & Reports</h3>
        <!-- Order Count Breakdown -->
        <h3 class="section-title">Order Statistics</h3>
        <div class="dashboard-container">
            <div class="metric-card">
                <h3>Today's Orders</h3>
                <div class="metric-value"><?= $order_counts['today'] ?></div>
                <p>Orders placed today</p>
            </div>
            <div class="metric-card">
                <h3>Weekly Orders</h3>
                <div class="metric-value"><?= $order_counts['week'] ?></div>
                <p>Last 7 days</p>
            </div>
            <div class="metric-card">
                <h3>Monthly Orders</h3>
                <div class="metric-value"><?= $order_counts['month'] ?></div>
                <p>Last 30 days</p>
            </div>
            <div class="metric-card">
                <h3>Order Status</h3>
                <div class="metric-value">
                    <span class="badge badge-success"><?= $order_counts['Delivered'] ?> Completed</span><br>
                    <span class="badge badge-warning"><?= $order_counts['pending'] ?> Pending</span><br>
                    <span class="badge badge-danger"><?= $order_counts['Cancelled'] ?> Rejected</span>
                </div>
            </div>
        </div>
        
        <!-- Top Selling Items -->
        <div class="box-container">
            <div class="chart-header">
                <h3><i class="fas fa-utensils"></i> Leading Restaurants by Earnings:</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Restaurant_Owners</th>
                        <th>Number of orders</th>
                        <th>Total Revenue</th>
                        <th>restaurant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['numberoforders'] ?></td>
                        <td><?= number_format($item['total_sold'], 2) ?> birr</td>
                        <td><?= $item['restaurant_name']  ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Canceled Orders Log -->
        <div class="box-container">
            <div class="chart-header">
                <h3><i class="fas fa-times-circle"></i> Canceled Orders</h3>
               
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($canceled_orders as $order): ?>
                    <tr>
                        <td>#<?= $order['order_id'] ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($order['order_date'])) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= number_format($order['total_amount'], 2) ?> birr</td>
                        <td><?= $order['status'] ? htmlspecialchars($order['status']) : 'Not specified' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.2.0/dist/js/datepicker.min.js"></script>
    