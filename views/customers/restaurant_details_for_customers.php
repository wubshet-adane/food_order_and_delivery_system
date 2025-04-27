<?php
require_once '../../models/restaurant_details_for_customers.php';
require_once '../../models/restaurant_review.php';
require_once '../../config/database.php';

session_start(); // Ensure the session is started

// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== "customer" || !isset($_SESSION['loggedIn']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password'])) {
    header("Location: ../auth/customer_login.php?message=Please enter correct credentials!");
    exit; // Stop execution after redirection
}

$resId = $_GET['restaurant_id'];

$restaurantModel = new Restaurant($conn);
$reviewModel = new Review($conn);
$restaurants = $restaurantModel->getOneRestaurant($resId);
$restaurantReviews = $reviewModel->getRestaurantReviews($resId);

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
    <meta name="description" content="Restaurant details page for customers. View restaurant information, reviews, and more.">
    <meta name="keywords" content="restaurant, details, reviews, food, order, online, system">
    <meta name="author" content="G3 Online Food Ordering System">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-title" content="G3 FoodOrder">
    <meta name="application-name" content="G3 FoodOrder">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../../public/images/logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Restaurant detail</title>
    <link rel="stylesheet" href="css/restaurant_details_for_customers.css">
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/footer.css">
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
                            <a class="back" href="javascript:history.back()"><i class=" fa fa-solid fa-arrow-left"></i></a>
                            <a href="about.php">About</a>
                            <a href="contact.php">Contact</a>
                            <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>">Menu</a>
                        </div>

                        <!-- Authentication Links -->
                        <div class="auth-links">
                            <?php if(!isset($_SESSION['loggedIn'])){
                            ?>
                                <a href="../auth/customer_login.php">Login</a>
                                <a href="../auth/restaurant_owner_registration.php">Sign Up</a>
                            <?php }else{?>
                                <div>
                                    <a href="cart.php"><i class="fa-solid fa-cart-plus" style="position: relative">
                                        <sup style="position: absolute; top: -12px; left: 12px; background: #0f1; color: #111; padding: 3px 2px; font-size: 10px; border-radius: 50%;">
                                            <?php
                                            if(isset($_SESSION['qty'])){
                                                echo $_SESSION['qty'];
                                            }else{
                                                echo 0;
                                            }?>
                                            </sup>
                                        </i>
                                    </a>
                                </div>
                                <div class="profile-dropdown">
                                    <a href="javascript:void(0)" class="profile-dropbtn"><img src="../../public/images/<?php echo $_SESSION['profile_image']?>" alt="profile"></a>
                                    <div class="profile-dropdown-content">
                                        <ul>
                                            <li><a href="profile.php"><i class="fa-solid fa-user"></i>&nbsp;&nbsp; Profile</a></li>
                                            <li><a href="cart.php"><i class="fa-solid fa-cart-plus"></i>&nbsp;&nbsp; Cart</a></li>
                                            <li><a href="order_history.php"><i class="fa-solid fa-bars"></i>&nbsp;&nbsp; Order History</a></li>
                                            <li><a href="restaurant_list.php"><i class="fa-solid fa-key"></i>&nbsp;&nbsp; Change password</a></li>
                                            <li><a href="restaurant_details_for_customers.php"><i class="fa-solid fa-gear"></i>&nbsp;&nbsp; Account settings</a></li>
                                            <li><a href="logout.html"><i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp; Logout</a></li>
                                        </ul>
                                    </div>
                                </div>
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

                    <p class="box">
                        <strong class="res_info"> Detail Description about <?= htmlspecialchars($restaurant['name']) ?>:</strong> 
                        <span class="description"><?= htmlspecialchars($restaurant['description']) ?></span>
                    </p>
                                       
                    <div class="map-container box">
                        <strong class="res_info"> Physical Location City or WellKnown Place </strong>
                        <i class="fa fa-map-marker"></i> <?= htmlspecialchars($restaurant['location'])?>
                        <div id="map"></div>
                    </div>

                    <div class="restaurant_review_section">
                        <strong class="res_info">top 10 reviews from our customers</strong>
                        <div class="review_box">
                            <?php foreach ($restaurantReviews as $review) :?>
                                <div class="review_card">
                                    <ul class="reviewer_info">
                                        <li><img src="../../public/images/profile icon.jpg" alt="user img"> <br> <?= htmlspecialchars($review['user_name']) ?></li>                                    
                                        <li class="review_text">&quot;<?= htmlspecialchars($review['review_text']) ?>&quot;</li>
                                        <?php
                                            $day =  date("l, F j, Y \a\\t g:i A", strtotime($review['created_at']));
                                        ?>
                                        <li class="rating"><?= htmlspecialchars($review['rating']) ?> <i class="fa-solid fa-star"></i></li>
                                        <li><span><?= $day ?></span></li>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <p class="box"><strong class="res_info"> Working Hours:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?>
                    </p>

                    <div class="box social_media"><strong class="res_info"> Get in touch with the following:</strong>
                        <div>
                            <div><a href="<?= htmlspecialchars($restaurant['website']) ?>" target="_blank"><span class="material-symbols-outlined">language </span> <span class="link"><?= htmlspecialchars($restaurant['website']) ?> </a></span> </div>
                            <div><a href="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>" target="_blank"><i class="fa-brands fa-tiktok"></i> <span class="link"><?= htmlspecialchars($restaurant['tiktokAccount']) ?></span></a> </div>
                            <div><a href="<?= htmlspecialchars($restaurant['telegramAccount']) ?>" target="_blank"><i class="fa-brands fa-telegram"></i> <span class="link"><?= htmlspecialchars($restaurant['telegramAccount']) ?></span></a> </div>
                            <div><a href="<?= htmlspecialchars($restaurant['instagramAccount']) ?>" target="_blank"><i class="fa-brands fa-instagram"></i> <span class="link"><?= htmlspecialchars($restaurant['instagramAccount']) ?></span></a></div>
                            <div><a href="tel:<?= htmlspecialchars($restaurant['phone']) ?>"><i class="fa-solid fa-phone"></i> <span class="link"><?= htmlspecialchars($restaurant['phone'])?></span></a></div>
                        </div>
                    </div>
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
                if (img.style.width !== '80%') {
                    img.style.width = '80%';
                } else {
                    img.style.width = '25%';
                }
            });
        });
    </script>
    <?php include "footer.php";?>
    <script src="javaScript/scroll_up.js"></script>
</body>
</html>