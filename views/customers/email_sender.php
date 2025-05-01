<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php'; // Composer autoloader

function sendOrderCompleteEmail($to, $order_id, $customer_name, $restaurant_name, $secret_code, $status, $amount) {
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
        $mail->addAddress($to, $customer_name);

        $mail->isHTML(true);
        $mail->Subject = "Order #$order_id plaeced successfully!";
        $mail->Body = "<h3>Hello $customer_name,</h3>
            <p>Your order from <strong>$restaurant_name</strong> is now <strong>placed successfully!</strong>.</p>
            <ul>
                <li><strong>Order ID:</strong> #$order_id</li>
                <li><strong>Status:</strong> $status</li>
                <li><strong>Secret Code:</strong> $secret_code</li>
                <li><strong>Amount:</strong> ETB $amount</li>
            </ul>
            <p>Thank you for using our food ordering platform!</p>
            <p>Regards,<br><em>Food Ordering System</em></p>
        ";

        $mail->AltBody = "Hello $customer_name,\nYour order from $restaurant_name is completed.\nOrder ID: $order_id\nStatus: $status\nSecret Code: $secret_code\nAmount: ETB $amount\nThank you!";

        $mail->send();
        
        return 'email sent successfully';
    } catch (Exception $e) {
        // Log the error message
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return 'email not sent';
    }
}
?>