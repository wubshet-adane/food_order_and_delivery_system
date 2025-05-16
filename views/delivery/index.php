    <?php
    session_start();
    require_once '../../config/database.php';

    // Check if user is logged in and has delivery role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null || !isset($_SESSION['userType']) || $_SESSION['userType'] !== 'delivery') {
        // Redirect to login page if not logged in or not a delivery person
        header('Location: ../auth/delivery_login.php');
        exit();
    }
    //chech staus weather approved or pending or rejected
    if ($_SESSION['status'] !== 'approved'){
        header('Location: delivery_registration_success.php');
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
        'cancelled' => 0,
        'total_earning' => 0,
    ];

    //Get today deliveries
      $stmt = $conn->prepare("
        SELECT o.order_id, o.order_date, o.status, u.name AS customer_name, r.name AS restaurant_name, p.delivery_person_fee
        FROM orders o
        JOIN users u ON u.user_id = o.customer_id  
        JOIN restaurants r ON r.restaurant_id = o.restaurant_id  
        JOIN payments p ON p.order_id = o.order_id  
        WHERE o.delivery_person_id = ? AND o.order_date = CURDATE() AND o.status = 'Delivered'
        ORDER BY o.order_date DESC
        LIMIT 10
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $today_del = $result->fetch_all(MYSQLI_ASSOC); // fetch multiple rows
    $stmt->close();

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
    $stmt = $conn->prepare("SELECT COUNT(*) as in_progress FROM orders WHERE delivery_person_id = ? AND status IN ('Delivering')");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['in_progress'] = $result->fetch_assoc()['in_progress'];
    $stmt->close();

    // Pending deliveries
    $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM orders WHERE delivery_person_id = ? AND status = 'Ready_for_delivery'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['pending'] = $result->fetch_assoc()['pending'];
    $stmt->close();

    // Recent deliveries
    $stmt = $conn->prepare("
        SELECT o.order_id, o.order_date, o.status, u.name AS customer_name, r.name AS restaurant_name, p.delivery_person_fee
        FROM orders o
        JOIN users u ON u.user_id = o.customer_id  
        JOIN restaurants r ON r.restaurant_id = o.restaurant_id  
        JOIN payments p ON p.order_id = o.order_id  
        WHERE o.delivery_person_id = ? AND o.status = 'Delivered'
        ORDER BY o.order_date DESC
        LIMIT 10
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_del = $result->fetch_all(MYSQLI_ASSOC); // fetch multiple rows
    $stmt->close();


     // total earnings deliveries
    $stmt = $conn->prepare("
                SELECT balance as total_earning 
                FROM delivery_partners
                WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_earning'] = $result->fetch_assoc()['total_earning'];
    $stmt->close();
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
        <link rel="stylesheet" href="css/delivering_map.css">
        <link rel="stylesheet" href="css/payouts.css">
        <link rel="stylesheet" href="css/total_earnings.css">
        
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
                        <img src="../../uploads/user_profiles/<?= htmlspecialchars($delivery_person['image']) ?>" style="border-radius:50%;" width = "100%" height = "100%">
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
                    labels: ['Total Ordes', 'Completed', 'Delivering', 'Ready for delivery'],
                    datasets: [{
                        label: 'Delivery Statistics',
                        data: [<?= $stats['total'] ?>, <?= $stats['completed'] ?>, <?= $stats['in_progress'] ?>, <?= $stats['pending'] ?>],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(245, 158, 11, 0.7)'
                        ],
                        borderColor: [
                            'rgba(79, 70, 229, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(245, 158, 11, 1)'
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
            // const statusCtx = document.createElement('canvas');
            // statusCtx.id = 'statusChart';
            // document.querySelector('.map-container').appendChild(statusCtx);
            
            // const statusChart = new Chart(statusCtx, {
            //     type: 'pie',
            //     flexible: true,
            //     data: {
            //         labels: ['Completed', 'In Progress', 'Pending', 'Cancelled'],
            //         datasets: [{
            //             data: [<?= $stats['completed'] ?>, <?= $stats['in_progress'] ?>, <?= $stats['pending'] ?>, <?= $stats['cancelled'] ?>],
            //             backgroundColor: [
            //                 'rgba(16, 185, 129, 0.7)',
            //                 'rgba(59, 130, 246, 0.7)',
            //                 'rgba(245, 158, 11, 0.7)',
            //                 'rgba(239, 68, 68, 0.7)'
            //             ],
            //             borderColor: [
            //                 'rgba(16, 185, 129, 1)',
            //                 'rgba(59, 130, 246, 1)',
            //                 'rgba(245, 158, 11, 1)',
            //                 'rgba(239, 68, 68, 1)'
            //             ],
            //             borderWidth: 1
            //         }]
            //     },
            //     options: {
            //         responsive: true,
            //         plugins: {
            //             legend: {
            //                 position: 'bottom',
            //             }
            //         }
            //     }
            // });

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
                sidebar.classList.add('hidden');
                expander.style.display = 'inline-block';
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

        <footer style="padding: 2rem; color: #333; text-align: center;">
            &copy; G-3 online food ordering system, 2025. all rights are reserved.
        </footer>
    </body>
    </html>