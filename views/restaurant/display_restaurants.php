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

// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "restaurant" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    
    header("Location: ../auth/restaurant_login.php?message=Please enter correct credentials!");
    exit; // Stop execution after redirection
}

$ownerId = $_SESSION['user_id'];

$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getAllRestaurants($ownerId);
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
                        <p><strong>Name:</strong> <?= htmlspecialchars($restaurant['name']) ?></p>
                        <p><strong>Adress:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                    </div>
                    <a href="manage_restaurants.php?id=<?= $restaurant['restaurant_id'] ?>" class="view-details-btn">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-restaurant">No restaurants found.</p>
        <?php endif; ?>
    </div>
</section>
