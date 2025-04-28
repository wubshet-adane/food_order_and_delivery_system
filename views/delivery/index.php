<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in and has delivery role
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null || !isset($_SESSION['userType']) || $_SESSION['userType'] !== 'delivery') {
    // Redirect to login page if not logged in or not a delivery person
    header('Location: ../auth/delivery_login.php');
    exit();
}

// Check if user has delivery permissions
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'delivery' && $user['role'] !== 'admin') {
    header('Location: ../dashboard/');
    exit();
}
$stmt->close();

// Get delivery person's name
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$delivery_person = $result->fetch_assoc();
$delivery_name = $delivery_person['name'];
$stmt->close();

// Get current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Get delivery statistics
$stats = [
    'total' => 0,
    'completed' => 0,
    'in_progress' => 0,
    'pending' => 0,
    'cancelled' => 0
];

// Total deliveries
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE delivery_person_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['total'] = $result->fetch_assoc()['total'];
$stmt->close();

// Completed deliveries
$stmt = $conn->prepare("SELECT COUNT(*) as completed FROM orders WHERE delivery_person_id = ? AND status = 'Delivered'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['completed'] = $result->fetch_assoc()['completed'];
$stmt->close();

// In progress deliveries
$stmt = $conn->prepare("SELECT COUNT(*) as in_progress FROM orders WHERE delivery_person_id = ? AND status IN ('Out for Delivery', 'preparing')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['in_progress'] = $result->fetch_assoc()['in_progress'];
$stmt->close();

// Pending deliveries
$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM orders WHERE delivery_person_id = ? AND status = 'Pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['pending'] = $result->fetch_assoc()['pending'];
$stmt->close();

// Cancelled deliveries
$stmt = $conn->prepare("SELECT COUNT(*) as cancelled FROM orders WHERE delivery_person_id = ? AND status = 'Cancelled'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['cancelled'] = $result->fetch_assoc()['cancelled'];
$stmt->close();


// Get delivery performance metrics
$performance = [
    'avg_time' => 'N/A',
    'on_time_rate' => 'N/A',
    'rating' => 'N/A'
];

// Average delivery time
$stmt = $conn->prepare("
    SELECT AVG(TIMESTAMPDIFF(MINUTE, o.order_date, o.delivered_at)) as avg_time
    FROM orders o
    WHERE o.delivery_person_id = ? AND o.status = 'Delivered'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$avg_time = $result->fetch_assoc()['avg_time'];
if ($avg_time) {
    $performance['avg_time'] = round($avg_time) . ' mins';
}
$stmt->close();

// On-time delivery rate
$stmt = $conn->prepare("
    SELECT 
        COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, o.order_date, o.delivered_at) <= 45 THEN 1 END) as on_time,
        COUNT(*) as total
    FROM orders o
    WHERE o.delivery_person_id = ? AND o.status = 'Delivered'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row['total'] > 0) {
    $on_time_rate = ($row['on_time'] / $row['total']) * 100;
    $performance['on_time_rate'] = round($on_time_rate) . '%';
}
$stmt->close();

// // Average rating
// $stmt = $conn->prepare("
//     SELECT AVG(r.rating) as avg_rating
//     FROM reviews r
//     JOIN orders o ON r.order_id = o.order_id
//     WHERE o.delivery_person_id = ?
// ");
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $result = $stmt->get_result();
// $avg_rating = $result->fetch_assoc()['avg_rating'];
// if ($avg_rating) {
//     $performance['rating'] = round($avg_rating, 1) . ' ★';
// }
// $stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-800 text-white p-4">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-xl font-bold"> G-3 Food Order</h1>
                <button class="text-white focus:outline-none" id="sidebar_toggler">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="flex items-center mb-6 p-2 bg-indigo-700 rounded-lg">
                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center mr-3">
                    <img src="../../public/images/<?= htmlspecialchars($delivery_person['image']) ?>" style="border-radius:50%;" width = "100%" height = "100%">
                </div>
                <div>
                    <p class="font-medium"><?= htmlspecialchars($delivery_name) ?></p>
                    <p class="text-xs text-indigo-200">Delivery Partner</p>
                </div>
            </div>
            
            <nav>
                <a href="#" class="flex items-center p-2 mb-1 bg-indigo-700 rounded-lg">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg">
                    <i class="fas fa-list-ul mr-3"></i>
                    <span>My Deliveries</span>
                </a>
                <a href="#" class="flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg">
                    <i class="fas fa-map-marked-alt mr-3"></i>
                    <span>Delivery Map</span>
                </a>
                <a href="#" class="flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg">
                    <i class="fas fa-chart-line mr-3"></i>
                    <span>Performance</span>
                </a>
                <a href="#" class="flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Settings</span>
                </a>
                <a href="../auth/logout.php" class="flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Delivery Dashboard</h2>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 mr-4"><?= date('l, F j, Y') ?></span>
                        <button class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-bell text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-4">
                <!-- Stats Cards -->
                <div class="grid grid-cols-5 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Deliveries</p>
                                <p class="text-2xl font-bold"><?= $stats['total'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Completed</p>
                                <p class="text-2xl font-bold"><?= $stats['completed'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">In Progress</p>
                                <p class="text-2xl font-bold"><?= $stats['in_progress'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pending</p>
                                <p class="text-2xl font-bold"><?= $stats['pending'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Cancelled</p>
                                <p class="text-2xl font-bold"><?= $stats['cancelled'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Today's Deliveries -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-lg flex items-center">
                                    <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>
                                    Today's Deliveries
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <?php if (empty($today_deliveries)): ?>
                                    <div class="p-4 text-center text-gray-500">
                                        No deliveries scheduled for today
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($today_deliveries as $delivery): ?>
                                        <div class="delivery-card p-4 hover:bg-gray-50">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="flex items-center mb-1">
                                                        <span class="font-medium mr-2">Order #<?= $delivery['order_id'] ?></span>
                                                        <span class="status-badge status-<?= str_replace(' ', '-', strtolower($delivery['status'])) ?>">
                                                            <?= $delivery['status'] ?>
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mb-1">
                                                        <i class="fas fa-store mr-1"></i>
                                                        <?= htmlspecialchars($delivery['restaurant_name']) ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600 mb-1">
                                                        <i class="fas fa-user mr-1"></i>
                                                        <?= htmlspecialchars($delivery['customer_name']) ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600">
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                        <?= htmlspecialchars($delivery['delivery_address']) ?>
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-gray-500 mb-2">
                                                        <?= date('h:i A', strtotime($delivery['order_date'])) ?>
                                                    </p>
                                                    <?php if ($delivery['status'] === 'Pending'): ?>
                                                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                                                            Start Delivery
                                                        </button>
                                                    <?php elseif ($delivery['status'] === 'Out for Delivery' || $delivery['status'] === 'On the Way'): ?>
                                                        <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                            Mark Delivered
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Performance and Map -->
                    <div class="space-y-6">
                        <!-- Performance Metrics -->
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="font-semibold text-lg mb-4 flex items-center">
                                <i class="fas fa-chart-line mr-2 text-indigo-600"></i>
                                Your Performance
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                                            <i class="fas fa-stopwatch"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Avg. Delivery Time</p>
                                            <p class="font-medium"><?= $performance['avg_time'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">On-Time Rate</p>
                                            <p class="font-medium"><?= $performance['on_time_rate'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-3">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Customer Rating</p>
                                            <p class="font-medium"><?= $performance['rating'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Map -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-lg flex items-center">
                                    <i class="fas fa-map-marked-alt mr-2 text-indigo-600"></i>
                                    Delivery Map
                                </h3>
                            </div>
                            <div class="map-container p-4">
                                <div class="text-center">
                                    <i class="fas fa-map text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">Map view would appear here</p>
                                    <button class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                                        View Full Map
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Deliveries -->
                <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-semibold text-lg flex items-center">
                            <i class="fas fa-history mr-2 text-indigo-600"></i>
                            Recent Deliveries
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($recent_deliveries)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No recent deliveries found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_deliveries as $delivery): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $delivery['order_id'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($delivery['restaurant_name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($delivery['customer_name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M j, Y', strtotime($delivery['order_date'])) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="status-badge status-<?= str_replace(' ', '-', strtolower($delivery['status'])) ?>">
                                                    <?= $delivery['status'] ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                <a href="#" class="text-gray-600 hover:text-gray-900">Invoice</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Performance Chart
        const ctx = document.createElement('canvas');
        ctx.id = 'performanceChart';
        document.querySelector('.map-container').appendChild(ctx);
        
        const performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Deliveries Completed',
                    data: [12, 19, 15, 17, 22, 25, 18],
                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Delivery Status Chart
        const statusCtx = document.createElement('canvas');
        statusCtx.id = 'statusChart';
        document.querySelector('.map-container').appendChild(statusCtx);
        
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Cancelled'],
                datasets: [{
                    data: [<?= $stats['completed'] ?>, <?= $stats['in_progress'] ?>, <?= $stats['pending'] ?>, <?= $stats['cancelled'] ?>],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Simulate map loading
        function loadMap() {
            const mapContainer = document.querySelector('.map-container');
            mapContainer.innerHTML = `
                <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600 mb-4">Interactive map would load here</p>
                        <div class="animate-pulse h-48 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>`;
            setTimeout(() => {
                mapContainer.innerHTML = `
                    <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center p-4">
                            <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                            <p class="text-gray-700 mb-2">Map loaded successfully</p>
                            <p class="text-sm text-gray-500">Showing delivery locations for today</p>
                        </div>
                    </div>
                `;
            }, 2000);
        }

        // Load map when map tab is clicked
        document.querySelector('[href="#map"]').addEventListener('click', loadMap);
    </script>
</body>
</html>