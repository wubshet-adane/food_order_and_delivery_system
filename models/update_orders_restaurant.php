<?php
require_once __DIR__ . '/../config/database.php';
class OrderUpdate
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    //to get specific restaurant review
    public function orderUpdateFunction($owner_id)
    {
            
        // Handle status updates
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['update_status'])) {
                $order_id = $_POST['order_id'];
                $new_status = $_POST['new_status'];
                
                // Validate status transition
                $valid_statuses = ['Accepted', 'Preparing', 'Out for Delivery'];
                $stmt = $this->conn->prepare("SELECT status FROM orders WHERE order_id = ? AND restaurant_id IN (SELECT restaurant_id FROM restaurants WHERE owner_id = ?)");
                $stmt->bind_param("ii", $order_id, $owner_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $order = $result->fetch_assoc();
                    $current_status = $order['status'];
                    
                    // Check if the new status is valid
                    if (in_array($new_status, $valid_statuses)) {
                        // Update status
                        $update_stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
                        $update_stmt->bind_param("si", $new_status, $order_id);
                        $update_stmt->execute();
                        
                        if ($update_stmt->affected_rows > 0) {
                            $success_msg = "Order status updated successfully!";
                        } else {
                            $error_msg = "Failed to update order status.";
                        }
                    } else {
                        $error_msg = "Invalid status transition.";
                    }
                } else {
                    $error_msg = "Order not found or you don't have permission to update it.";
                }
            }
            
            // Handle accept/reject actions
            if (isset($_POST['action'])) {
                $order_id = $_POST['order_id'];
                $action = $_POST['action'];
                
                // Verify the order belongs to this owner's restaurant
                $stmt = $this->conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND restaurant_id IN (SELECT restaurant_id FROM restaurants WHERE owner_id = ?)");
                $stmt->bind_param("ii", $order_id, $owner_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    if ($action == 'accept') {
                        $new_status = 'Accepted';
                    } elseif ($action == 'reject') {
                        $new_status = 'Cancelled';
                    }
                    
                    $update_stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
                    $update_stmt->bind_param("si", $new_status, $order_id);
                    $update_stmt->execute();
                    
                    if ($update_stmt->affected_rows > 0) {
                        $success_msg = "Order has been " . ($action == 'accept' ? "accepted" : "rejected") . "!";
                    } else {
                        $error_msg = "Failed to update order status.";
                    }
                } else {
                    $error_msg = "Order not found or you don't have permission to modify it.";
                }
            }
        }
    }
}
?>