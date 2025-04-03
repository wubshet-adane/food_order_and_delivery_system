<?php
include 'header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Delivery Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <i class="bi bi-calendar"></i> This week
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Assigned Orders</h5>
                <h2 class="card-text" id="assigned-count">0</h2>
            </div>
            <div class="card-footer">
                <a href="assigned.php" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Completed Today</h5>
                <h2 class="card-text" id="completed-count">0</h2>
            </div>
            <div class="card-footer">
                <a href="completed.php" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Earnings Today</h5>
                <h2 class="card-text" id="earnings">₹0</h2>
            </div>
            <div class="card-footer">
                <a href="earnings.php" class="text-white">View details</a>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3">Recent Orders</h4>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Restaurant</th>
                <th>Customer</th>
                <th>Address</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="recent-orders">
            <!-- Orders will be loaded via AJAX -->
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>

<script src="../assets/js/script.js"></script>
<script>
    $(document).ready(function() {
        loadDashboardStats();
        loadRecentOrders();
        
        // Refresh every 30 seconds
        setInterval(function() {
            loadDashboardStats();
            loadRecentOrders();
        }, 30000);
    });
    
    function loadDashboardStats() {
        $.ajax({
            url: '../ajax/get_delivery_stats.php',
            type: 'GET',
            data: { delivery_id: <?php echo $delivery_id; ?> },
            success: function(response) {
                const data = JSON.parse(response);
                $('#assigned-count').text(data.assigned);
                $('#completed-count').text(data.completed);
                $('#earnings').text('₹' + data.earnings);
            }
        });
    }
    
    function loadRecentOrders() {
        $.ajax({
            url: '../ajax/get_recent_orders.php',
            type: 'GET',
            data: { delivery_id: <?php echo $delivery_id; ?> },
            success: function(response) {
                $('#recent-orders').html(response);
            }
        });
    }
</script>