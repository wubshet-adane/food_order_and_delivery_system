<?php

session_start();

require_once '../../models/manage_restaurant.php';
require_once '../../config/database.php';

// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "restaurant" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    
    header("Location: ../auth/restaurant_login.php?message=Please authenticate to access.");
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
    <link rel="stylesheet" href="css/orders.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/display_restaurant.css">
    <link rel="stylesheet" href="css/manage_restaurants.css">
    <link rel="stylesheet" href="css/manage_menu.css">
    <!--sweet alert external library-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    
    <script src="javaScript/settimeout.js"></script>
    <script src="javaScript/menu_toggler.js"></script>
    <!--script for closing responce messages automaticaly-->
    <script> closeResponseById("responce_message");</script>
    <!--edit menu modal script-->
    <script src="javaScript/edit_menu_modal.js"></script>

</body>
</html>
