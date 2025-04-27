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
    public function getRestaurantReviews($resId){
        $sql = "SELECT
                    rv.id AS review_id,
                    rv.rating,
                    rv.review_text,
                    rv.created_at,
                    users.user_id,
                    res.name AS restaurant_name,
                    res.image AS restaurant_logo,
                    users.name AS user_name,
                    users.image AS user_image,
                    users.email AS user_email
                FROM review rv
                JOIN users ON rv.user_id = users.user_id
                JOIN restaurants res ON rv.restaurant_id = res.restaurant_id
                WHERE res.restaurant_id = ?
                ORDER BY rv.rating DESC";

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