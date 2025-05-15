<?php 
    require_once __DIR__ . "/../config/database.php";

    class Recomendation{
        public static function getRecomendation(){
            global $conn;
            $sql = "SELECT r.restaurant_id, r.name, r.location, r.image,
                COALESCE(AVG(rv.rating), 0) AS avg_rating,
                COUNT(DISTINCT rv.user_id) AS no_of_reviewers
                FROM restaurants r
                JOIN review rv ON r.restaurant_id = rv.restaurant_id
                where r.confirmation_status = 'approved'
                GROUP BY r.restaurant_id , r.name, r.location, r.image
                ORDER BY avg_rating DESC LIMIT 6;";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $recomond = $stmt->get_result();
            return $recomond->fetch_all(MYSQLI_ASSOC);
        }
    }
?>