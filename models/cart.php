<?php
    require_once __DIR__ . "/../config/database.php";
    

    //creating class for cart management
    class Cart {
        private $conn;
        public function __construct($conn){
            $this-> conn = $conn;
        }
        //fetch all cart items based on users id...
        public function getCart($user_id){
            $sql = "SELECT c.*, quantity * m.price AS sub_total, SUM(c.quantity * m.price) OVER () AS total, m.name AS menu_item, m.image AS menu_image, m.content AS content, m.price AS menu_price, r.restaurant_id AS res_id, r.name AS restaurant_name, r.location AS restaurant_address
                    FROM cart c
                    JOIN menu m ON c.menu_id = m.menu_id
                    JOIN restaurants r ON m.restaurant_id = r.restaurant_id
                    WHERE c.user_id = ?";
            $stmt = $this->conn->prepare($sql);
            // Check if `prepare()` failed
            if (!$stmt) {
                die("SQL Prepare Error: " . $this->conn->error);
            }
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            //get the result set
            $result = $stmt->get_result();
            if (!$result) {
                die("Error executing query: " . $stmt->error);
            }

            $stmt->close();
            
            return $result;
        }
    }

?>