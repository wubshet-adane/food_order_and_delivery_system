<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

// Initialize response variables
$success = false;
$error = null;

// Check if Ajax request and user is logged in
if (isset($_GET['request_name']) && $_GET['request_name'] === 'Ajax' && isset($_SESSION['user_id'])) {
    $deliveryPersonId = $_SESSION['user_id']; // Logged-in delivery person's ID

    $scanData = json_decode(file_get_contents("php://input"), true);

    if ($scanData) {
        $orderId = $scanData['order_id'] ?? null;
        $secretCode = $scanData['secret_code'] ?? null;

        if ($orderId && $secretCode) {
            // Check if secret code matches
            $stmt = $conn->prepare("SELECT secret_code FROM orders WHERE order_id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();

            if ($order && $order['secret_code'] === $secretCode) {
                // Update status to Delivered and process delivery fee
                if (updateOrderStatus($conn, $orderId, 'Delivered', $deliveryPersonId)) {
                    $success = true;
                } else {
                    $error = "⚠️ Failed to update order status or payment.";
                }
            } else {
                $error = "❌ Invalid secret code or order ID.";
            }
        } else {
            $error = "❌ Missing order_id or secret_code.";
        }
    } else {
        $error = "❌ Invalid JSON request.";
    }
} else {
    $error = "⛔ Unauthorized or invalid request.";
}

// Return response as JSON
echo json_encode([
    "success" => $success,
    "error" => $error
]);


// Function to update order and delivery partner balance using transaction
function updateOrderStatus($conn, $orderId, $status, $deliveryPersonId) {
    $conn->begin_transaction();

    try {
        // Step 1: Update order
        $stmt = $conn->prepare("UPDATE orders SET status = ?, delivered_at = NOW() WHERE order_id = ? AND delivery_person_id = ?");
        $stmt->bind_param("sii", $status, $orderId, $deliveryPersonId);
        $stmt->execute();

        // Step 2: Process balance only if status is 'Delivered'
        if (strtolower($status) === 'delivered') {
            $feeStmt = $conn->prepare("SELECT delivery_person_fee FROM payments WHERE order_id = ?");
            $feeStmt->bind_param("i", $orderId);
            $feeStmt->execute();
            $feeResult = $feeStmt->get_result();

            if ($feeRow = $feeResult->fetch_assoc()) {
                $deliveryFee = $feeRow['delivery_person_fee'];

                // Step 3: Update balance
                $updateStmt = $conn->prepare("UPDATE delivery_partners SET balance = balance + ? WHERE user_id = ?");
                $updateStmt->bind_param("di", $deliveryFee, $deliveryPersonId);
                $updateStmt->execute();
            } else {
                throw new Exception("Delivery fee not found for order_id = $orderId");
            }
        }

        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        return false;
    }
}
?>
