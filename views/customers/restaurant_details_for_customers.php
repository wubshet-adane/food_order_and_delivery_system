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

/*
    $apiKey = "AIzaSyAiwVbMDuB2I6fSDJSNhym8mTmE3kc4VLM"; // Your Google API Key
    $location = isset($restaurant['MAP_location']) ? urlencode($restaurant['MAP_location']) : urlencode('Ethiopia');
    $mapUrl = "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q={$location}";
    */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant detail</title>
    <link rel="stylesheet" href="css/restaurant_details_for_customers.css">
    <link rel="icon" href="../../public/images/logo.jpg'">
     <!--font ausome for star rating-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="javaScript/map.js"></script>

    <style>
        #map{
            display: flex;
            height: 400px;
            width: 90%;
            margin: auto;
            border-radius: 5px;
        }
        @media screen and (max-width: 900px) {
            #map {
                height: 300px;
                width: 100%;
            }
        }

    </style>
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
                    
                    <input type="text" id="location" value="<?= $restaurant['location']?>">
                    <input type="text" id="latitude" value="<?= $restaurant['latitude']?>">
                    <input type="text" id="longtude" value="<?= $restaurant['longitude']?>">
                    
                    <div class="map-container box">
                        <div><strong class="res_info"> Physical Location City or WellKnown Place </strong>
                            <i class="fa fa-map-marker"></i> <?= htmlspecialchars($restaurant['location'])?>
                            <div id="map"></div>
                        </div>
                    </div>
                   

                    <p class="box"><strong class="res_info"> Detail Description about <?= htmlspecialchars($restaurant['name']) ?>:</strong> <span class="description"><?= htmlspecialchars($restaurant['description']) ?></span></p>
                    <p class="box"><strong class="res_info"> Working Hours:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?>
                    </p>


                    <p class="box social_media"><strong class="res_info"> Get in touch with the following:</strong>
                        <a href="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>" target="_blank"><i class="fa-brands fa-tiktok"></i> TikTok</a> 
                        <a href="<?= htmlspecialchars($restaurant['telegramAccount']) ?>" target="_blank"><i class="fa-brands fa-telegram"></i> Telegram</a> 
                        <a href="<?= htmlspecialchars($restaurant['instagramAaccount']) ?>" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram</a>
                        <a href="tel:<?= htmlspecialchars($restaurant['phone']) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($restaurant['phone'])?></a>
                    </p>
                    <p class="box">
                        <strong class="res_info">Status:</strong> <span
                            class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></span>
                    </p>

                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!--map script-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap&libraries=places&v=weekly" defer>
    </script>
</body>

</html>