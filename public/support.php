<?php
ob_start(); // 🛡️ Output buffering starts

session_start();
require_once __DIR__ . "/../models/ask_support_model.php";


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
    $user_id = $_SESSION['user_id'];
    $user_type = $_POST['user_type'];
    //$status = 'open';
    //upload request
    $submitSupport = new Faqs($conn);
    $SSR = $submitSupport->submitSupportRequest($user_id, $user_type, $name, $email, $subject, $message);
 
}
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
    <link rel="stylesheet" href="../views/customers/css/footer.css">
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
            <p>Find answers to common questions or contact our us for personalized assistance.</p>
        </div>
    </header>

    <div class="container">
        <?php if (isset($SSR) && !empty($SSR['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($SSR['success_message']) ?>
            </div>
        <?php elseif (isset($SSR) && !empty($SSR['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($SSR['error_message']) ?>
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
                <?php 
                $role = 'general';
                //get frequently assked questions
                $supportResponse = new Faqs($conn);
                $faqs = $supportResponse->getSupportResponse($role, 'answered');
                if ($faqs && count($faqs) > 0): 
                    foreach ($faqs as $row):
                ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><?=$row['message']?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><?=$row['answer']?></p>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <p>General frequently asked questions not found!</p>
                <?php endif; ?> 
            </div>

            <div class="faq-items" id="customer-faq">
            <?php 
                $role = 'customer';
                //get frequently assked questions
                $supportResponse = new Faqs($conn);
                $faqs = $supportResponse->getSupportResponse($role, 'answered');
                if ($faqs && count($faqs) > 0): 
                    foreach ($faqs as $row):
                ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><?=$row['message']?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><?=$row['answer']?></p>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <p>General frequently asked questions not found!</p>
                <?php endif; ?> 
            </div>

            <div class="faq-items" id="restaurant-faq">
                <?php 
                $role = 'restaurant';
                //get frequently assked questions
                $supportResponse = new Faqs($conn);
                $faqs = $supportResponse->getSupportResponse($role, 'answered');
                if ($faqs && count($faqs) > 0): 
                    foreach ($faqs as $row):
                ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><?=$row['message']?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><?=$row['answer']?></p>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <p>General frequently asked questions not found!</p>
                <?php endif; ?> 
            </div>

            <div class="faq-items" id="delivery-faq">
            <?php 
                $role = 'delivery';
                //get frequently assked questions
                $supportResponse = new Faqs($conn);
                $faqs = $supportResponse->getSupportResponse($role, 'answered');
                if ($faqs && count($faqs) > 0): 
                    foreach ($faqs as $row):
                ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><?=$row['message']?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p><?=$row['answer']?></p>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <p>General frequently asked questions not found!</p>
                <?php endif; ?> 
                
            </div>
        </div>

        <div class="contact-form" id="form-container">
            <h2><i class="fas fa-envelope"></i> Contact Support</h2>
            
            <form method="POST" action="support.php" <?= isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] ? '' : 'onsubmit="event.preventDefault(); alert(\'You must be logged in to submit.\');"' ?>>
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
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Your Message *</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                
                <button type="submit" name="submit_ticket" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit Question
                </button>
            </form>
        </div>
    </div>
    <?php include_once '../views/customers/footer.php'?>

    <script src="js/support.js"></script>
</body>
</html>