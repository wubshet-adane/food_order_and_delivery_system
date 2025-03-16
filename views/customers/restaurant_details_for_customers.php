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


    $apiKey = "AIzaSyAiwVbMDuB2I6fSDJSNhym8mTmE3kc4VLM"; // Your Google API Key
    $location = isset($restaurant['MAP_location']) ? urlencode($restaurant['MAP_location']) : urlencode('Ethiopia');
    $mapUrl = "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q={$location}";
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant detail</title>
    <link rel="stylesheet" href="css/restaurant_details_for_customers.css">
    <link rel="icon" href="../../public/images/logo.jpg'">
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
                    <div class="res_name">
                        <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
                    </div>
                </div>
                <div class="restaurant-card">

                    <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                    <p><strong class="res_info"> Physical Location (Maybe City or WellKnown Place):</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                    <p><strong class="res_info"> Detail Description about <?= htmlspecialchars($restaurant['name']) ?>:</strong> <span class="description"><?= htmlspecialchars($restaurant['description']) ?></span></p>
                    <p><strong class="res_info"> Working Hours:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?>
                    </p>

                    <div class="map-container">
                        <p><strong class="res_info">reliable location located on google map:</strong></p>
                        <iframe width="100%" height="250" style="border:0;" title="Restaurant Location" loading="lazy"
                            allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="<?= $mapUrl ?>">
                        </iframe>
                    </div>

                    <p><strong class="res_info"> contact us at different platforms like:</strong></p>
                        <a href="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>" target="_blank">TikTok</a> |
                        <a href="<?= htmlspecialchars($restaurant['telegramAccount']) ?>" target="_blank">Telegram</a> |
                        <a href="<?= htmlspecialchars($restaurant['instagramAaccount']) ?>" target="_blank">Instagram</a>

                    <p>
                        <strong class="res_info">Status:</strong> <span
                            class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></span>
                    </p>

                </div>
            <?php endforeach; ?>
        </div>
    </section>

</body>

</html>