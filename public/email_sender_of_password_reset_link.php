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
            <div style="background-color: #f4f6f8; padding: 30px; font-family: Arial, sans-serif;">
                <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h2 style="color: #2c3e50; text-align: center;">G-3 Online Food Ordering System</h2>
                    <hr style="border: none; border-top: 2px solid #e67e22; margin: 20px 0;">
                    <p style="font-size: 16px; color: #333;">Hello <strong>' . htmlspecialchars($role) . '</strong>,</p>
                    <p style="font-size: 16px; color: #333;">
                        We received a request to reset your password. To proceed, click the button below:
                    </p>
                    <div style="text-align: center; margin: 30px auto;">
                        <a href="' . $resetLink . '" style="background-color: #e67e22; color: #ffffff; display: inline-block; padding: 12px 25px; margin: auto; text-decoration: none; border-radius: 5px; font-size: 16px;">
                            Reset My Password
                        </a>
                    </div>
                    <p style="font-size: 14px; color: #666;">
                        Or copy and paste this link into your browser:<br>
                        <a href="' . $resetLink . '" style="color: #2980b9;">' . $resetLink . '</a>
                    </p>
                    <p style="font-size: 14px; color: #666;">
                        This link will expire in <strong>5 minutes</strong> for your security.
                    </p>
                    <p style="font-size: 14px; color: #999; text-align: center; margin-top: 40px;">
                        &copy; ' . date("Y") . ' G-3 Online Food Ordering System. All rights reserved.
                    </p>
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