<?php
require_once __DIR__ . '/../config/database.php';
class Place_customer_order_model {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        }

    public function createOrder($user_id, $res_id, $order_status, $order_note, $secret_code) {
        $stmt = $this->conn->prepare("INSERT INTO orders (customer_id, restaurant_id, status, secret_code, o_description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $res_id, $order_status, $secret_code, $order_note);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function addOrderItem($order_id, $product_id, $qty) {
        $stmt = $this->conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $order_id, $product_id, $qty);
        $stmt->execute();
    }

    public function savePayment($order_id, $amount, $delivery_parson_fee, $service_fee, $payment_method, $screenshot, $payment_trans) {
        $stmt = $this->conn->prepare("
            INSERT INTO payments (order_id, amount, delivery_person_fee, service_fee, payment_method, transaction_id, payment_file) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }
        $stmt->bind_param("idddsss", $order_id, $amount,$delivery_parson_fee, $service_fee, $payment_method, $payment_trans, $screenshot);
        $stmt->execute();
    }

    public function clearCart($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}
?>