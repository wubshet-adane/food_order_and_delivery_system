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

   // $conn->close();
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
        <link rel="stylesheet" href="css/order_controll.css">        
        
    </head>
    <body class="bg-gray-100">
        <div class="flex h-screen">
            <!-- Sidebar -->
            <div class="w-64 bg-indigo-800 text-white p-4">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-xl font-bold"> G-3 Food Order</h1>
                    <button style="border: none; font-size:24px; background-color: #ffffff00; color: #000;" class="text-white focus:outline-none" id="sidebar_closer">
                        <i class="fas fa-remove"></i>
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
                    <a href="?page=delivery_dashboard&class=bg-indigo-700" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="?page=order_controll" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fas fa-list-ul mr-3"></i>
                        <span>My Deliveries</span>
                    </a>
                    <a href="?page=delivering_map" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fas fa-map-marked-alt mr-3"></i>
                        <span>Delivery Map</span>
                    </a>
                    <a href="?page=total_earnings" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fas fa-chart-line mr-3"></i>
                        <span>Total Earnings</span>
                    </a>
                    <a href="?page=payouts" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                    <i class="fa-solid fa-credit-card mr-3"></i>
                        <span>Payouts</span>
                    </a>
                    <a href="update_delivery_profile.php" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fas fa-cog mr-3"></i>
                        <span>Profile Settings</span>
                    </a>
                    <a href="../../public/support.php" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fa-solid fa-circle-question mr-3"></i>
                        <span>Support Center</span>
                    </a>
                    <a href="logout.html" class="nav-link flex items-center p-2 mb-1 hover:bg-indigo-700 rounded-lg" onclick="selectButton(this)">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </a>
                </nav>

                <script>
                    function selectButton(clickedBtn) {
                        // Remove the class from all buttons
                        document.querySelectorAll('.nav-link').forEach(btn => {
                            btn.classList.remove('bg-indigo-700');
                        });

                        // Add the class to the clicked one
                        clickedBtn.classList.add('bg-indigo-700');
                    }
                    </script>
            </div>

            <!-- Main Content -->
            <div class="flex-1 overflow-auto">
                <!-- Header -->
                <header class="bg-white shadow-sm p-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800"><button style="border: none; font-size:24px; background-color: #ffffff00; color: #000;" class="text-white focus:outline-none" id="sidebar_expander" title="toggle sidebar"><span ><i class="fa-solid fa-bars"></i></span></button> &nbsp;  <span>Delivery Dashboard</span></h2>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-4"><?= date('l, F j, Y') ?></span>
                            <button class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-bell text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                </header>

                <?php $page = isset($_GET['page']) ? $_GET['page'] : 'delivery_dashboard.php'; 
            switch ($page) {
                case 'display_restaurants':
                    include_once 'display_restaurants.php';
                    break;
                case 'order_controll':
                    include_once 'order_controll.php';
                    break;
                case 'delivering_map':
                    include_once 'delivering_map.php';
                    break;
                case 'total_earnings':
                    include_once 'total_earnings.php';
                    break;
                case 'payouts':
                    include_once 'payouts.php';
                    break;
                case 'financial_reports':
                    include_once 'financial_reports.php';
                    break;
                default:
                    include_once 'delivery_dashboard.php';
            }
            ?>

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
                    labels: ['Total Deliveries', 'Completed', 'In Progress', 'Pending', 'Cancelled'],
                    datasets: [{
                        label: 'Delivery Statistics',
                        data: [<?= $stats['total'] ?>, <?= $stats['completed'] ?>, <?= $stats['in_progress'] ?>, <?= $stats['pending'] ?>, <?= $stats['cancelled'] ?>],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgba(79, 70, 229, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
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
                type: 'pie',
                flexible: true,
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
        <!--side bar toggler-->
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.querySelector('.w-64');
                const closer = document.getElementById('sidebar_closer');
                const expander = document.getElementById('sidebar_expander');

                //check if side bar is hidden
                if (sidebar.classList.contains('hidden')) {
                        expander.style.display = 'inline-block';
                    } else {
                        expander.style.display = 'none';
                    }

                // Toggle sidebar visibility on button click
                closer.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                    if (sidebar.classList.contains('hidden')) {
                        expander.style.display = 'inline-block';
                    } else {
                        expander.style.display = 'none';
                    }
                });

                expander.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                    if (sidebar.classList.contains('hidden')) {
                        expander.style.display = 'inline-block';
                    } else {
                        expander.style.display = 'none';
                    }
                });
            });
        </script>
    </body>
    </html>