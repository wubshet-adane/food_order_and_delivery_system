<?php
include 'includes/header.php';
include 'includes/auth.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">All Orders</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="refreshOrders()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <select class="form-select" id="status-filter">
            <option value="all">All Statuses</option>
            <option value="assigned">Assigned</option>
            <option value="picked_up">Picked Up</option>
            <option value="on_the_way">On the Way</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" class="form-control" id="date-filter">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary" onclick="filterOrders()">Filter</button>
        <button class="btn btn-secondary" onclick="resetFilters()">Reset</button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Restaurant</th>
                <th>Customer</th>
                <th>Order Time</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="orders-table">
            <!-- Orders will be loaded via AJAX -->
        </tbody>
    </table>
</div>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center" id="pagination">
        <!-- Pagination will be loaded via AJAX -->
    </ul>
</nav>

<?php include 'includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        loadOrders(1);
    });
    
    function loadOrders(page, status = 'all', date = '') {
        $.ajax({
            url: '../ajax/get_orders.php',
            type: 'GET',
            data: {
                delivery_id: <?php echo $delivery_id; ?>,
                page: page,
                status: status,
                date: date
            },
            success: function(response) {
                const data = JSON.parse(response);
                $('#orders-table').html(data.orders);
                $('#pagination').html(data.pagination);
            }
        });
    }
    
    function filterOrders() {
        const status = $('#status-filter').val();
        const date = $('#date-filter').val();
        loadOrders(1, status, date);
    }
    
    function resetFilters() {
        $('#status-filter').val('all');
        $('#date-filter').val('');
        loadOrders(1);
    }
    
    function refreshOrders() {
        const status = $('#status-filter').val();
        const date = $('#date-filter').val();
        const activePage = $('.page-item.active').data('page');
        loadOrders(activePage || 1, status, date);
    }
    
    function updateOrderStatus(orderId, status) {
        if (confirm('Are you sure you want to update this order status?')) {
            $.ajax({
                url: '../ajax/update_order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId,
                    status: status,
                    delivery_id: <?php echo $delivery_id; ?>
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('Order status updated successfully');
                        refreshOrders();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }
            });
        }
    }
</script>