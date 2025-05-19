    <?php

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
        SELECT SUM(p.service_fee) AS total_earnings 
            FROM orders o
            JOIN payments p ON o.order_id = p.order_id
            WHERE 1 = 1 $dateCondition";
    if ($stmt = $conn->prepare($sql)) {
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
            WHERE 1 = 1 $cond";
        $stmt = $conn->prepare($query);
        if(!$stmt) {
            die("SQL Error: " . $conn->error);
            // Optional: throw new Exception("Database query failed.");
        }
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats[$period] = $res['total'] ?? 0;
    }



    // Function to get total number of restaurants based on their status filter
    function getRestaurantCount($conn, $status = null) {
        $count = 0;
        $sql = "SELECT COUNT(*) as count FROM restaurants";
        
        // Add status filter if status is provided
        if ($status !== null) {
            $sql .= " WHERE confirmation_status = ?";
        }

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            // Bind parameter if status filter is used
            if ($status !== null) {
                $stmt->bind_param("s", $status);
            }

            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            
            return $count;
        } else {
            // Log the error to a file for easier tracking
            error_log("Database error: " . $conn->error . " SQL: " . $sql);
            throw new Exception("Database error: Could not retrieve restaurant count.");
        }
    }

    // Example usage with improved error handling and performance consideration
    try {
        // Example: Getting counts for restaurants and caching them for improved performance
        if (!isset($res['total_restaurants'])) {
            $res['total_restaurants'] = getRestaurantCount($conn); // Cache total count in session
        }
        if (!isset($res['pending_restaurants'])) {
            $res['pending_restaurants'] = getRestaurantCount($conn, 'pending'); // Cache pending count
        }
        if (!isset($res['approved_restaurants'])) {
            $res['approved_restaurants'] = getRestaurantCount($conn, 'approved'); // Cache approved count
        }
        if (!isset($res['rejected_restaurants'])) {
            $res['rejected_restaurants'] = getRestaurantCount($conn, 'rejected'); // Cache approved count
        }

        // Now you can use the stats for display
        $stats = [
            'total_restaurants' => $res['total_restaurants'],
            'pending_restaurants' => $res['pending_restaurants'],
            'approved_restaurants' => $res['approved_restaurants'],
            'rejected_restaurants' => $res['rejected_restaurants']
        ];

    } catch (Exception $e) {
        // Log detailed error for debugging
        error_log("Error: " . $e->getMessage());
        // Display a user-friendly message to the admin
        echo "There was an error fetching the restaurant statistics. Please try again later.";
    }


    //get number of users of this platform basecd on their role
    function getUserCountByRole($conn, $role) {
        $count = 0;
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            return $count;
        } else {
            throw new Exception("Database error: " . $conn->error);
        }
    }
    // Example usage
    try {
        $deliveryCount = getUserCountByRole($conn, 'delivery');
        $customerCount = getUserCountByRole($conn, 'customer');
        $restaurantCount = getUserCountByRole($conn, 'restaurant');

        $userStats = [
            'delivery_persons' => $deliveryCount,
            'customers' => $customerCount,
            'restaurant_owners' => $restaurantCount
        ];
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo "Error fetching user statistics. Please try again.";
    }

    $sql = "SELECT SUM(service_fee) AS total_balance FROM payments";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    ?>



    <!--js chart-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- about users -->
    <h3>System users analytics:</h3>
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-indigo">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Total Restaurant Owners</p>
                        <p class="stat-value"><?= htmlspecialchars($userStats['restaurant_owners']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Total Customers</p>
                        <p class="stat-value"><?= htmlspecialchars($userStats['customers']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-blue">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Total Delivery partners</p>
                        <p class="stat-value"><?= htmlspecialchars($userStats['delivery_persons']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-red">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Total balance</p>
                        <p class="stat-value"><?= htmlspecialchars( round($result['total_balance'], 2)) ?> Birr</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- about restaurants -->
    <h3>Restaurants on our platform:</h3>
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-yellow">
                        <i class="fa-solid fa-utensils fa-beat-fade"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Total restaurants</p>
                        <p class="stat-value"><?= htmlspecialchars($stats['total_restaurants']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-blue">
                        <i class="fa-solid fa-hourglass-half fa-spin"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Pending restaurants</p>
                        <p class="stat-value"><?= htmlspecialchars($stats['pending_restaurants']) ?></p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-green">
                    <i class="fa-regular fa-square-check fa-bounce"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Confirmed restaurants</p>
                        <p class="stat-value"><?= htmlspecialchars($stats['approved_restaurants']) ?></p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon bg-yellow">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-text">
                        <p class="stat-label">Rejected restaurants</p>
                        <p class="stat-value"><?= htmlspecialchars($stats['rejected_restaurants']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart box title-->
    <h3 class="chart-title">Earnings Overview with Chart </h3>
    <!-- Chart box -->
    <div class="chart-container">
        <canvas id="earningsChart" height="100"></canvas>
    </div>

    <!-- scripts for earnings chart -->
    <!-- scripts for earnings chart -->
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Today', 'This Week', 'This Month', 'Total'],
            datasets: [{
                label: 'Earnings (ETB)',
                data: [
                    <?= round($stats['daily'], 2) ?>,
                    <?= round($stats['weekly'], 2) ?>,
                    <?= round($stats['monthly'], 2) ?>,
                    <?= round($stats['all'], 2) ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Earnings (Birr)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time Period'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
</script>
