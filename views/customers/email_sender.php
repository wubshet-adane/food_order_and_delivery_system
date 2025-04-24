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
        $mail->Username = '0967490154w@gmail.com';
        $mail->Password = '12ab,.12';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->SMTPDebug = 2; // or 3 for more details
        $mail->Debugoutput = 'html'; // Debug output format
        $mail->setFrom('0967490154w@gmail.com', 'Food Ordering System');
        $mail->addAddress($to, $customer_name);

        $mail->isHTML(true);
        $mail->Subject = "🎉 Order #$order_id Completed!";
        $mail->Body = "<h3>Hello $customer_name,</h3>
            <p>Your order from <strong>$restaurant_name</strong> is now <strong>completed</strong>.</p>
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
        return true;
    } catch (Exception $e) {
        error_log("Email error: {$mail->ErrorInfo}");
        return false;

    }
}
