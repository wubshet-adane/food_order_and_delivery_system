<?php
require_once '../../models/manage_restaurant.php';
require_once '../../config/database.php';

session_start(); // Ensure the session is started

// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "restaurant" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    header("Location: ../auth/restaurant_login.php?message=Please enter correct credentials!");
    exit; // Stop execution after redirection
}

$ownerId = $_SESSION['user_id'];
$resId = $_GET['id'];

$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getOneRestaurant($ownerId, $resId);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants</title>
    <link rel="stylesheet" href="css/manage_restaurant.css">
</head>
<body>
    <section class="restaurant-management">
        <h2>🏢 Manage Your Restaurants</h2>
        <button id="addRestaurantBtn">➕ Add New Restaurant</button>
        
        <div class="restaurant-list">
            <?php foreach ($restaurants as $restaurant) : ?>
                <div class="restaurant-card">
                    <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                    <p><strong>📍 Location:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                    <p><strong>📝 Description:</strong> <?= htmlspecialchars($restaurant['description']) ?></p>
                    <p><strong>⏰ Working Hours:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?></p>
                    
                    <div class="map-container">
                        <iframe width="100%" height="250" style="border:0;" title="Restaurant Location" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBjlgNQQFbENtCwtN_livp3RAhzHq4pTuE<?= urlencode($restaurant['MAP_location']) ?>">
                        </iframe>
                    </div>
                    
                    <p><strong>🔗 Socials:</strong>
                        <a href="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>" target="_blank">TikTok</a> |
                        <a href="<?= htmlspecialchars($restaurant['telegramAccount']) ?>" target="_blank">Telegram</a> |
                        <a href="<?= htmlspecialchars($restaurant['instagramAaccount']) ?>" target="_blank">Instagram</a>
                    </p>
                    
                    <p>
                        <strong>Status:</strong> <span class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></span>
                    </p>
                    
                    <button class="edit-btn" data-id="<?= $restaurant['restaurant_id'] ?>">✏️ Edit</button>
                    <button class="delete-btn" data-id="<?= $restaurant['restaurant_id'] ?>">🗑️ Delete</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
</body>
</html>