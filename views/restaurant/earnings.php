<?php
include '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/restaurant_login.php');
    exit();
}

$restaurant_id = $_SESSION['user_id'];
$filter = $_GET['filter'] ?? 'all';

// Earnings by filter
$conditions = [
    'daily'   => "AND DATE(order_date) = CURDATE()",
    'weekly'  => "AND YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)",
    'monthly' => "AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())",
    'yearly'  => "AND YEAR(order_date) = YEAR(CURDATE())",
    'all'     => ""
];

$dateCondition = $conditions[$filter] ?? "";

// Main earnings query
$sql = "
    SELECT SUM(p.amount) AS total_earnings 
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
        WHERE o.customer_id = ? $dateCondition";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalEarnings = $result['total_earnings'] ?? 0.00;

// Stats cards (today, week, month, all)
$stats = [];
foreach (['daily', 'weekly', 'monthly', 'all'] as $period) {
    $cond = $conditions[$period];
    $query = "
        SELECT SUM(p.amount) AS total 
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
        WHERE o.customer_id = ? $cond";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stats[$period] = $res['total'] ?? 0;
}

// Get recent paid transactions
$transactions = [];
$sql = "
    SELECT u.name AS customer_name, p.amount AS total_amount, o.order_date 
    FROM orders o
    JOIN payments p ON o.order_id = p.order_id
    JOIN users u ON o.customer_id = o.customer_id
    JOIN restaurants r ON o.restaurant_id = r.restaurant_id
    WHERE o.customer_id = ?
    ORDER BY order_date DESC LIMIT 10";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die($conn->error);
}
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $transactions[] = $row;
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h2>Earnings Dashboard</h2>

<div class="filters">
    <a href="?page=earnings&filter=daily" class="<?= $filter === 'daily' ? 'active' : '' ?>">Daily</a>
    <a href="?page=earnings&filter=weekly" class="<?= $filter === 'weekly' ? 'active' : '' ?>">Weekly</a>
    <a href="?page=earnings&filter=monthly" class="<?= $filter === 'monthly' ? 'active' : '' ?>">Monthly</a>
    <a href="?page=earnings&filter=yearly" class="<?= $filter === 'yearly' ? 'active' : '' ?>">Yearly</a>
    <a href="?page=earnings&filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All Time</a>
</div>

<div class="card-boxes">
    <div class="card">
        <h3>Today</h3>
        <p> ETB <?= number_format($stats['daily'], 2) ?></p>
    </div>
    <div class="card">
        <h3>This Week</h3>
        <p>ETB <?= number_format($stats['weekly'], 2) ?></p>
    </div>
    <div class="card">
        <h3>This Month</h3>
        <p>ETB <?= number_format($stats['monthly'], 2) ?></p>
    </div>
    <div class="card">
        <h3>Total Earnings</h3>
        <p>ETB <?= number_format($stats['all'], 2) ?></p>
    </div>
</div>

<div class="chart-container">
    <canvas id="earningsChart" height="100"></canvas>
</div>

<div class="transaction-table">
    <h3>Recent Transactions</h3>
    <table>
        <tr>
            <th>Customer</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
        <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['customer_name']) ?></td>
                <td>$<?= number_format($t['total_amount'], 2) ?></td>
                <td><?= date("M d, Y", strtotime($t['order_date'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
const ctx = document.getElementById('earningsChart').getContext('2d');
const earningsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Today', 'This Week', 'This Month', 'Total'],
        datasets: [{
            label: 'Earnings ($)',
            data: [
                <?= $stats['daily'] ?>,
                <?= $stats['weekly'] ?>,
                <?= $stats['monthly'] ?>,
                <?= $stats['all'] ?>
            ],
            backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

