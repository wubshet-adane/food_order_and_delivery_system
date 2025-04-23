<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pending | Food Delivery Partner</title>
    <style>
        :root {
            --primary: #FF9900;
            --primary-dark: #443311;
            --secondary: #1DD1A1;
            --dark: #2F3640;
            --light: #F5F6FA;
            --gray: #DCDDE1;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .pending-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .status-icon {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        p {
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 2rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gray);
            z-index: 1;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            position: relative;
        }

        .step-number {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--gray);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .step-number.active {
            background: var(--primary);
        }

        .step-label {
            font-size: 0.8rem;
            color: var(--dark);
            text-align: center;
        }

        .contact-info {
            background: rgba(255, 153, 0, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            text-align: left;
        }

        .contact-info h3 {
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .pending-container {
                padding: 1.5rem;
            }
            
            .status-icon {
                font-size: 3rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .timeline {
                margin: 1.5rem 0;
            }
            
            .step-label {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <div class="status-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
        </div>
        
        <h1>Your Application is Under Review</h1>
        
        <p>Thank you for submitting your application to become a delivery partner. Our team is currently reviewing your information and documents.</p>
        
        <p>This process typically takes <strong>1-3 business days</strong>. We'll notify you via email once your account is approved.</p>
        
        <div class="timeline">
            <div class="timeline-step">
                <div class="step-number active">1</div>
                <div class="step-label">Application Submitted</div>
            </div>
            <div class="timeline-step">
                <div class="step-number">2</div>
                <div class="step-label">Under Review</div>
            </div>
            <div class="timeline-step">
                <div class="step-number">3</div>
                <div class="step-label">Approval</div>
            </div>
        </div>
        
        <div class="contact-info">
            <h3>Need Help?</h3>
            <p>If you have any questions about your application status, please contact our support team at <strong>support@deliveryapp.com</strong> or call <strong>(800) 123-4567</strong>.</p>
        </div>
        
        <a href="dashboard.html" class="btn">Go to Dashboard</a>
    </div>
</body>
</html>