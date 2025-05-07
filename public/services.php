<?php
session_start();
require_once '../config/database.php';

// Determine user type for personalized content
$user_type = isset($_SESSION['userType']) ? $_SESSION['userType'] : 'guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - G-3 online food ordering system</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/services.css">
  
</head>
<body>
    <header class="services-header">
        <div class="container">
            <h1>Our Services</h1>
            <p>Discover how FoodExpress connects hungry customers with great local restaurants and provides earning opportunities for delivery partners</p>
        </div>
    </header>

    <div class="container">
        <div class="user-type-tabs">
            <button class="user-tab customer <?= $user_type === 'customer' ? 'active' : '' ?>" data-target="customer-services">
                <i class="fas fa-user"></i> For Customers
            </button>
            <button class="user-tab restaurant <?= $user_type === 'restaurant' ? 'active' : '' ?>" data-target="restaurant-services">
                <i class="fas fa-utensils"></i> For Restaurants
            </button>
            <button class="user-tab delivery <?= $user_type === 'delivery' ? 'active' : '' ?>" data-target="delivery-services">
                <i class="fas fa-motorcycle"></i> For Delivery Partners
            </button>
        </div>

        <!-- Customer Services -->
        <div id="customer-services" class="services-content <?= $user_type === 'customer' ? 'active' : '' ?>">
            <div class="services-section">
                <h2><i class="fas fa-mobile-alt"></i> Ordering Experience</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-search"></i> Restaurant Discovery</h3>
                        <p>Browse hundreds of local restaurants with detailed menus, photos, and ratings to find exactly what you're craving.</p>
                        <a href="#" class="btn-learn">Learn More</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-bolt"></i> Express Delivery</h3>
                        <p>Get your food delivered in as little as 30 minutes with our optimized delivery network.</p>
                        <a href="#" class="btn-learn">Learn More</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-map-marker-alt"></i> Real-Time Tracking</h3>
                        <p>Follow your order from restaurant to doorstep with our live GPS tracking.</p>
                        <a href="#" class="btn-learn">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="services-section">
                <h2><i class="fas fa-percentage"></i> Special Features</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-tag"></i> Deals & Promotions</h3>
                        <p>Exclusive discounts and special offers from your favorite restaurants.</p>
                        <a href="#" class="btn-learn">View Current Deals</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-user-friends"></i> Group Ordering</h3>
                        <p>Easily coordinate meals for your office, family, or group with our shared cart feature.</p>
                        <a href="#" class="btn-learn">Learn How</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-history"></i> Order History</h3>
                        <p>Quickly reorder your favorites from your order history with just one tap.</p>
                        <a href="#" class="btn-learn">See How It Works</a>
                    </div>
                </div>
            </div>

            <div class="pricing-section">
                <h2><i class="fas fa-money-bill-wave"></i> Transparent Pricing</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-shipping-fast"></i> Delivery Fees</h3>
                        <p>Small delivery fee based on distance, clearly displayed before checkout. Many restaurants offer free delivery promotions.</p>
                        <p><strong>Typical fee:</strong> ETB 15-35</p>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-file-invoice"></i> Service Fee</h3>
                        <p>Small service fee helps operate our platform and provide 24/7 customer support.</p>
                        <p><strong>Standard rate:</strong> 5% of order total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restaurant Services -->
        <div id="restaurant-services" class="services-content <?= $user_type === 'restaurant' ? 'active' : '' ?>">
            <div class="services-section">
                <h2><i class="fas fa-chart-line"></i> Growth Solutions</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-users"></i> New Customer Acquisition</h3>
                        <p>Reach thousands of hungry customers in your area who may not know about your restaurant.</p>
                        <a href="#" class="btn-learn">Learn More</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-bullhorn"></i> Marketing & Promotion</h3>
                        <p>Featured placements, email campaigns, and social media promotion to drive more orders.</p>
                        <a href="#" class="btn-learn">See Options</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-tools"></i> Business Tools</h3>
                        <p>Real-time analytics, customer feedback, and inventory management tools.</p>
                        <a href="#" class="btn-learn">Explore Tools</a>
                    </div>
                </div>
            </div>

            <div class="services-section">
                <h2><i class="fas fa-truck"></i> Delivery Solutions</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-network-wired"></i> Delivery Network</h3>
                        <p>Access to our fleet of professional delivery partners, available on-demand.</p>
                        <a href="#" class="btn-learn">How It Works</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-tablet-alt"></i> POS Integration</h3>
                        <p>Seamless integration with popular point-of-sale systems to streamline operations.</p>
                        <a href="#" class="btn-learn">View Integrations</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-box-open"></i> Packaging Solutions</h3>
                        <p>Special packaging options to ensure food arrives fresh and presentable.</p>
                        <a href="#" class="btn-learn">Get Supplies</a>
                    </div>
                </div>
            </div>

            <div class="pricing-section">
                <h2><i class="fas fa-calculator"></i> Restaurant Pricing</h2>
                <p style="text-align: center; margin-bottom: 30px;">Flexible plans designed for restaurants of all sizes</p>
                
                <div class="pricing-cards">
                    <div class="pricing-card">
                        <h3>Starter</h3>
                        <div class="price">15% <span>per order</span></div>
                        <ul>
                            <li>Basic listing</li>
                            <li>Order management</li>
                            <li>Customer support</li>
                            <li>Delivery network access</li>
                        </ul>
                        <a href="register.php?type=restaurant" class="btn-signup">Get Started</a>
                    </div>
                    
                    <div class="pricing-card">
                        <h3>Professional</h3>
                        <div class="price">12% <span>per order</span></div>
                        <ul>
                            <li>Everything in Starter</li>
                            <li>Enhanced listing</li>
                            <li>Basic analytics</li>
                            <li>Priority support</li>
                        </ul>
                        <a href="register.php?type=restaurant" class="btn-signup">Upgrade Now</a>
                    </div>
                    
                    <div class="pricing-card">
                        <h3>Enterprise</h3>
                        <div class="price">Custom</div>
                        <ul>
                            <li>Everything in Professional</li>
                            <li>Dedicated account manager</li>
                            <li>Advanced analytics</li>
                            <li>POS integration</li>
                            <li>Custom contract</li>
                        </ul>
                        <a href="contact.php" class="btn-signup">Contact Sales</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Partner Services -->
        <div id="delivery-services" class="services-content <?= $user_type === 'delivery' ? 'active' : '' ?>">
            <div class="services-section">
                <h2><i class="fas fa-road"></i> Delivery Opportunities</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-clock"></i> Flexible Schedule</h3>
                        <p>Work when you want - full time, part time, or just during peak hours.</p>
                        <a href="#" class="btn-learn">See How It Works</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-money-bill-wave"></i> Earnings Potential</h3>
                        <p>Competitive pay with 100% of tips. Earn bonuses during busy times.</p>
                        <a href="#" class="btn-learn">Calculate Earnings</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-gas-pump"></i> Vehicle Options</h3>
                        <p>Deliver by car, motorcycle, bicycle, or even on foot in some areas.</p>
                        <a href="#" class="btn-learn">Requirements</a>
                    </div>
                </div>
            </div>

            <div class="services-section">
                <h2><i class="fas fa-tools"></i> Partner Support</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-map-marked-alt"></i> Navigation Tools</h3>
                        <p>Optimized routing and turn-by-turn navigation to maximize efficiency.</p>
                        <a href="#" class="btn-learn">App Features</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-shield-alt"></i> Insurance Coverage</h3>
                        <p>Accident insurance included while you're on active deliveries.</p>
                        <a href="#" class="btn-learn">Coverage Details</a>
                    </div>
                    <div class="service-card">
                        <h3><i class="fasfa-hands-helping"></i> Support Network</h3>
                        <p>24/7 support team and local hubs for assistance when you need it.</p>
                        <a href="#" class="btn-learn">Get Help</a>
                    </div>
                </div>
            </div>

            <div class="services-section">
                <h2><i class="fas fa-chart-pie"></i> Earnings Breakdown</h2>
                <div class="service-grid">
                    <div class="service-card">
                        <h3><i class="fas fa-money-bill-alt"></i> Base Pay</h3>
                        <p>Per-delivery fee based on distance and time, typically ETB 25-50 per delivery.</p>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-gift"></i> Tips</h3>
                        <p>Keep 100% of customer tips - average ETB 10-30 per delivery.</p>
                    </div>
                    <div class="service-card">
                        <h3><i class="fas fa-star"></i> Bonuses</h3>
                        <p>Earn extra during peak times, for completing challenges, or maintaining high ratings.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="testimonials">
            <h2 style="text-align: center; margin-bottom: 30px;">What People Say About Us</h2>
            
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "FoodExpress has helped our restaurant reach new customers and increase revenue by 40% without adding any staff. The delivery service is reliable and the platform is easy to use."
                    </div>
                    <div class="testimonial-author">
                        <img src="../images/testimonials/restaurant-owner.jpg" alt="Restaurant Owner" class="author-avatar">
                        <div class="author-info">
                            <h4>Daniel M.</h4>
                            <p>Owner, Addis Ababa Restaurant</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "I love being able to order from all my favorite local spots in one app. The delivery is always fast and the food arrives hot. It's changed how I eat!"
                    </div>
                    <div class="testimonial-author">
                        <img src="../images/testimonials/customer.jpg" alt="Customer" class="author-avatar">
                        <div class="author-info">
                            <h4>Selam W.</h4>
                            <p>FoodExpress Customer</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Delivering for FoodExpress gives me the flexibility I need as a student. I can work between classes and earn good money. The app makes everything simple."
                    </div>
                    <div class="testimonial-author">
                        <img src="../images/testimonials/driver.jpg" alt="Delivery Partner" class="author-avatar">
                        <div class="author-info">
                            <h4>Yohannes T.</h4>
                            <p>FoodExpress Delivery Partner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of happy customers, restaurant partners, and delivery drivers who are already enjoying the FoodExpress platform.</p>
            
            <?php if ($user_type === 'guest'): ?>
                <a href="register.php" class="cta-btn">Sign Up Now</a>
            <?php elseif ($user_type === 'customer'): ?>
                <a href="restaurants.php" class="cta-btn">Order Food Now</a>
            <?php elseif ($user_type === 'restaurant'): ?>
                <a href="dashboard.php" class="cta-btn">Go to Restaurant Dashboard</a>
            <?php elseif ($user_type === 'delivery'): ?>
                <a href="driver.php" class="cta-btn">Go to Driver App</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.user-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.user-tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Hide all content
                document.querySelectorAll('.services-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show selected content
                const target = tab.getAttribute('data-target');
                document.getElementById(target).classList.add('active');
            });
        });

        // Auto-scroll to relevant section if coming from another page
        window.addEventListener('DOMContentLoaded', () => {
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const tab = document.querySelector(`.user-tab[data-target="${hash}"]`);
                
                if (tab) {
                    tab.click();
                    document.getElementById(hash).scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    </script>
</body>
</html>