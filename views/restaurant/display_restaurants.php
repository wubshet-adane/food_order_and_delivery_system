<?php
session_start();
/*
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}
*/
require_once '../../models/manage_restaurant.php';
require_once '../../config/database.php';

$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getAllRestaurants();
?>

<section class="restaurant-container">
    <h2>📍 My Restaurant Locations</h2>

    <div class="restaurant-grid">
        <?php if (count($restaurants) > 0): ?>
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="restaurant-card">
                    <div class="restaurant-info">
                        <div class="logo">
                            <img src="restaurantAsset/<?=$restaurant['image']?>" alt="logo">
                        </div>
                        <p>Name:<?= htmlspecialchars($restaurant['name']) ?></p>
                        <!--<p>📍Google map location link:  <?= htmlspecialchars($restaurant['MAP_address']) ?></p>-->
                        <p>🏠 <?= htmlspecialchars($restaurant['location']) ?></p>
                    </div>
                    <a href="restaurant_details.php?id=<?= $restaurant['restaurant_id'] ?>" class="view-details-btn">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-restaurant">No restaurants found.</p>
        <?php endif; ?>
    </div>
</section>
