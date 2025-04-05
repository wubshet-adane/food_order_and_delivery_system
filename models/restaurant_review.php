<?php
require_once __DIR__ . '/../config/database.php';
class Review
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    //to get specific restaurant review
    public function getRestaurantReviews($resId)
    {
        $sql = "SELECT
                    rv.id AS review_id,
                    rv.rating,
                    rv.review_text,
                    rv.created_at,
                    
                    users.user_id,
                    users.name AS user_name,
                    users.email AS user_email
                    /*
                    res.restaurant_id,
                    res.name AS restaurant_name,
                    res.location AS restaurant_address
                    */
                FROM review rv
                JOIN users ON rv.user_id = users.user_id
                JOIN restaurants res ON rv.restaurant_id = res.restaurant_id
                WHERE rv.restaurant_id = ?
                ORDER BY rv.rating DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        // Check if `prepare()` failed
        if (!$stmt) {
            die("SQL Prepare Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $resId);
        $stmt->execute();
        // Get the result set
        $queryResult = $stmt->get_result();
        // Fetch data correctly
        $review = [];
        while ($row = $queryResult->fetch_assoc()) {
            $review[] = $row;
        }
        return $review; // Always return an array
    }
}
?>