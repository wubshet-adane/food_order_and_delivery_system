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
    <title>Restaurant detail</title>
    <link rel="stylesheet" href="css/manage_restaurant.css">
    <link rel="icon" href="../../public/images/logo.jpg'">
     <!--font ausome for star rating-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="../customers/javaScript/map.js"></script>

    <style>

    </style>
    </head>


<body>
    <section class="restaurant-management">

        <div class="restaurant-list">
            <div class="searchingAndSortingFunctionality">
                <div>
                    <button class="boBack" onclick="location.href='restaurant_list.php'"> Back </button>
                </div>
                <div class="addMenu">
                    <button class="boAdd" onclick="location.href='add menu.php?id=<?php echo $resId ?>'">Add menu</button>
                </div>
            </div>
            <div>
                <h2>Restaurant Detail</h2>
            </div>
            <?php foreach ($restaurants as $restaurant): ?>
                <div>
                    
                    <div class="res_name">
                        <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
                    </div>

                    <div class="image">
                        <strong> restaurant Card Image:</strong> 
                        <img src = "restaurantAsset/<?= htmlspecialchars($restaurant['image']);?>" alt="imge">
                    </div>

                    <div class="banner">
                        <strong> restaurant Banner Image:</strong>
                        <img src = "restaurantAsset/<?= htmlspecialchars($restaurant['banner']);?>" alt="banner">
                    </div>

                    <div class="license">
                        <strong> restaurant legal license Image:</strong>
                        <img src = "restaurantAsset/<?= htmlspecialchars($restaurant['license']);?>" alt="license">
                    </div>

                    <div class="latitude">
                        <strong>Latitude</strong>
                        <input type="text" id="latitude" value="<?= $restaurant['latitude']?>">
                    </div>

                    <div class="longitude">
                        <strong>Longitude</strong>
                        <input type="text" id="longtude" value="<?= $restaurant['longitude']?>">
                    </div>

                    <div class="map-container">
                        <div>
                            <strong> Location:</strong> 
                            <i class="fa fa-map-marker"></i> <?= htmlspecialchars($restaurant['location'])?>
                            <div id="map"></div>
                        </div>
                    </div>
                   
                    <div class="description">
                        <strong> Detail Description:</strong> <span class="description"><?= htmlspecialchars($restaurant['description']) ?></span>
                    </div>

                    <div class="time">
                        <strong> Working time:</strong> <?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?>
                    </div>
                
                    <div class="tiktokAccount">
                        <strong> Tiktok link:</strong>
                        <input type="text" value="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>">
                    </div>

                    <div class="website">
                        <strong> Website link:</strong>
                        <input type="text" value="<?= htmlspecialchars($restaurant['website']) ?>"> 
                    </div>

                    <div class="telegramAccount">
                        <strong> Telegram link:</strong>
                        <input type="text" value="<?= htmlspecialchars($restaurant['telegramAccount']) ?>"> 
                    </div>

                    <div class="instagramAccount">
                        <strong> Instagram link:</strong>
                        <input type="text" value="<?= htmlspecialchars($restaurant['instagramAccount']) ?>">
                    </div>

                    <div class="facebook">
                        <strong> facebook account:</strong>
                        <input type="text" value="<?= htmlspecialchars($restaurant['facebook']) ?>">
                    </div>

                    <div class="phone">
                        <strong> phone number:</strong>
                        <input type="text" value="<?= htmlspecialchars($restaurant['phone']) ?>">
                    </div>
                    <div class="status">
                        <strong>Status:</strong>
                        <p class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></p>
                    </div>

                    <div class="rating">
                        <strong>Rating:</strong>
                        <p><?= htmlspecialchars($restaurant['rating']) ?></p>
                    </div>

                    <div class="created_at">
                        <strong>registered time:</strong>
                        <p ><?= htmlspecialchars($restaurant['created_at']) ?></p>
                    </div>

                    <div class="updated_at">
                        <strong>latest update time:</strong>
                        <p ><?= htmlspecialchars($restaurant['updated_at']) ?></p>
                    </div>
                    
                </div>

                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!--map script-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap&libraries=places&v=weekly" defer>
    </script>
</body>

</html>