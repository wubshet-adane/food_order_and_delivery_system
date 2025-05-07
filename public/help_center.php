<?php
session_start();
require_once '../config/database.php';

// Determine user type for customized help
$user_type = isset($_SESSION['userType']) ? $_SESSION['userType'] : 'guest'; // customer, restaurant, delivery, or guest
$user_id = $_SESSION['user_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - G-3 online food ordering system</title>
    <link rel="icon" href="images/logo-icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/help_center.css">
    <link rel="stylesheet" href="../views/customers/css/footer.css">
</head>
<body>
    <header class="help-header">
        <div class="topbarInHeader">
            <div class="logopart">
                <img src="images/logo.jpg" alt="logo image" onclick="window.location.href='../views/customers/home.php'">
            </div>
            <div class="backLink">
                <a href="javascript:history.back()"><i class="fa-solid fa-arrow-left"></i>  Back to homepage</a>
            </div>
        </div>
        <div class="container">
            <h1><i class="fas fa-question-circle"></i> Help Center</h1>
            <p>Find answers to your questions and learn how to get the most out of Our Platform</p>
        </div>
    </header>

    <div class="container">
        <div class="user-type-tabs">
            <button class="user-tab customer <?= $user_type === 'customer' ? 'active' : '' ?>" data-target="customer-help">
                <i class="fas fa-user"></i> I'm a Customer
            </button>
            <button class="user-tab restaurant <?= $user_type === 'restaurant' ? 'active' : '' ?>" data-target="restaurant-help">
                <i class="fas fa-utensils"></i> I'm a Restaurant Owner
            </button>
            <button class="user-tab delivery <?= $user_type === 'delivery' ? 'active' : '' ?>" data-target="delivery-help">
                <i class="fas fa-motorcycle"></i> I'm a Delivery Partner
            </button>
        </div>

        <!-- Customer Help Content -->
        <div id="customer-help" class="help-content <?= $user_type === 'customer' ? 'active' : '' ?>">
            <div class="help-section">
                <h2><i class="fas fa-shopping-bag"></i> Ordering Food</h2>
                <div class="topic-list">
                    <div class="topic-card" onclick="showGuide('customer-order')">
                        <h3><i class="fas fa-utensils"></i> How to place an order</h3>
                        <p>Step-by-step guide to ordering food from your favorite restaurants</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('customer-payment')">
                        <h3><i class="fas fa-credit-card"></i> Payment methods</h3>
                        <p>Learn about available payment options and how to use them</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('customer-tracking')">
                        <h3><i class="fas fa-map-marker-alt"></i> Tracking your order</h3>
                        <p>How to track your delivery in real-time</p>
                    </div>
                </div>
            </div>

            <div class="help-section">
                <h2><i class="fas fa-user-circle"></i> Account & Settings</h2>
                <div class="topic-list">
                    <div class="topic-card" onclick="showGuide('customer-account')">
                        <h3><i class="fas fa-user-plus"></i> Creating an account</h3>
                        <p>How to set up your FoodExpress customer account</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('customer-address')">
                        <h3><i class="fas fa-home"></i> Managing addresses</h3>
                        <p>How to add, edit, or remove delivery addresses</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('customer-promo')">
                        <h3><i class="fas fa-tag"></i> Using promo codes</h3>
                        <p>How to apply discounts and special offers</p>
                    </div>
                </div>
            </div>

            <div id="customer-order-guide" class="detailed-guide" style="display: none;">
                <h2><i class="fas fa-utensils"></i> How to Place an Order</h2>
                
                <div class="guide-step">
                    <h3><span class="step-number">1</span> Find a restaurant</h3>
                    <p>Browse or search for restaurants in your area. You can filter by cuisine type, delivery time, or ratings.</p>
                    <img src="../images/help/search-restaurant.jpg" alt="Searching for restaurants">
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">2</span> Select your items</h3>
                    <p>Click on menu items to add them to your cart. You can specify special instructions for each item.</p>
                    <img src="../images/help/select-items.jpg" alt="Selecting menu items">
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">3</span> Review your order</h3>
                    <p>Check your cart to ensure all items are correct. You can modify quantities or remove items at this stage.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">4</span> Choose delivery option</h3>
                    <p>Select delivery or pickup (if available). Enter your delivery address or confirm pickup location.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">5</span> Complete payment</h3>
                    <p>Select your payment method and enter any promo codes. Confirm your order when ready.</p>
                </div>
                
                <button class="back-button" onclick="hideGuides()"><i class="fas fa-arrow-left"></i> Back to topics</button>
            </div>
        </div>

        <!-- Restaurant Help Content -->
        <div id="restaurant-help" class="help-content <?= $user_type === 'restaurant' ? 'active' : '' ?>">
            <div class="help-section">
                <h2><i class="fas fa-store"></i> Restaurant Management</h2>
                <div class="topic-list">
                    <div class="topic-card" onclick="showGuide('restaurant-menu')">
                        <h3><i class="fas fa-clipboard-list"></i> Managing your menu</h3>
                        <p>How to add, edit, or remove items from your restaurant menu</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('restaurant-orders')">
                        <h3><i class="fas fa-receipt"></i> Processing orders</h3>
                        <p>Step-by-step guide to receiving and fulfilling orders</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('restaurant-hours')">
                        <h3><i class="fas fa-clock"></i> Setting business hours</h3>
                        <p>How to configure your restaurant's operating schedule</p>
                    </div>
                </div>
            </div>

            <div class="help-section">
                <h2><i class="fas fa-chart-line"></i> Analytics & Growth</h2>
                <div class="topic-list">
                    <div class="topic-card" onclick="showGuide('restaurant-analytics')">
                        <h3><i class="fas fa-chart-pie"></i> Understanding analytics</h3>
                        <p>How to interpret your restaurant's performance data</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('restaurant-promotions')">
                        <h3><i class="fas fa-bullhorn"></i> Running promotions</h3>
                        <p>How to create and manage special offers</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('restaurant-payouts')">
                        <h3><i class="fas fa-money-bill-wave"></i> Payouts & earnings</h3>
                        <p>How payments are processed and when you'll receive funds</p>
                    </div>
                </div>
            </div>

            <div id="restaurant-menu-guide" class="detailed-guide" style="display: none;">
                <h2><i class="fas fa-clipboard-list"></i> Managing Your Restaurant Menu</h2>
                
                <div class="guide-step">
                    <h3><span class="step-number">1</span> Access your dashboard</h3>
                    <p>Log in to your restaurant account and navigate to the "Menu Management" section.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">2</span> Add new menu items</h3>
                    <p>Click "Add Item" and fill in all required details including name, description, price, and category.</p>
                    <img src="../images/help/add-menu-item.jpg" alt="Adding menu items">
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">3</span> Edit existing items</h3>
                    <p>Click on any menu item to update its details, pricing, or availability status.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">4</span> Organize your menu</h3>
                    <p>Create categories and arrange items to make your menu easy to navigate.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">5</span> Set item availability</h3>
                    <p>Mark items as available/unavailable based on your current inventory.</p>
                </div>
                
                <button class="back-button" onclick="hideGuides()"><i class="fas fa-arrow-left"></i> Back to topics</button>
            </div>
        </div>

        <!-- Delivery Partner Help Content -->
        <div id="delivery-help" class="help-content <?= $user_type === 'delivery' ? 'active' : '' ?>">
            <div class="help-section">
                <h2><i class="fas fa-road"></i> Delivery Basics</h2>
                <div class="topic-list">
                    <div class="topic-card" onclick="showGuide('delivery-getting-started')">
                        <h3><i class="fas fa-play-circle"></i> Getting started</h3>
                        <p>Complete guide to setting up and using the delivery partner app</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('delivery-orders')">
                        <h3><i class="fas fa-shopping-bag"></i> Accepting orders</h3>
                        <p>How to receive and manage delivery requests</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('delivery-navigation')">
                        <h3><i class="fas fa-map-marked-alt"></i> Navigation tips</h3>
                        <p>Best practices for efficient delivery routing</p>
                    </div>
                </div>
            </div>

            <div class="help-section">
                <h2><i class="fas fa-money-bill-alt"></i> Earnings & Payments</h2>
                <div class="topic-list">
                    <div class="topic-card" onclick="showGuide('delivery-earnings')">
                        <h3><i class="fas fa-calculator"></i> Understanding earnings</h3>
                        <p>How your pay is calculated and when you'll receive payments</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('delivery-tips')">
                        <h3><i class="fas fa-hand-holding-usd"></i> Managing tips</h3>
                        <p>How the tipping system works and how to maximize your tips</p>
                    </div>
                    <div class="topic-card" onclick="showGuide('delivery-incentives')">
                        <h3><i class="fas fa-medal"></i> Bonus programs</h3>
                        <p>Information about incentive programs and how to qualify</p>
                    </div>
                </div>
            </div>

            <div id="delivery-getting-started-guide" class="detailed-guide" style="display: none;">
                <h2><i class="fas fa-play-circle"></i> Getting Started as a Delivery Partner</h2>
                
                <div class="guide-step">
                    <h3><span class="step-number">1</span> Download the app</h3>
                    <p>Install the FoodExpress Delivery Partner app from the App Store or Google Play Store.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">2</span> Complete your profile</h3>
                    <p>Provide all required information including your vehicle details and payment preferences.</p>
                    <img src="../images/help/delivery-profile.jpg" alt="Delivery partner profile setup">
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">3</span> Pass the orientation</h3>
                    <p>Complete the short training module to learn about our delivery standards.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">4</span> Go online</h3>
                    <p>Tap the "Go Online" button when you're ready to receive delivery requests.</p>
                </div>
                
                <div class="guide-step">
                    <h3><span class="step-number">5</span> Accept your first order</h3>
                    <p>When you receive a delivery request, review it and tap "Accept" to get started.</p>
                </div>
                
                <button class="back-button" onclick="hideGuides()"><i class="fas fa-arrow-left"></i> Back to topics</button>
            </div>
        </div>

        <div class="contact-support">
            <h3>Still need help?</h3>
            <p>We are available 24/7 to assist you with any questions or issues.</p>
            <a href="support.php"><i class="fas fa-headset"></i> Contact Support</a>
        </div>
    </div>
    <p style="z-index: 1000;" id="scrollToTopBtn" title="Go to top"><img src="images/scrollup icon.jpg" alt="Go to Top"></p>

    <?php include_once '../views/customers/footer.php'?>

    <script>
        // Switch between user type tabs
        document.querySelectorAll('.user-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.user-tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Hide all help content
                document.querySelectorAll('.help-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show selected help content
                const target = tab.getAttribute('data-target');
                document.getElementById(target).classList.add('active');
            });
        });

        // Show detailed guide when topic is clicked
        function showGuide(guideId) {
            // Hide all topic lists and show the selected guide
            document.querySelectorAll('.topic-list, .detailed-guide').forEach(el => {
                el.style.display = 'none';
            });
            
            document.getElementById(guideId + '-guide').style.display = 'block';
            
            // Scroll to the guide
            document.getElementById(guideId + '-guide').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Return to topic list view
        function hideGuides() {
            // Show all topic lists and hide guides
            document.querySelectorAll('.topic-list').forEach(el => {
                el.style.display = 'grid';
            });
            
            document.querySelectorAll('.detailed-guide').forEach(el => {
                el.style.display = 'none';
            });
            
            // Scroll back to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Auto-select tab based on URL hash
        window.addEventListener('DOMContentLoaded', () => {
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const tab = document.querySelector(`.user-tab[data-target="${hash}"]`);
                
                if (tab) {
                    tab.click();
                }
            }
        });
    </script>
    <script src="js/headercolorChangerWhenScrollPage.js"></script>
    <script src="../views/customers/javaScript/scroll_up.js"></script>
</body>
</html>