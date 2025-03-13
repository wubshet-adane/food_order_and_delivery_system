<?php
require_once '../../models/manage_menu.php';
$menuItems = Menu::getAllItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/display_restaurant.css">
    <link rel="stylesheet" href="css/manage_restaurant.css">
    <link rel="stylesheet" href="css/manage_menu.css">
</head>
<body>
    <!--header section-->
    <?php include_once 'header.php'; ?>


    <!--sidebar And MainContent section-->
    <section class="sidebarAndMainContent_section">
        <!--sidbar section-->
        <div class="sidebar">
            <div class="sidbar_content">
                 <h2>🍽️ My Restaurant</h2>
                 <ul class="sidebar_ul">
                    
                    <!-- Restaurant Management -->
                    <li><a href="?page=display_restaurants">🏠 Manage Restaurants</a></li>
                    <li><a href="?page=manage_menu" class="active">📋 Manage Menu</a></li>
                    <li><a href="?page=orders">🛒 Orders</a></li>
                    <li><a href="?page=rating_and_review">⭐ Ratings & Reviews</a></li>

                    <!-- Financial Section -->
                    <li class="dropdown">
                        <a href="?page=monetary">💰 Monetary ▾</a>
                        <ul class="dropdown-menu">
                            <li><a href="?page=transactions">💳 Transactions</a></li>
                            <li><a href="?page=earnings">📈 Earnings</a></li>
                            <li><a href="?page=payouts">💵 Payouts</a></li>
                            <li><a href="?page=financial_reports">📊 Financial Reports</a></li>
                        </ul>
                    </li>

                    <!-- Analytics & Reports -->
                    <li><a href="?page=reports_and_analytics">📊 Reports & Analytics</a></li>

                    <!-- Settings & Logout -->
                    <li><a href="?page=settings">⚙️ Settings</a></li>
                    <li><a href="?page=logout">🚪 Logout</a></li>
                </ul>

            </div>
        </div>

        <!--main-content section-->
        <div class="main-content">
           
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'display_restaurants';

            // Include the content for the respective page
            switch ($page) {
                case 'manage_menu':
                    include 'manage_menu.php';
                    break;
                case'display_restaurants':
                    include 'display_restaurants.php';
                    break;
                case 'reports_and_analytics':
                    include 'reports_and_rnalytics.php';
                    break;
                case 'transactions':
                    include 'transactions.php';
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
                case 'financial_reports':
                    include 'financial_reports.php';
                    break;
                case 'rating_and_review':
                    include 'rating_and_review.php';
                    break;
                case 'orders':
                    include 'orders.php';
                    break;
                case 'add_menu':
                    include 'add_menu.php.php';
                    break;
                case 'logout':
                    include 'logout.php';
                    break;
                default:
                    include 'display_restaurants.php';
            }
            ?>
        </div>
    </section>

    <!--footer section-->
    <footer class="footer_section">
        copywrite &copy; 2017 G3 Ethiopia;
    </footer>

    <!--edit menu modal script-->
    <script src="javaScript/edit_menu_modal.js"></script>
    <script src="javaScript/settimeout.js"></script>
    <script src="../../public/js/delete_confirmation.js"></script>
    <script>
        //script for closing responce messages automaticaly
        closeResponseById("responce_message"); // Auto-hide after 5 seconds
    </script>
</body>
</html>
