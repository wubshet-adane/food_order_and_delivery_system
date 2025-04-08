    <?php
    session_start();
    require_once __DIR__ . '/../../config/database.php';
    include "../../models/recomendation.php";

    //get recomendation restaurants
    $recomendation = Recomendation::getRecomendation();

    //default value for lat andlng
    
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['latitude']) && isset($_GET['longitude'])) {
        $latitude = floatval($_GET['latitude']);
        $longitude = floatval($_GET['longitude']);
    }  
    else {
        $latitude = 0.0; // Default latitude
        $longitude = 0.0; // Default longitude
    }
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'distance_km ASC';
    

    $sql = "SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating,
    COALESCE(COUNT(DISTINCT rv.user_id), 0) AS no_of_reviewers,
    (6371 * ACOS(
        COS(RADIANS(?)) * COS(RADIANS(r.latitude)) * COS(RADIANS(r.longitude) - RADIANS(?)) +
        SIN(RADIANS(?)) * SIN(RADIANS(r.latitude))
    )) AS distance_km
    FROM restaurants r LEFT JOIN review rv ON r.restaurant_id = rv.restaurant_id
    WHERE r.name LIKE ? OR r.description LIKE ? OR r.location LIKE ? OR r.phone LIKE ?
    GROUP BY r.restaurant_id, r.name, r.location, r.image, r.phone, r.status, r.latitude, r.longitude
    ORDER BY $sort";  

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $searchQuery = "%" . $search . "%";
    $stmt->bind_param("dddssss", $latitude, $longitude, $latitude, $searchQuery, $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurants = $result->fetch_all(MYSQLI_ASSOC);

    //echo json_encode($latitude, $longitude);
    // Handle AJAX request
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        if ($restaurants) {
            foreach ($restaurants as $restaurant) {
                ?>
                <div class="restaurant-card">
                    <div class="restaurant-image">
                        <img src="../restaurant/restaurantAsset/<?php echo $restaurant['image']; ?>" alt="<?php echo $restaurant['name']; ?>">
                        <h3 class="res-name"><?php echo htmlspecialchars($restaurant['name']); ?> hotel:</h3>
                    </div>
                    <div class="restaurant-details">
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['location']); ?></p>
                        <p><strong>distance from your location:</strong> <?php echo round(htmlspecialchars($restaurant['distance_km']), 2); ?> KM</p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($restaurant['phone']); ?></p>
                        <!--add rating-->
                        <p>Review: 
                            <?php 
                                $rating = round($restaurant['avg_rating'], 1); // Round to 1 decimal place
                            
                                // Display stars based on the average rating
                                $fullStars = floor($rating); // Count full stars
                                $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0; // Check for a half star
                                $emptyStars = 5 - ($fullStars + $halfStar); // Remaining empty stars
                        
                                // Display full stars
                                for ($i = 0; $i < $fullStars; $i++) {
                                    echo '<i class="fa-solid fa-star"></i>'; // Full Star
                                }
                        
                                // Display half star if needed
                                if ($halfStar) {
                                    echo '<i class="fa-solid fa-star-half-stroke"></i>'; // Half Star
                                }
                        
                                // Display empty stars
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    echo '<i class="fa-regular fa-star"></i>'; // Empty Star
                                }
                            ?>
                            <strong> <?=$rating?>/5</strong>
                            <span class="reviewer"><?=$restaurant['no_of_reviewers']?> <i class="fa-solid fa-person"></i></span>
                        </p>
                        
                        <div class="redirect">
                            <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn" title="Display menu from this restaurant">View Menu</a> 
                            <a href="restaurant_details_for_customers.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn" title="Details about restaurant"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <?php
                    //check if restaurant is open or closed:
                        if ($restaurant){
                            echo "<div class='restaurant-status'>";
                            if ($restaurant['status'] == 'open'){
                    ?>
                            <div class="restaurant-open" id="restaurant-open">
                                <p>open</p>
                            </div>
                    <?php
                            }
                            else{
                                echo "<div class='restaurant-closed' id='restaurant-closed'> <p>closed</p> </div>";
                            }
                            echo "</div>";
                        }
                    ?>
                </div>
            <?php
            }
        } else {
            echo "<div class='no_restaurants'>
                    <img src='../../public/images/no restaurants.jpg' alt='not found' width='400px' height='350px' style='margin: 0 auto; border-radius:10px; display: block;'>
                    <h2 style='text-align: center;'>No restaurants found.</h2>
                </div>";
        }
        exit();
    }
    ?>

    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="G3 Food Ordering System - Find the best restaurants near you. Order food online with ease.">
        <meta name="keywords" content="food, ordering, online, restaurants, delivery, G3 Food Ordering System">
        <meta name="author" content="G3 Team">
        <meta name="copyright" content="G3 Food Ordering System">
        <meta name="language" content="English">
        <meta name="revisit-after" content="1 days">
        <meta name="rating" content="General">
        <meta name="distribution" content="Global">
        <meta name="googlebot" content="index, follow">
        <meta name="google" content="notranslate">
        <meta name="msapplication-TileColor" content="#ff9900">
        <meta name="msapplication-TileImage" content="../../public/images/logo-icon.png">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#ff9900">
        <title>Find Restaurants - G3 Food Ordering System</title>
        <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/topbar.css">
        <link rel="stylesheet" href="../footer.css">
        <link rel="stylesheet" href="css/recommendation.css">
        <!--font ausome for star rating-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- SweetAlert CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.css">
        <!-- SweetAlert JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.js"></script>

        <style>
            .phone{
                text-decoration:none;
                text-align: center;
                padding: 3px auto;
            }
            .phone:hover{
                color:blue;
                text-decoration:underline;
                background: linear-gradient(to right, #00000011, #ff9900, #00000011);
            }
        </style>
    </head>
    <body>

    <?php
    include "topbar.php";
    include "recommendation.php";
    ?>
    <!--search and sort section-->
    <section class="search-sort container">
        <form id="searchForm" method="GET" class="search-form">
            <label for="search" class="label">Search Restaurants:</label>
            <input type="text" id="searchInput" name="search" placeholder="Search restaurants..." value="<?php echo htmlspecialchars($search); ?>">
            
            <select id="sortSelect" name="sort">
                <option value="name ASC" <?php if ($sort === 'name ASC') echo 'selected'; ?>>Restaurant name ascending</option>
                <option value="name DESC" <?php if ($sort === 'name DESC') echo 'selected'; ?>>Restaurant name dscending</option>
                <option value="distance_km ASC" <?php if ($sort === 'distance_km') echo 'selected'; ?>>Nearest Restaurant first</option>
                <option value="avg_rating DESC" <?php if ($sort === 'avg_rating DESC') echo 'selected'; ?>>Top rated first</option>
                <option value="avg_rating ASC" <?php if ($sort === 'avg_rating ASC') echo 'selected'; ?>>Least rated first</option>
            </select>

            <button type="submit">Find</button>
        </form>
    </section>

    <section class="restaurants container">
        <div class="restaurants_quote">
            <h1>Top Restaurants Near You</h1>  
            <p>the easiest way to found everything <br> what you want to eat quickly!</p> 
        </div>

        <div id="restaurantResults" class="restaurant-grid">
            <?php if ($restaurants): ?>
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="restaurant-card">
                        <div class="restaurant-image">
                            <img src="../restaurant/restaurantAsset/<?php echo $restaurant['image']; ?>" alt="<?php echo $restaurant['name'];?>">
                            <h3 class="res-name"><?php echo htmlspecialchars($restaurant['name']); ?>:</h3>
                        </div>
                        <div class="restaurant-details">
                            <p><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($restaurant['location']); ?></p>
                            <p><a class="phone" href="tel:<?=  htmlspecialchars($restaurant['phone']); ?>"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($restaurant['phone']); ?></a></p>

                            <div class="res_card_bottom">
                                <!--add rating-->
                                <p>Review:
                                    <?php 
                                        $rating = round($restaurant['avg_rating'], 1); // Round to 1 decimal place
                                    
                                        // Display stars based on the average rating
                                        $fullStars = floor($rating); // Count full stars
                                        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0; // Check for a half star
                                        $emptyStars = 5 - ($fullStars + $halfStar); // Remaining empty stars
                                
                                        // Display full stars
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            echo '<i class="fa-solid fa-star"></i>'; // Full Star
                                        }
                                
                                        // Display half star if needed
                                        if ($halfStar) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>'; // Half Star
                                        }
                                
                                        // Display empty stars
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            echo '<i class="fa-regular fa-star"></i>'; // Empty Star
                                        }
                                    ?>
                                    <strong> <?=$rating?>/5</strong>
                                    <span class="reviewer"><?=$restaurant['no_of_reviewers']?> <i class="fa-solid fa-person"></i></span>
                                </p>
                                <br>
                                <div class="redirect">
                                    <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn" title="Display menu from this restaurant">View Menu</a> 
                                    <a href="restaurant_details_for_customers.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn" title="Details about restaurant"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php
                        //check if restaurant is open or closed:
                            if ($restaurant){
                                echo "<div class='restaurant-status'>";
                                if ($restaurant['status'] == 'open'){
                        ?>
                                <div class="restaurant-open" id="restaurant-open">
                                    <p>open</p>
                                </div>
                        <?php
                                }
                                else{
                                    echo "<div class='restaurant-closed' id='restaurant-closed'> <p>closed</p> </div>";
                                }
                                echo "</div>";
                            }
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no_restaurants">
                    <img src="../../public/images/no restaurants.jpg" alt="not found" width="200px" height="200px"style='margin: 0 auto; border-radius:10px; display: block;'>
                    <h2 style='text-align: center;'>No restaurants found.</h2>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="">
        <div>
        </div>
    </div>

    <!--become a member-->
    <div class="restaurants_quote">
        <h1>Are you ready to enjoy our service?</h1>  
        <p>The easiest way to find everything <br> you crave in just a few clicks!</p> 
    </div>

    <div class="become_partner">
        <div class="container">
            <div class="box customer">
                <h2>Become a <span>Customer</span></h2>
                <p>Order your favorite meals with ease and convenience.</p>
                <div class="partner_redirect_link_box">
                    <a href="../auth/customer_login.php"> Customer</a>
                </div>
            </div>
            <div class="box restaurant">
                <h2>Become a <span>Merchant</span></h2>
                <p>Grow your business by reaching more customers.</p>
                <div class="partner_redirect_link_box">
                    <a href="../restaurant/dashboard.php">Merchant</a>
                </div>
            </div>
            <div class="box delivery">
                <h2>Become a <span>Delivery Person</span></h2>
                <p>Earn money by delivering food to hungry customers.</p>
                <div class="partner_redirect_link_box">
                    <a href="../auth/delivery_login.php">Delivery</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
    </body>

    <!--search and sort AJAX functionality-->
    <script>
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const restaurantResults = document.getElementById('restaurantResults');

    function fetchRestaurants() {
        if (!navigator.geolocation) {
            Swal.fire({
                title: 'Error!',
                text: 'Geolocation is not supported by your browser.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const search = searchInput.value;
                const sort = sortSelect.value;

                const xhr = new XMLHttpRequest();
                xhr.open(
                    'GET',
                    `<?php echo basename($_SERVER['PHP_SELF']); ?>?ajax=1&search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}&latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}`,
                    true
                );
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        restaurantResults.innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            },
            function (error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Error getting location: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    searchInput.addEventListener('input', fetchRestaurants);
    sortSelect.addEventListener('change', fetchRestaurants);
</script>
</html>
