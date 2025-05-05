<?php

// Database connection
require_once '../../config/database.php';

// Create connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get date ranges
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('-7 days'));
$month_start = date('Y-m-d', strtotime('-30 days'));
$year_start = date('Y-m-d', strtotime('-1 year'));

// Function to fetch revenue data
function getRevenueData($conn, $start_date, $end_date = null) {
    $sql = "
            SELECT SUM(p.amount) as revenue 
            FROM orders o
            JOIN payments p ON o.order_id = p.order_id
            WHERE status = 'completed' AND order_date >= ?";
    $stmt = $conn->prepare($sql);
    
    if ($end_date) {
        $sql .= " AND order_date <= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $start_date, $end_date);
    } else {
        $stmt = $conn->prepare($sql);
        if(!$stmt){
            die($conn->error);
        }
        $stmt->bind_param("s", $start_date);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['revenue'] ?? 0;
}

// Fetch revenue data
$daily_revenue = getRevenueData($conn, $today);
$weekly_revenue = getRevenueData($conn, $week_start, $today);
$monthly_revenue = getRevenueData($conn, $month_start, $today);
$yearly_revenue = getRevenueData($conn, $year_start, $today);

// Order count breakdowns
$order_counts = [
    'today' => $conn->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = '$today'")->fetch_row()[0],
    'week' => $conn->query("SELECT COUNT(*) FROM orders WHERE order_date >= '$week_start'")->fetch_row()[0],
    'month' => $conn->query("SELECT COUNT(*) FROM orders WHERE order_date >= '$month_start'")->fetch_row()[0],
    'year' => $conn->query("SELECT COUNT(*) FROM orders WHERE order_date >= '$year_start'")->fetch_row()[0],
    'completed' => $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetch_row()[0],
    'canceled' => $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'canceled'")->fetch_row()[0],
    'pending' => $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetch_row()[0]
];

// Top-selling items
$top_items_result = $conn->query(
    "SELECT m.name AS item_name, SUM(oi.quantity) as total_sold, m.price
        FROM order_items oi
        JOIN menu m ON oi.menu_id = m.menu_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.status = 'completed'
        GROUP BY oi.menu_id
        ORDER BY total_sold DESC
        LIMIT 10"
    );
    if(!$top_items_result){
        die($conn->error);
    }
$top_items = [];
while ($row = $top_items_result->fetch_assoc()) {
    $top_items[] = $row;
}

// Canceled orders log
$canceled_orders_result = $conn->query(
    "SELECT o.order_id, o.order_date, p.amount as total_amount, u.name as customer_name, o.status as cancel_reason
     FROM orders o
     JOIN users u ON o.customer_id = u.user_id
     JOIN payments p ON o.order_id = p.order_id
     WHERE o.status = 'canceled'
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

// Customer purchase trends (last 12 months)
$monthly_trends_result = $conn->query(
    "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, 
            COUNT(*) as order_count, 
            SUM(p.amount) as total_revenue
            FROM orders o       
            JOIN payments p ON o.order_id = p.order_id
            WHERE order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND status = 'completed'
            ORDER BY month"
);
$monthly_trends = [];
while ($row = $monthly_trends_result->fetch_assoc()) {
    $monthly_trends[] = $row;
}

// Prepare data for charts
$trend_months = array_column($monthly_trends, 'month');
$trend_orders = array_column($monthly_trends, 'order_count');
$trend_revenue = array_column($monthly_trends, 'total_revenue');

$conn->close();
?>

    <div class="admin-container">
        <h1><i class="fas fa-chart-line"></i> Analytics & Reports</h1>
        
        <!-- Revenue Metrics -->
        <h3 class="section-title">Revenue Overview</h3>
        <div class="dashboard-container">
            <div class="metric-card">
                <h3>Daily Revenue</h3>
                <div class="metric-value"><?= number_format($daily_revenue, 2) ?> birr</div>
                <p>Today's completed orders</p>
            </div>
            <div class="metric-card">
                <h3>Weekly Revenue</h3>
                <div class="metric-value"><?= number_format($weekly_revenue, 2) ?> birr</div>
                <p>Last 7 days</p>
            </div>
            <div class="metric-card">
                <h3>Monthly Revenue</h3>
                <div class="metric-value"><?= number_format($monthly_revenue, 2) ?> birr</div>
                <p>Last 30 days</p>
            </div>
            <div class="metric-card">
                <h3>Yearly Revenue</h3>
                <div class="metric-value"><?= number_format($yearly_revenue, 2) ?> birr</div>
                <p>Last 12 months</p>
            </div>
        </div>
        
        <!-- Order Trends Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h2><i class="fas fa-chart-bar"></i> Order Trends</h2>
                <button class="export-btn" id="exportTrends">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
            <div class="time-period-selector">
                <button class="active" onclick="updateChart('monthly')">Monthly</button>
                <button onclick="updateChart('weekly')">Weekly</button>
                <button onclick="updateChart('daily')">Daily</button>
            </div>
            <div class="date-range-selector">
                <div class="input-group">
                    <label for="startDate">From:</label>
                    <input type="text" id="startDate" class="datepicker" placeholder="Start date">
                </div>
                <div class="input-group">
                    <label for="endDate">To:</label>
                    <input type="text" id="endDate" class="datepicker" placeholder="End date">
                </div>
                <button class="apply-btn" id="applyDateRange">Apply</button>
            </div>
            <canvas id="orderTrendsChart" height="300"></canvas>
        </div>
        
        <!-- Order Count Breakdown -->
        <h2 class="section-title">Order Statistics</h2>
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
                    <span class="badge badge-success"><?= $order_counts['completed'] ?> Completed</span><br>
                    <span class="badge badge-warning"><?= $order_counts['pending'] ?> Pending</span><br>
                    <span class="badge badge-danger"><?= $order_counts['canceled'] ?> Canceled</span>
                </div>
            </div>
        </div>
        
        <!-- Top Selling Items -->
        <div class="chart-container">
            <div class="chart-header">
                <h2><i class="fas fa-utensils"></i> Top Selling Menu Items</h2>
                <button class="export-btn" id="exportTopItems">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
            <canvas id="topItemsChart" height="300"></canvas>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity Sold</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['total_sold'] ?></td>
                        <td>₹<?= number_format($item['total_sold'] * $item['price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Canceled Orders Log -->
        <div class="chart-container">
            <div class="chart-header">
                <h2><i class="fas fa-times-circle"></i> Canceled Orders</h2>
                <button class="export-btn" id="exportCanceled">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($canceled_orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($order['order_date'])) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= $order['cancel_reason'] ? htmlspecialchars($order['cancel_reason']) : 'Not specified' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.2.0/dist/js/datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Initialize date pickers
        document.querySelectorAll('.datepicker').forEach(el => {
            new Datepicker(el, {
                format: 'yyyy-mm-dd',
                autohide: true
            });
        });
        
        // Chart data
        const trendMonths = <?= json_encode($trend_months) ?>;
        const trendOrders = <?= json_encode($trend_orders) ?>;
        const trendRevenue = <?= json_encode($trend_revenue) ?>;
        const topItemsLabels = <?= json_encode(array_column($top_items, 'item_name')) ?>;
        const topItemsData = <?= json_encode(array_column($top_items, 'total_sold')) ?>;
        
        // Order Trends Chart
        const orderTrendsCtx = document.getElementById('orderTrendsChart').getContext('2d');
        const orderTrendsChart = new Chart(orderTrendsCtx, {
            type: 'line',
            data: {
                labels: trendMonths,
                datasets: [
                    {
                        label: 'Number of Orders',
                        data: trendOrders,
                        borderColor: '#ff6600',
                        backgroundColor: 'rgba(255, 102, 0, 0.1)',
                        tension: 0.3,
                        yAxisID: 'y',
                        borderWidth: 2,
                        pointBackgroundColor: '#ff6600',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Revenue (₹)',
                        data: trendRevenue,
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.3,
                        yAxisID: 'y1',
                        borderWidth: 2,
                        pointBackgroundColor: '#4361ee',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 1) {
                                    label += '₹' + context.raw.toLocaleString();
                                } else {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Orders',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue (₹)',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Top Items Chart
        const topItemsCtx = document.getElementById('topItemsChart').getContext('2d');
        const topItemsChart = new Chart(topItemsCtx, {
            type: 'bar',
            data: {
                labels: topItemsLabels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: topItemsData,
                    backgroundColor: 'rgba(255, 102, 0, 0.7)',
                    borderColor: 'rgba(255, 102, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12
                    },
                    legend: {
                        display: false
                    },
                    datalabels: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity Sold',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            drawOnChartArea: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Function to update chart based on time period
        function updateChart(period) {
            const btn = event.target;
            const loading = document.createElement('span');
            loading.className = 'loading';
            btn.innerHTML = '';
            btn.appendChild(loading);
            
            // Simulate API call with timeout
            setTimeout(() => {
                // In a real implementation, you would fetch new data via AJAX
                // For demo, we'll just update the active button
                document.querySelectorAll('.time-period-selector button').forEach(btn => {
                    btn.classList.remove('active');
                    btn.innerHTML = btn.textContent;
                });
                
                btn.classList.add('active');
                btn.innerHTML = btn.textContent;
                
                // Show a toast notification
                showToast(`Showing ${period} data`);
            }, 1000);
        }
        
        // Apply date range
        document.getElementById('applyDateRange').addEventListener('click', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                showToast('Please select both start and end dates', 'error');
                return;
            }
            
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Applying...';
            
            // Simulate API call with timeout
            setTimeout(() => {
                btn.innerHTML = originalText;
                showToast(`Showing data from ${startDate} to ${endDate}`);
                
                // In a real implementation, you would fetch data for this date range
                // and update the chart
            }, 1500);
        });
        
        // Export functions
        document.getElementById('exportTrends').addEventListener('click', function() {
            exportChartAsImage(orderTrendsChart, 'order-trends');
        });
        
        document.getElementById('exportTopItems').addEventListener('click', function() {
            exportChartAsImage(topItemsChart, 'top-items');
        });
        
        document.getElementById('exportCanceled').addEventListener('click', function() {
            exportTableAsCSV('canceled-orders', 'canceled-orders.csv');
        });
        
        function exportChartAsImage(chart, filename) {
            const link = document.createElement('a');
            link.download = `${filename}.png`;
            link.href = chart.toBase64Image('image/png', 1);
            link.click();
            showToast(`Exported ${filename} chart as PNG`);
        }
        
        function exportTableAsCSV(tableId, filename) {
            const table = document.querySelector(`#${tableId} table`);
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    // Clean innerText to remove multiple spaces and line breaks
                    const text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                    row.push(text);
                }
                
                csv.push(row.join(','));
            }
            
            // Download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showToast(`Exported ${filename}`);
        }
        
        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
        
        // Add toast styles dynamically
        const toastStyles = document.createElement('style');
        toastStyles.innerHTML = `
            .toast {
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transform: translateY(100px);
                opacity: 0;
                transition: all 0.3s ease;
                z-index: 1000;
            }
            .toast.show {
                transform: translateY(0);
                opacity: 1;
            }
            .toast-success {
                background-color: var(--success-color);
            }
            .toast-error {
                background-color: var(--danger-color);
            }
        `;
        document.head.appendChild(toastStyles);
    </script>
