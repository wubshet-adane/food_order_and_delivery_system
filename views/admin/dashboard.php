<?php
ob_start(); // 🛡️ Output buffering starts

session_start();

require_once '../../models/manage_restaurant.php';
require_once '../../config/database.php';
require_once '../../models/restaurant_review.php';

// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "admin" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    
    header("Location: ../auth/admin_login.php?message=Please authenticate to access.");
    exit; // Stop execution after redirection
}
$ownerId = $_SESSION['user_id'];
$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getAllRestaurants($ownerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G3 FOOD ORDER</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!--google font-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.2.0/dist/css/datepicker.min.css">
   
    <link rel="stylesheet" href="css/orders.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/asdashboard.css">
    <link rel="stylesheet" href="css/manage_restaurants.css">
    <link rel="stylesheet" href="css/manage_menu.css">
    <link rel="stylesheet" href="css/reviews.css">
    <link rel="stylesheet" href="css/earnings.css">
    <link rel="stylesheet" href="css/payouts.css">
    <link rel="stylesheet" href="css/reports_and_analytics.css">
    <link rel="stylesheet" href="css/support_management.css">
    <!--sweet alert external library-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!--header section-->
    <?php include_once 'header.php'; ?>


    <!--sidebar And MainContent section-->
    <section class="sidebarAndMainContent_section">
        <!--sidbar section-->
        <div class="sidebar" id="sidebar">
            <div class="side_bar_header">
                <button style="border: none; font-size:24px; background-color: #ffffff00; color: #000;" class="text-white focus:outline-none" id="sidebar_closer">
                    <i class="fas fa-remove"></i>
                </button>
            </div>
            <div class="sidbar_content">
                 <ul class="sidebar_ul">
                    <!-- Restaurant Management -->
                    <li><a href="?page=asdashboard"><i class="fa-solid fa-table-columns"></i></i> &nbsp;&nbsp;Dashboard </a></li>
                    <li><a href="?page=manage_restaurants"><i class="fa-solid fa-hotel"></i> &nbsp;&nbsp;Manage Restaurant </a></li>
                    <li><a href="?page=support_management" class="active"><i class="fa-solid fa-utensils"></i> &nbsp;&nbsp;Support Management</a></li>
                    <li><a href="?page=orders"><i class="fas fa-list-ul mr-3"></i> &nbsp;&nbsp;Orders</a></li>
                    <li><a href="?page=rating_and_review"><i class="fa-solid fa-star"></i> &nbsp;&nbsp;Ratings & Reviews</a></li>
                    <!-- Financial Section -->
                    <li class="dropdown">
                        <a href="?page=monetary">
                            <i class="fa-solid fa-briefcase"></i> &nbsp;&nbsp; Monetary <i class="fa-solid fa-chevron-down" style="float: right;"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- <li><a href="?page=transactions">💳 &nbsp;&nbsp;Transactions</a></li> -->
                            <li><a href="?page=earnings">📈 &nbsp;&nbsp;Earnings</a></li>
                            <li><a href="?page=payouts">💵 &nbsp;&nbsp;Payouts</a></li>
                            <!-- <li><a href="?page=financial_reports">📊 &nbsp;&nbsp;Financial Reports</a></li> -->
                        </ul>
                    </li>
                    <!-- Analytics & Reports -->
                    <li><a href="?page=reports_and_analytics"><i class="fa-solid fa-chart-line"></i> &nbsp;&nbsp;Reports & Analytics</a></li>
                    <!-- Settings & Logout -->
                    <li><a href="?page=settings"><i class="fas fa-cog mr-3"> </i> &nbsp;&nbsp;Settings</a></li>
                    <li><a href="../../public/help_center.php"><i class="fa-solid fa-circle-question"></i> &nbsp;&nbsp;Help Center</a></li>
                    <li><a href="logout.html"><i class="fas fa-sign-out-alt mr-3"> </i> &nbsp;&nbsp;Logout</a></li>
                </ul>

            </div>
        </div>

        <!--main-content section-->
        <div class="main-content"> 
            <h2 style="margin-bottom: 0;">
                <button style="border: none; font-size:24px; background-color: #ffffff00; color: #000;" class="text-white focus:outline-none" id="sidebar_expander" title="toggle sidebar">
                    <span ><i class="fa-solid fa-bars"></i></span>
                </button> &nbsp; 
                <!-- Greeting message for restaurant owner -->
                <storng class="welcome_message"><span><?=$_SESSION['name']?> Dashboard:</span></storng>   
            </h2>
            
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'asdashboard';

            // Include the content for the respective page
            switch ($page) {
                case 'support_management':
                    include 'support_management.php';
                    break;
                case'asdashboard':
                    include 'asdashboard.php';
                    break;
                    case'manage_restaurants':
                    include 'manage_restaurants.php';
                    break;
                case 'reports_and_analytics':
                    include 'reports_and_analytics.php';
                    break;
                case 'settings':
                    include 'settings.php';
                    break;
                case 'earnings':
                    include 'earnings.php';
                    break;
                case 'payouts':
                    include 'payouts.php';
                    break;
                // case 'financial_reports':
                //     include 'financial_reports.php';
                //     break;
                case 'rating_and_review':
                    include 'rating_and_review.php';
                    break;
                case 'orders':
                    include 'orders.php';
                    break;
                case 'add_menu':
                    include 'add_menu.php.php';
                    break;
                default:
                    include 'asdashboard.php';
            }
            ?>
        </div>
         <!--footer section-->
        <footer class="footer_section">
            copywrite &copy; 2017 G3 Ethiopia;
        </footer>
    </section>
    
    <script src="javaScript/settimeout.js"></script>
    <script src="javaScript/menu_toggler.js"></script>
    <!--script for closing responce messages automaticaly-->
    <script> closeResponseById("responce_message");</script>
    <!--edit menu modal script-->
    <script src="javaScript/edit_menu_modal.js"></script>
    <script src="javaScript/side_bar_toggler.js"></script>
    

</body>
</html>
