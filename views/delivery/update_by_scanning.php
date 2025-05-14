<?php
    session_start();
        require_once '../../config/database.php';

    // confirm ordered to delivered by scanning secret code QR code from customers phone
    if ( isset($_GET['request_name']) && $_GET['request_name'] == 'Ajax') {
        $user_id = $_SESSION['user_id'];


        $scanData = json_decode(file_get_contents("php://input"), true);
        if($scanData){
            $orderId = $scanData['order_id'] ?? null;
            $secretCode = $scanData['secret_code'] ?? null;

            // Validation and logic
            if ($orderId && $secretCode) {
                $stmt = $conn->prepare("SELECT secret_code FROM orders WHERE order_id = ?");
                $stmt->bind_param("i", $orderId);
                $stmt->execute();
                $result = $stmt->get_result();
                $order = $result->fetch_assoc();

                if ($order && $order['secret_code'] === $secretCode) {
                    updateOrderStatus($conn, $orderId, 'Delivered', $deliveryPersonId);
                    $success = true;
                } else {
                    $error = "❌ Invalid secret code or order ID.";
                }
            } else {
                $error = "❌ Missing order_id or secret_code.";
            }

            // Send JSON response
            header('Content-Type: application/json');
            echo json_encode([
                "success" => $success,
                "error" => $error
            ]);
        }
    }

    function updateOrderStatus($conn, $orderId, $status, $deliveryPersonId) {
    $stmt = $conn->prepare("UPDATE orders SET status = ?, delivered_at = now() WHERE order_id = ? AND delivery_person_id = ?");
    $stmt->bind_param("sii", $status, $orderId, $deliveryPersonId);
    $stmt->execute();
}
?>

