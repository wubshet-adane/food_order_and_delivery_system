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
$resName = $_GET['name'];

$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getOneRestaurant($ownerId, $resId);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant detail</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!--local style css files-->
    <link rel="stylesheet" href="../../views/restaurant/css/manage_restaurants.css">
    <!--sweet alert external library-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script type="module" src="../customers/javaScript/map.js"></script>

    <style>
       

    </style>
</head>

<body>
    <section class="restaurant-management">
        <form action="../../controllers/restaurant_register_form_controller.php?action=update_restaurant&restaurant_id=<?= $resId ?>" method="POST" autocomplete="on" enctype="multipart/form-data">
            <div class="restaurant-list">
                <div class="top_buttons">
                    <div class="go_back">
                        <a class="boBack" onclick="history.back()" title="go Back">Back </a>
                    </div>
                    <div class="addMenu">
                        <a class="boAdd" onclick="location.href='add menu.php?resId=<?php echo $resId ?>'" title="Add menu to this Restaurant">Add menu</a>
                    </div>
                    <div class="editRestaurant">
                        <a class="boEdit" id="boEdit" name="boEdit" title="Edit Restaurant">Edit</a>
                    </div>
                    <div class="deleteRestaurant">
                        <a class="boDelete" id="boDelete" name="boDelete" href="javascript:void(0);" onclick="confirmDelete('<?= $resId?>', '<?= $resName?>');" title="Delete this Restaurant">Delete</a>
                        <script>
                            function confirmDelete(resId, resName) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    html: `You want to delete <span style="color: red; font-weight: bold; font-family: Arial, sans-serif;">${resName}</span>?`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, delete it!',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = `../../controllers/restaurant_register_form_controller.php?action=delete_restaurant&restaurant_id=${resId}`;
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: `${resName} has been deleted.`,
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Cancelled',
                                            text: 'Your restaurant is safe :)',
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                            }
                            </script>

                    </div>
                </div>

                <div>
                    <h2>Restaurant Detail</h2>
                </div>

                <?php foreach ($restaurants as $restaurant): ?>
                <div>
                    <div class="res_name">
                        <span><h1><?= htmlspecialchars($restaurant['name']) ?></h1></span>
                        <h1><input class="in" type="text" id="name" name="name" value="<?= htmlspecialchars($restaurant['name']) ?>"></h1>
                    </div>

                    <div class="image">
                        <strong> restaurant Card Image:</strong> 
                        <span><img src = "restaurantAsset/<?= htmlspecialchars($restaurant['image']);?>" alt="imge"></span>
                        <input class="in" type="file" id="image" name="image" accept="image/*">
                    </div>

                    <div class="banner">
                        <strong> restaurant Banner Image:</strong>
                        <span><img src = "restaurantAsset/<?= htmlspecialchars($restaurant['banner']);?>" alt="banner"></span>
                        <input class="in" type="file" id="banner" name="banner" accept="image/*">
                    </div>

                    <div class="license">
                        <strong> Renewed legal business license image for this hotel:</strong>
                        <span><img src = "restaurantAsset/<?= htmlspecialchars($restaurant['license']);?>" alt="license"></span>
                        <input class="in" type="file" id="license" name="license" accept="image/*">
                    </div>

                    <div class="latitude">
                        <strong>Latitude</strong>
                        <span><?= $restaurant['latitude']?></span>
                        <input class="in" type="text" id="latitude" name="latitude" value="<?= $restaurant['latitude']?>">
                    </div>

                    <div class="longitude">
                        <strong>Longitude</strong>
                        <span><?= $restaurant['longitude']?></span>
                        <input class="in" type="text" id="longtude" name="longitude" value="<?= $restaurant['longitude']?>">
                    </div>

                    <div class="map-container">
                        <div>
                            <strong> Location:</strong> 
                            <span><i class="fa fa-map-marker"></i> <?= htmlspecialchars($restaurant['location'])?></span>
                            <input class="in" type="text" id="location" name="location" value="<?= htmlspecialchars($restaurant['location'])?>">                            
                            <span><div id="map"></div></span>
                        </div>
                    </div>
                
                    <div class="description">
                        <strong> Detail Description:</strong>
                        <span class="description"><?= htmlspecialchars($restaurant['description']) ?></span>
                        <textarea class="in" id="description" name="description" rows="4" cols="50"><?= htmlspecialchars($restaurant['description']) ?></textarea>
                    </div>

                    <div class="time">
                        <strong> Working time:</strong> 
                        <span><?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?></span>
                        <textarea name="opening_and_closing_hour" id="opening_and_closing_hour" rows="3" cols="50"><?= htmlspecialchars($restaurant['opening_and_closing_hour']) ?></textarea>
                    </div>
                
                    <div class="tiktokAccount">
                        <strong> Tiktok link:</strong>
                        <span><?= htmlspecialchars($restaurant['tiktokAccount']) ?></span>
                        <input class="in" type="text" id="tiktokAccount" name="tiktokAccount" value="<?= htmlspecialchars($restaurant['tiktokAccount']) ?>">
                    </div>

                    <div class="website">
                        <strong> Website link:</strong>
                        <span><?= htmlspecialchars($restaurant['website']) ?></span>
                        <input class="in" type="text" id="website" name="website" value="<?= htmlspecialchars($restaurant['website']) ?>"> 
                    </div>

                    <div class="telegramAccount">
                        <strong> Telegram link:</strong>
                        <span><?= htmlspecialchars($restaurant['telegramAccount']) ?></span>
                        <input class="in" type="text" id="telegramAccount" name="telegramAccount" value="<?= htmlspecialchars($restaurant['telegramAccount']) ?>"> 
                    </div>

                    <div class="instagramAccount">
                        <strong> Instagram link:</strong>
                        <span><?= htmlspecialchars($restaurant['instagramAccount']) ?></span>
                        <input class="in" type="text" id="instagramAccount" name="instagramAccount" value="<?= htmlspecialchars($restaurant['instagramAccount']) ?>">
                    </div>

                    <div class="facebook">
                        <strong> facebook account:</strong>
                        <span><?= htmlspecialchars($restaurant['facebook']) ?></span>
                        <input class="in" type="text" id="facebook" name="facebook" value="<?= htmlspecialchars($restaurant['facebook']) ?>">
                    </div>

                    <div class="phone">
                        <strong> phone number:</strong>
                        <span><?= htmlspecialchars($restaurant['phone']) ?></span>
                        <input class="in" type="text" id="phone" name="phone" value="<?= htmlspecialchars($restaurant['phone']) ?>">
                    </div>

                    <div class="status">
                        <strong>Status:</strong>
                        <span class="status <?= strtolower($restaurant['status']) ?>"><?= htmlspecialchars($restaurant['status']) ?></span>
                        <select class="in" name="status" id="status">
                            <option value="open" <?= htmlspecialchars($restaurant['status']) == 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="closed" <?= htmlspecialchars($restaurant['status']) == 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <div class="rating off">
                        <strong>Rating:</strong>
                        <span><?= htmlspecialchars($restaurant['rating']) ?></span>
                    </div>

                    <div class="created_at off">
                        <strong>registered time:</strong>
                        <span><?= htmlspecialchars($restaurant['created_at']) ?></span>
                    </div>

                    <div class="updated_at off">
                        <strong>latest update time:</strong>
                        <span><?= htmlspecialchars($restaurant['updated_at']) ?></span>
                    </div>
                    <!--update button-->
                    <button class="boUpdate" id="boUpdate" name="boUpdate">Update</button>
                </div>
                <?php endforeach; ?>

            </div>
        </form>
    </section>
    <!--edit restaurant script-->
    <script src="javaScript/edit_restaurant.js" defer loading="async"></script>
    <!--map script-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap&libraries=places&v=weekly" defer loading="async">
    </script>
</body>

</html>