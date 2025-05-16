<?php
    session_start();
    include '../../config/database.php';

    if(isset($_GET['email'])){
        $email = $_SESSION['email'];
        $stmt = $conn->prepare("SELECT *  FROM users WHERE role = 'restaurant' AND email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pending | Restaurant Owner Partner</title>
    <link rel="stylesheet" href="css/owner_registration_success.css">
</head>
<body>
    <div class="pending-container">
        <div class="status-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
        </div>
        <p style="padding: 1rem; border-radius: 5px; margin : 4px 1rem; color : #111111; background-color: #F3B3B3FF; ">
            <?php if(isset($_GET['message'])) echo $_GET['message'];?>
        </p>
        <a href="javascript:history.back()">back</a>
        
        <h1>Your Application is Under Review</h1>
        
        <p>Thank you for submitting your application to become a Restaurant Merchant. Our team is currently reviewing your information and documents.</p>
        
        <p>This process typically takes <strong>1-3 business days</strong>. We'll notify you via email once your account is approved.</p>
        
        <div class="timeline">
            <div class="timeline-step">
                <div class="step-number active">1</div>
                <div class="step-label">Application Submitted</div>
            </div>
            <?php if(isset($result['status']) && $result['status'] == 'approved'){?>
            <div class="timeline-step">
                <div class="step-number active">2</div>
                <div class="step-label">Approved</div>
            </div>
            <?php }else{ ?>
            <div class="timeline-step">
                <div class="step-number">2</div>
                <div class="step-label">Approved</div>
            </div>
            <?php }?>
        </div>
        
        <div class="contact-info">
            <h3>Need Help?</h3>
            <p>If you have any questions about your application status, please contact our support team at <strong><a href="../../public/support.php">contact us</a></strong> or call <strong>(+251) 965868933</strong>.</p>
        </div>
        
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>