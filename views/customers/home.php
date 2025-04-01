    <?php
    session_start();
    require_once __DIR__ . '/../../config/database.php';
    include "../../models/recomendation.php";

    //get recomendation restaurants
    $recomendation = Recomendation::getRecomendation();

    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'name';
    $sort_order = $_GET['sort_order'] ?? 'ASC';

    $allowedSortColumns = ['name', 'location', 'rating'];
    if (!in_array($sort, $allowedSortColumns)) {
        $sort = 'name';
    }
    if ($sort == 'rating'){
        $sort_order = 'DESC';
    }else{
        $sort_order = 'ASC';
    }

    $sql = "SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating,
    COALESCE(COUNT(DISTINCT rv.user_id), 0) AS no_of_reviewers
    FROM restaurants r LEFT JOIN review rv ON r.restaurant_id = rv.restaurant_id
    WHERE r.name LIKE ? OR r.description OR r.location LIKE ? OR r.phone LIKE ?
    GROUP BY r.restaurant_id, r.name, r.location, r.image, r.phone, r.status
    ORDER BY $sort $sort_order";  
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $searchQuery = "%" . $search . "%";
    $stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurants = $result->fetch_all(MYSQLI_ASSOC);

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
            echo "<p>No restaurants found. Please try again.</p>";
        }
        exit();
    }
    ?>

    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Customer Home - Online Food Ordering</title>
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/recommendation.css">

        <!--font ausome for star rating-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>

    <?php
    include "header/topnav.php";
    include "recommendation.php";
    ?>
    <!--search and sort section-->
    <section class="search-sort container">
        <form id="searchForm" method="GET" class="search-form">
            <label for="search" class="label">Search Restaurants:</label>
            <input type="text" id="searchInput" name="search" placeholder="Search restaurants..." value="<?php echo htmlspecialchars($search); ?>">
            
            <select id="sortSelect" name="sort">
                <option value="name" <?php if ($sort === 'name') echo 'selected'; ?>>Sort by Name</option>
                <option value="location" <?php if ($sort === 'location') echo 'selected'; ?>>Sort by Location</option>
                <option value="rating" <?php if ($sort === 'rating') echo 'selected'; ?>>Sort by Rating</option>
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
                            <img src="../restaurant/restaurantAsset/<?php echo $restaurant['image']; ?>" alt="<?php echo $restaurant['name']; ?>">
                            <h3 class="res-name"><?php echo htmlspecialchars($restaurant['name']); ?>:</h3>
                        </div>
                        <div class="restaurant-details">
                            <p><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($restaurant['location']); ?></p>
                            <p><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($restaurant['phone']); ?></p>

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
                <p>No restaurants found. Please try again.</p>
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
                    <a href="../auth/customer_register.php"> Customer</a>
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
                    <a href="../delivery/home.php">Delivery</a>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> FoodieExpress. All rights reserved.</p>
    </footer>
    </body>

        <script>
            const searchInput = document.getElementById('searchInput');
            const sortSelect = document.getElementById('sortSelect');
            const restaurantResults = document.getElementById('restaurantResults');

            function fetchRestaurants() {
                const search = searchInput.value;
                const sort = sortSelect.value;

                const xhr = new XMLHttpRequest();
                xhr.open('GET', `<?php echo basename($_SERVER['PHP_SELF']); ?>?ajax=1&search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}`, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        restaurantResults.innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            }

            searchInput.addEventListener('input', fetchRestaurants);
            sortSelect.addEventListener('change', fetchRestaurants);
        </script>
    </html>
