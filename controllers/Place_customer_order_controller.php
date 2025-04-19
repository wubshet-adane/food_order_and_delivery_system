<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header ('Location: ../views/auth/customer_login.php');
    exit();
}


// 👇 Instantiate controller and place order
require_once __DIR__ . '/../config/database.php'; // Ensure database connection available
require_once __DIR__ . '/../models/place_order_model.php';
require_once __DIR__ . '/../models/cart.php';
require_once __DIR__ . '/../views/customers/email_sender.php';

// Only proceed if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Sanitize Inputs
        $customer_id = $_SESSION['user_id']; // Normally from session
        $res_id = $_POST['res_id'] ?? null;
        $amount = $_POST['grand_total'] ?? null;
        $order_note = $_POST['order_note'] ?? '';
        $payment_method = $_POST['payment_method'] ?? null;
        $payment_trans = $_POST['transaction_id'] ?? null;
        $order_status = $_POST['order_status'] ?? null;

        // Basic Input Validation
        if (!$res_id || !$amount || !$payment_method || !$order_status) {
            throw new Exception("Required fields missing.");
        }

        //Generate secret code
        function generateSecretCode($length = 14) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $random = '';
            for ($i = 0; $i < $length; $i++) {
                $random .= $characters[random_int(0, strlen($characters) - 1)];
            }
            return 'G3' . $random;
        }
        $secret_code = generateSecretCode();

        //  Handle Screenshot Upload
        $screenshot = $_FILES['payment_proof'] ?? null;
        if (!$screenshot || $screenshot['error'] !== 0) {
            throw new Exception("File upload failed.");
        }

        //Validate Screenshot
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif', 'image/bmp', 'image/svg+xml', 'image/tiff', 'image/x-icon'];
        $file_type = mime_content_type($screenshot['tmp_name']);
        $file_size = $screenshot['size'];

        if (!in_array($file_type, $allowed_types)) {
            throw new Exception("Invalid image type.");
        }

        if ($file_size > 5 * 1024 * 1024) {
            throw new Exception("Image too large. Max 5MB allowed.");
        }

        // Move to Upload Directory
        $upload_dir = '../uploads/payments/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $filename = "G3_" . uniqid() . "." . pathinfo($screenshot['name'], PATHINFO_EXTENSION);
        $screenshot_path = $upload_dir . $filename;
        if (!move_uploaded_file($screenshot['tmp_name'], $screenshot_path)) {
            throw new Exception("Failed to save uploaded image.");
        }

        // 💼 Controller Class
        class PlaceOrderController {
            private $conn;
            public function __construct($conn) {
                $this->conn = $conn;
            }

            public function placeOrder($customer_id, $res_id, $order_note, $order_status, $secret_code, $filename, $payment_method, $payment_trans, $amount) {
                $cart = new Cart($this->conn);
                $order = new Place_customer_order_model($this->conn);
                $payment = new Place_customer_order_model($this->conn);
                $clearCart = new Place_customer_order_model($this->conn);

                $user_cart = $cart->getCart($customer_id);
                if ($user_cart->num_rows == 0) {
                    throw new Exception("Your cart is empty.");
                }

                // Begin transaction
                $this->conn->begin_transaction();
                try {
                    // 🔹 1. Create order
                    $order_id = $order->createOrder($customer_id, $res_id, $order_status, $order_note, $secret_code);
                    // 🔹 2. Add order items
                    while ($item = $user_cart->fetch_assoc() ) {
                        $order->addOrderItem($order_id, $item['menu_id'], $item['quantity']);
                    }
                    // 🔹 3. Add payment
                    $payment->savePayment($order_id, $amount, $payment_method, $filename, $payment_trans);
                    // 🔹 4. Clear cart
                    $clearCart->clearCart($customer_id);

                    // ✅ Commit all changes
                    $this->conn->commit();
                    //at the end redirect success responce for frontend AJAX
                    return ['success' => true, 'message' => 'Order placed successfully.'];
                }
                catch (Exception $e) {
                    // Rollback if any step fails
                    $this->conn->rollback();
                    throw $e;
                }
            }//end of function
        }//end of class

        //call function
        $controller = new PlaceOrderController($conn);//create object by constractor
        $result = $controller->placeOrder($customer_id, $res_id, $order_note, $order_status, $secret_code, $filename, $payment_method, $payment_trans, $amount);

        // when order cmpleted send email notification for customers
        if ($result) {
            $stmt = $conn->prepare("
                SELECT cda.email, cda.name 
                FROM orders o
                JOIN users u ON o.customer_id = u.user_id
                JOIN customer_delivery_address cda ON u.user_id = cda.user_id
                WHERE o.order_id = ?
            ");            
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $email = $result['email'];
            $name = $result['name'];
            sendOrderCompleteEmail($email, $order_id, $name, $amount);


            echo json_encode($result);

        }

    } catch (Exception $e) {
        // Handle all errors
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>
