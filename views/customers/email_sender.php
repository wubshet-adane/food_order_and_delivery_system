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
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px; border-radius: 8px; color: #1f2937; max-width: 600px; margin: auto; border: 1px solid #e5e7eb;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h2 style='color: #10b981; font-size: 24px; margin: 0;'>Order Confirmation</h2>
                    <p style='font-size: 14px; color: #6b7280;'>Your order has been placed successfully!</p>
                </div>

                <p style='font-size: 16px; margin-bottom: 15px;'>Hi <strong>$customer_name</strong>,</p>
                
                <p style='font-size: 15px; margin-bottom: 10px;'>We're happy to let you know that your order from <strong>$restaurant_name</strong> has been successfully placed.</p>

                <div style='background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; padding: 15px; margin-top: 15px;'>
                    <p style='margin: 0 0 8px;'><strong>Order ID:</strong> #$order_id</p>
                    <p style='margin: 0 0 8px;'><strong>Status:</strong> <span style='color: #2563eb;'>$status</span></p>
                    <p style='margin: 0 0 8px;'><strong>Secret Code:</strong> <span style='font-weight: bold; color: #dc2626;'>$secret_code</span></p>
                    <p style='margin: 0;'><strong>Total Amount:</strong> <span style='color: #16a34a;'>ETB $amount</span></p>
                </div>

                <p style='margin-top: 20px; font-size: 14px;'>Please keep your secret code safe. You'll need it to confirm your order upon delivery or pickup.</p>

                <div style='margin-top: 25px; text-align: center;'>
                    <a href='http://localhost:8081/food_ordering_system/views/customers/order_history.php' style='background-color: #3b82f6; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 15px;'>View Order</a>
                </div>

                <p style='margin-top: 30px; font-size: 14px; color: #6b7280;'>Thank you for choosing our service!<br>— <em>G-3 Online Food Ordering System</em></p>
            </div>
        ";

        $mail->AltBody = 
            "Hi $customer_name,
            Your order from $restaurant_name has been placed successfully.
            Order Details:
            - Order ID: #$order_id
            - Status: $status
            - Secret Code: $secret_code
            - Total Amount: ETB $amount
            Please keep your secret code safe. You'll need it to confirm your order upon delivery or pickup.
            Thank you for choosing our service!            
            — G-3 Online Food Ordering System  
            Contact: support@g3foods | +251-965-65 86 89 33
        ";
        
        $mail->send();
        
        return 'email sent successfully';
    } catch (Exception $e) {
        // Log the error message
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return 'email not sent';
    }
}
?>