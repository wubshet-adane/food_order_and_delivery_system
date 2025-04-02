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
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <!--font ausome for star rating-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=language" />
    <script type="module" src="javaScript/map.js"></script>

    <style>
/* Status styles */
.status {
    padding: 5px 5px;
    border-radius: 3px;
    font-weight: bold;
    color: white;
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translate(-50%, -50%); /* Move the status slightly inside */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="40" viewBox="0 0 120 40"%3E%3Crect width="120" height="40" rx="8" ry="8" style="fill:%23ffffff;opacity:0.15;" /%3E%3C/svg%3E');
    background-size: cover;
}

/* Specific status colors */
.status.open {
    background-color: #059326FF; /* Green */
}

.status.closed {
    background-color: #FF0019FF; /* Red */
}
    </style>
    </head>


<body>
    
       
    </section>

        <div class="restaurant-list">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="header" style="background: linear-gradient(rgba(8,13,8,0.9), rgba(12,1,12,0.1)), url('../restaurant/restaurantAsset/<?php echo $restaurant['banner']?>');
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;">
                    
                    <!-- Top Bar -->
                    <div class="top-bar">
                        <!-- Logo -->
                        <div class="logo">
                            <a href="home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 FoodOrder" sizes="" srcset=""></a>
                        </div>

                        <!-- Center Navigation Links -->
                        <div class="nav-links">
                            <a href="home.php">Home</a>
                            <a href="about.php">About</a>
                            <a href="contact.php">Contact</a>
                            <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>">Menu</a>
                        </div>

                        <!-- Authentication Links -->
                        <div class="auth-links">
                            <button id="darkModeToggle">🌙 Dark Mode</button>
                            <?php if(!isset($_SESSION['loggedIn'])){
                            ?>
                                <a href="../auth/customer_login.php">Login</a>
                                <a href="register.php">Sign Up</a>
                            <?php }else{?>
                                <a href="cart.php"><i class="fa-solid fa-cart-plus"></i><sup>12</sup></a>
                                <a href="../auth/logout.php">Logout</a>
                            <?php }?>
                        </div>
                    </div>

                    <div class="res_name">
                        <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
                        <div class="status_div">
                             <span class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="restaurant-card">
                    
                    <input type="hidden" id="location" value="<?= $restaurant['location']?>">
                    <input type="hidden" id="latitude" value="<?= $restaurant['latitude']?>">
                    <input type="hidden" id="longtude" value="<?= $restaurant['longitude']?>">
                    
                    <div class="box">
                        <div class="image">
                            <strong class="res_info"> Renewed legal business license image for our hotel: </strong>
                            <img id="license" onclick="enlarges()" src="../restaurant/restaurantAsset/<?= htmlspecialchars($restaurant['license'])?>" alt="🌄 lisence image">
                        </div>
                    </div>

                    <p class="box"><strong class="res_info"> Detail Description about <?= htmlspecialchars($restaurant['name']) ?>:</strong> <span class="description"><?= htmlspecialchars($restaurant['description']) ?></span></p>
                                       
                    <div class="map-container box">
                        <div><strong class="res_info"> Physical Location City or WellKnown Place </strong>
                            <i class="fa fa-map-marker"></i> <?= htmlspecialchars($restaurant['location'])?>
                            <div id="map"></div>
                        </div>
                    </div>

                    <p class="box social_media"><strong class="res_info"> Get in touch with the following:</strong>
                        <a href="<?= htmlspecialchars($restaurant['website']) ?>" target="_blank"><span class="material-symbols-outlined">language </span> Website </a> 
                        <a href="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>" target="_blank"><i class="fa-brands fa-tiktok"></i> TikTok</a> 
                        <a href="<?= htmlspecialchars($restaurant['telegramAccount']) ?>" target="_blank"><i class="fa-brands fa-telegram"></i> Telegram</a> 
                        <a href="<?= htmlspecialchars($restaurant['instagramAccount']) ?>" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram</a>
                        <a href="tel:<?= htmlspecialchars($restaurant['phone']) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($restaurant['phone'])?></a>
                    </p>

                    <p class="box"><strong class="res_info"> Working Hours:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!--map script-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap&libraries=places&v=weekly" defer></script>
    
    <!--image enlargement-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let img = document.getElementById("license");

            img.addEventListener("click", () => {
                if (img.style.width !== '100%') {
                    img.style.width = '100%';
                } else {
                    img.style.width = '400px';
                }
            });
        });
    </script>

</body>
</html>