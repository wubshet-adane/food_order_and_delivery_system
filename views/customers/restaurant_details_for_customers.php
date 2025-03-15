<?php
require_once '../../models/restaurant_details_for_customers.php';
require_once '../../config/database.php';

session_start(); // Ensure the session is started

// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "customer" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    header("Location: ../auth/customer_login.php?message=Please enter correct credentials!");
    exit; // Stop execution after redirection
}

$resId = $_GET['restaurant_id'];

$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getOneRestaurant($resId);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants</title>
    <link rel="stylesheet" href="css/restaurant_details_for_customers.css">
</head>

<body>
    <section class="restaurant-management">

        <div class="restaurant-list">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="header" style="background: linear-gradient(rgba(142,13,332,0.1), rgba(12,1,12,0.1)), url('../restaurant/restaurantAsset/<?php echo $restaurant['banner']?>');
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;">

                    <div>

                    </div>
                    <div>
                        <img src="../restaurant/restaurantAsset/<?= $restaurant['image'] ?>"
                            alt="<?= htmlspecialchars($restaurant['name']) ?>" class="resimage">
                    </div>
                </div>
                <div class="restaurant-card">

                    <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                    <p><strong>📍 Location:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                    <p><strong>📝 Description:</strong> <?= htmlspecialchars($restaurant['description']) ?></p>
                    <p><strong>⏰ Working Hours:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?>
                    </p>

                    <div class="map-container">
                        <iframe width="100%" height="250" style="border:0;" title="Restaurant Location" loading="lazy"
                            allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBjlgNQQFbENtCwtN_livp3RAhzHq4pTuE<?= urlencode($restaurant['MAP_location']) ?>">
                        </iframe>
                    </div>

                    <p><strong>🔗 Socials:</strong>
                        <a href="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>" target="_blank">TikTok</a> |
                        <a href="<?= htmlspecialchars($restaurant['telegramAccount']) ?>" target="_blank">Telegram</a> |
                        <a href="<?= htmlspecialchars($restaurant['instagramAaccount']) ?>" target="_blank">Instagram</a>
                    </p>

                    <p>
                        <strong>Status:</strong> <span
                            class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></span>
                    </p>

                </div>
            <?php endforeach; ?>
        </div>
    </section>

</body>

</html>