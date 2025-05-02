    <?php

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
            JOIN users u ON o.customer_id = u.user_id
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE r.owner_id = ? $dateCondition";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $totalEarnings = $result['total_earnings'] ?? 0.00;
    } else {
        error_log("SQL Error: " . $conn->error);
        // Optional: throw new Exception("Database query failed.");
    }


    // Stats cards (today, week, month, all)
    $stats = [];
    foreach (['daily', 'weekly', 'monthly', 'all'] as $period) {
        $cond = $conditions[$period];
        $query = "
            SELECT SUM(p.amount) AS total 
            FROM orders o
            JOIN payments p ON o.order_id = p.order_id
            JOIN users u ON o.customer_id = u.user_id
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE r.owner_id = ? $cond";
        $stmt = $conn->prepare($query);
        if(!$stmt) {
            die("SQL Error: " . $conn->error);
            // Optional: throw new Exception("Database query failed.");
        }
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats[$period] = $res['total'] ?? 0;
    }

    // Get recent paid transactions
    function getRecentTransactions($conn, $restaurant_id, $limit = 100) {
        $transactions = [];
        $sql = "
            SELECT 
                u.name AS customer_name, 
                p.amount AS total_amount, 
                o.order_date 
            FROM orders o
            JOIN payments p ON o.order_id = p.order_id
            JOIN users u ON o.customer_id = u.user_id
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE r.owner_id = ?
            ORDER BY o.order_date DESC 
            LIMIT ?
        ";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $restaurant_id, $limit);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $transactions[] = $row;
            }
            $stmt->close();
        } 
        return $transactions;
    }
    $transactions = getRecentTransactions($conn, $restaurant_id);
    ?>
    <!--js chart-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <h2 class="Earnings-Dashboard">Earnings Dashboard</h2>
    <!-- 
    <div class="filters">
        <a href="?page=earnings&filter=daily" class="<?= $filter === 'daily' ? 'active' : '' ?>">Daily</a>
        <a href="?page=earnings&filter=weekly" class="<?= $filter === 'weekly' ? 'active' : '' ?>">Weekly</a>
        <a href="?page=earnings&filter=monthly" class="<?= $filter === 'monthly' ? 'active' : '' ?>">Monthly</a>
        <a href="?page=earnings&filter=yearly" class="<?= $filter === 'yearly' ? 'active' : '' ?>">Yearly</a>
        <a href="?page=earnings&filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All Time</a>
    </div> -->

    <div class="card-boxes">
        <div class="card">
            <h3>Today</h3>
            <h5>start from midnight</h5>
            <p>  <?= number_format($stats['daily'], 2) ?> birr</p>
        </div>
        <div class="card">
            <h3>This Week</h3>
            <h5>start from the previous monday</h5>
            <p> <?= number_format($stats['weekly'], 2) ?> birr</p>
        </div>
        <div class="card">
            <h3>This Month</h3>
            <h5>start from the first day of this month</h5>
            <p> <?= number_format($stats['monthly'], 2) ?> birr</p>
        </div>
        <div class="card">
            <h3>Total Earnings</h3>
            <p> <?= number_format($stats['all'], 2) ?> birr</p>
        </div>
    </div>

    <!-- Chart box title-->
    <h3 class="chart-title">Earnings Overview with Chart </h3>
    <!-- Chart box -->
    <div class="chart-container">
        <canvas id="earningsChart" height="100"></canvas>
    </div>

    <?php if ($transactions): ?>
        <div class="transaction-table">
        <h3>Recent Transactions</h3>
        <table>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>

            <?php 
            $transactions = array_slice($transactions, 0, 20); // Limit to 10 transactions
            //index numbers for transactions
            $index = 0;
            foreach ($transactions as $t): 
                $index++;
            ?>
                <tr class="<?= $index % 2 == 0 ? 'even_row' : 'odd_row' ?>">
                    <td><strong><?=$index?>.</strong></td>
                    <td><?= ucwords(htmlspecialchars($t['customer_name'])) ?></td>
                    <td> <span style="font-family: 'oswald', sans-serif;"><?= number_format($t['total_amount'], 2) ?></span> birr</td>
                    <td><?= date("M d, Y", strtotime($t['order_date'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" class="text-center"><a href="" class="view-more-btn">View more <i class="fa-solid fa-arrow-down"></i> </a></td>
            </tr>
        </table>
    </div>
    <?php else: ?>
        <div class="no-transactions">
            <p>No transactions available.</p>
        </div>
    <?php endif; ?>

    <script>
        const ctx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Today', 'This Week', 'This Month', 'Total'],
                datasets: [{
                    label: 'Earnings (ETB)',
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

