<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php'; // Composer autoloader

function sendOrderCompleteEmail($to, $order_id, $customer_name, $amount) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '0967490154w@gmail.com';       // 🔁 Replace with your Gmail
        $mail->Password = '12ab,.12';         // 🔁 App Password (not Gmail password)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('0967490154w@gmail.com', 'Food Ordering System');
        $mail->addAddress($to, $customer_name);

        $mail->isHTML(true);
        $mail->Subject = "🎉 Order #$order_id Completed!";
        $mail->Body = "
            <h3>Hello $customer_name,</h3>
            <p>Your order <strong>#{$order_id}</strong> has been <strong>successfully completed</strong>.</p>
            <p>We appreciate your trust in our service. G3 food ordering and delivering platform! 🍽️</p>
            <hr>
            <small>you pay #ETB{$amount} for this order</small>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: {$mail->ErrorInfo}");
        return false;
    }
}
