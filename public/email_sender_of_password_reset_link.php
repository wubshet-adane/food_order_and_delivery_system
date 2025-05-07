<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Composer autoloader

function sendPasswordResetLink($to, $resetLink, $role) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'esraeladmasu958@gmail.com';
        $mail->Password = 'dtfxlonzswmgashc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587; // Port 587 for TLS
        $mail->setFrom('esraeladmasu958@gmail.com', 'G-3 Online Food Ordering System');
        $mail->addAddress($to, $role); // You can set a name here

        $mail->isHTML(true);
        $mail->Subject = "Reset Your Password - G-3 Online Food Ordering System";
        $mail->Body = '
            <div style="background-color: #f8f9fa; padding: 20px; font-family: \'Arial\', sans-serif; line-height: 1.6; color: #333;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <!-- Header with gradient -->
                    <div style="background: linear-gradient(135deg, #e67e22, #d35400); padding: 30px 20px; text-align: center;">
                        <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 600;">G-3 Online Food Ordering</h1>
                        <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0; font-size: 16px;">Delicious meals at your fingertips</p>
                    </div>
                    
                    <!-- Content -->
                    <div style="padding: 30px;">
                        <h2 style="color: #2c3e50; margin-top: 0; font-size: 22px;">Hello ' . htmlspecialchars($role) . ',</h2>
                        
                        <p style="font-size: 16px; margin-bottom: 25px;">
                            We received a request to reset your password. Click the button below to securely reset it:
                        </p>
                        
                        <!-- Main CTA Button -->
                        <div style="text-align: center; margin: 30px 0 40px;">
                            <a href="' . $resetLink . '" style="background: linear-gradient(135deg, #e67e22, #d35400); color: white; display: inline-block; padding: 14px 30px; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 8px rgba(230, 126, 34, 0.3); transition: all 0.3s ease;">
                                Reset My Password
                            </a>
                        </div>
                        
                        <!-- Secondary link -->
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                            <p style="font-size: 14px; margin: 0 0 10px; color: #666;">Or copy and paste this link into your browser:</p>
                            <a href="' . $resetLink . '" style="color: #2980b9; word-break: break-all; font-size: 14px; display: inline-block; padding: 8px 12px; background-color: white; border-radius: 4px; border: 1px solid #eee;">' . $resetLink . '</a>
                        </div>
                        
                        <!-- Security notice -->
                        <div style="border-left: 4px solid #e67e22; padding-left: 15px; margin-bottom: 25px;">
                            <p style="font-size: 14px; color: #666; margin: 0;">
                                <strong>Security notice:</strong> This link will expire in <strong style="color: #d35400;">5 minutes</strong> and can only be used once.
                            </p>
                        </div>
                        
                        <!-- Help text -->
                        <p style="font-size: 14px; color: #777; margin-bottom: 0;">
                            If you didn\'t request this password reset, please ignore this email or contact support if you have questions.
                        </p>
                    </div>
                    
                    <!-- Footer -->
                    <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eee;">
                        <p style="font-size: 12px; color: #999; margin: 0;">
                            &copy; ' . date("Y") . ' G-3 Online Food Ordering System. All rights reserved.<br>
                            <span style="font-size: 11px;">123 Food Street, Cityville, FL 12345</span>
                        </p>
                        
                        <!-- Social links (optional) -->
                        <div style="margin-top: 15px;">
                            <a href="http://localhost:8081/food_ordering_system/public/support.php#form-container" style="display: inline-block; margin: 0 5px;"> support center</a>
                            <a href="http://localhost:8081/food_ordering_system/public/help_center.php" style="display: inline-block; margin: 0 5px;"> help center</a>
                            <a href="http://localhost:8081/food_ordering_system/views/customers/home.php" style="display: inline-block; margin: 0 5px;">home page</a>
                        </div>
                    </div>
                </div>
            </div>
            ';

        $mail->AltBody = "Hello,\nYou requested a password reset. Click this link:\n$resetLink\n\nThis link expires in 5 minutes.";
        
        $mail->send();
        
        return true; // Email sent successfully
    } catch (Exception $e) {
        // Log the error message
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        // Return a user-friendly message
        return $mail->ErrorInfo; // Return the error message for debugging
    }
}
?>