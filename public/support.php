<?php
session_start();
require_once '../config/database.php';

// Determine user type for customized support
$user_type = 'guest'; // default
if (isset($_SESSION['userType'])) {
    $user_type = $_SESSION['userType']; // customer, restaurant, or delivery
}

// Handle support ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $user_id = $_SESSION['user_id'] ?? 0;
    $user_type = $_POST['user_type'];
    $status = 'open';
    
    $stmt = $conn->prepare("INSERT INTO support_tickets 
                          (user_id, user_type, name, email, subject, message, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssss", $user_id, $user_type, $name, $email, $subject, $message, $status);
    
    if ($stmt->execute()) {
        $success_message = "Your support ticket has been submitted successfully!";
    } else {
        $error_message = "Error submitting your ticket. Please try again.";
    }
    $stmt->close();
}

$frequentlyAskedQuestions = new Faqs($conn);
$faqs = $frequentlyAskedQuestions->freqAQ();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - FoodExpress</title>
    <link rel="icon" href="images/logo-icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/support.css">
</head>
<body>
    <header>
        <div class="topbarInHeader">
            <div class="logopart">
                <img src="images/logo.jpg" alt="logo image" onclick="window.location.href='../views/customers/home.php'">
            </div>
            <div class="backLink">
                <a href="javascript:history.back()"><i class="fa-solid fa-arrow-left"></i>  Back to homepage</a>
            </div>
        </div>
        <div class="container">
            <h1><i class="fas fa-headset"></i> Support Center</h1>
            <p>Find answers to common questions or contact our support team for personalized assistance.</p>
        </div>
    </header>

    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= $success_message ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-error">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="support-sections">
            <div class="support-card customer">
                <h2><i class="fas fa-user"></i> Customer Support</h2>
                <p>Having trouble with your order? Need help with payments or account issues? We're here to help you with any questions about using our platform as a customer.</p>
                <ul style="margin-top: 15px; padding-left: 20px;">
                    <li>Order tracking assistance</li>
                    <li>Payment issues</li>
                    <li>Refund requests</li>
                    <li>Account management</li>
                    <li>Rating system</li>
                </ul>
            </div>

            <div class="support-card restaurant">
                <h2><i class="fas fa-utensils"></i> Restaurant Support</h2>
                <p>Restaurant owners can get help with menu management, order processing, payout questions, and any other restaurant-related inquiries.</p>
                <ul style="margin-top: 15px; padding-left: 20px;">
                    <li>Restaurant management</li>
                    <li>Menu management</li>
                    <li>Account management</li>
                    <li>Order processing</li>
                    <li>Payout questions</li>
                    <li>Technical issues</li>
                </ul>
            </div>

            <div class="support-card delivery">
                <h2><i class="fas fa-motorcycle"></i> Delivery Partner Support</h2>
                <p>Delivery partners can find help with order pickup, navigation, earnings, and any other delivery-related questions.</p>
                <ul style="margin-top: 15px; padding-left: 20px;">
                    <li>Account Management</li>
                    <li>Order pickup issues</li>
                    <li>Navigation help</li>
                    <li>Earnings questions</li>
                </ul>
            </div>
        </div>

        <div class="faq-section">
            <h2><i class="fas fa-question-circle"></i> Frequently Asked Questions</h2>
            
            <div class="faq-categories">
                <div class="faq-category active" data-category="general">General</div>
                <div class="faq-category" data-category="customer">Customer</div>
                <div class="faq-category" data-category="restaurant">Restaurant</div>
                <div class="faq-category" data-category="delivery">Delivery</div>
            </div>

            <div class="faq-items active" id="general-faq">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I create an account?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>You can create an account by clicking on the "Sign Up" button at the top right corner of our website. Choose your user type (customer, restaurant owner, or delivery partner) and follow the instructions to complete your registration.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We accept various payment methods including credit/debit cards, mobile payments (Telebirr, CBE Birr), and cash on delivery for certain restaurants.</p>
                    </div>
                </div>
            </div>

            <div class="faq-items" id="customer-faq">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How can I track my order?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Once your order is confirmed and assigned to a delivery partner, you can track it in real-time through the "My Orders" section in your account. You'll see the delivery partner's location and estimated arrival time.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What should I do if my food arrives late?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We apologize for any delays. You can contact our customer support through this page, and we'll investigate the issue. Depending on the circumstances, we may offer compensation for significant delays.</p>
                    </div>
                </div>
            </div>

            <div class="faq-items" id="restaurant-faq">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I update my restaurant's menu?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Log in to your restaurant dashboard, navigate to the "Menu Management" section, and make the necessary changes. All updates will be reflected on the platform within a few minutes.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>When will I receive my payouts?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Payouts are processed weekly every Monday for the previous week's orders. It may take 1-3 business days for the funds to appear in your account depending on your bank.</p>
                    </div>
                </div>
            </div>

            <div class="faq-items" id="delivery-faq">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How are delivery partners paid?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Delivery partners earn per delivery with rates varying by distance. Earnings are calculated daily and paid out weekly every Monday to your registered payment method.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What should I do if I can't find the customer's address?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>First, try contacting the customer through the in-app messaging system. If you're still unable to locate the address, contact our support team for assistance.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h2><i class="fas fa-envelope"></i> Contact Support</h2>
            
            <form method="POST" action="support.php">
                <div class="user-type-selector">
                    <div class="user-type-btn customer <?= $user_type === 'customer' ? 'active' : '' ?>" data-type="customer">
                        <i class="fas fa-user"></i>
                        <span>Customer</span>
                    </div>
                    <div class="user-type-btn restaurant <?= $user_type === 'restaurant' ? 'active' : '' ?>" data-type="restaurant">
                        <i class="fas fa-utensils"></i>
                        <span>Restaurant</span>
                    </div>
                    <div class="user-type-btn delivery <?= $user_type === 'delivery' ? 'active' : '' ?>" data-type="delivery">
                        <i class="fas fa-motorcycle"></i>
                        <span>Delivery Partner</span>
                    </div>
                </div>
                
                <input type="hidden" name="user_type" id="user_type" value="<?= $user_type ?>">
                
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required 
                           value="<?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                
                <button type="submit" name="submit_ticket" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit Ticket
                </button>
            </form>
        </div>
    </div>

    <script>
        // FAQ functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                question.classList.toggle('active');
                answer.classList.toggle('active');
            });
        });

        // FAQ category switching
        document.querySelectorAll('.faq-category').forEach(category => {
            category.addEventListener('click', () => {
                // Remove active class from all categories
                document.querySelectorAll('.faq-category').forEach(c => {
                    c.classList.remove('active');
                });
                
                // Add active class to clicked category
                category.classList.add('active');
                
                // Hide all FAQ items
                document.querySelectorAll('.faq-items').forEach(items => {
                    items.classList.remove('active');
                });
                
                // Show selected FAQ items
                const categoryId = category.getAttribute('data-category') + '-faq';
                document.getElementById(categoryId).classList.add('active');
            });
        });

        // User type selection
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons
                document.querySelectorAll('.user-type-btn').forEach(b => {
                    b.classList.remove('active');
                });
                
                // Add active class to clicked button
                btn.classList.add('active');
                
                // Update hidden input value
                document.getElementById('user_type').value = btn.getAttribute('data-type');
            });
        });

        // Auto-expand FAQ based on URL hash
        window.addEventListener('DOMContentLoaded', () => {
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const category = hash.split('-')[0];
                
                if (category) {
                    const categoryBtn = document.querySelector(`.faq-category[data-category="${category}"]`);
                    if (categoryBtn) {
                        categoryBtn.click();
                    }
                    
                    const question = document.getElementById(hash);
                    if (question) {
                        question.scrollIntoView();
                        question.click();
                    }
                }
            }
        });
    </script>
</body>
</html>