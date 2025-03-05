    <?php
    session_start();
    require_once __DIR__ . '/../../config/database.php';

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

    $sql = "SELECT * FROM restaurants WHERE name LIKE ? OR location LIKE ? OR rating LIKE ? ORDER BY $sort $sort_order";
    $stmt = $conn->prepare($sql);
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
                        <div class="restaurant-rating">
                            <h3>Rating</h3>
                            <div class="star-rating">
                                <i class="fa-solid fa-star" data-value="1"></i>
                                <i class="fa-solid fa-star" data-value="2"></i>
                                <i class="fa-solid fa-star" data-value="3"></i>
                                <i class="fa-solid fa-star" data-value="4"></i>
                                <i class="fa-solid fa-star" data-value="5"></i>
                                <div class="rating-value"><?php echo $restaurant['rating']; ?>/5</div>
                            </div>
                        </div>
                        
                        <div class="redirect">
                            <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn">View Menu</a> 
                            <a href="restaurant_details.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn">Read Details</a>
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
        <h2>Top Restaurants Near You</h2>
        <div id="restaurantResults" class="restaurant-grid">
            <?php if ($restaurants): ?>
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="restaurant-card">
                        <div class="restaurant-image">
                            <img src="../restaurant/restaurantAsset/<?php echo $restaurant['image']; ?>" alt="<?php echo $restaurant['name']; ?>">
                            <h3 class="res-name"><?php echo htmlspecialchars($restaurant['name']); ?> hotel:</h3>
                        </div>
                        <div class="restaurant-details">
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['location']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($restaurant['phone']); ?></p>
                        <!--add rating-->
                            <div class="restaurant-rating">
                                <h3>Rating</h3>
                                <div class="star-rating">
                                    <i class="fa-solid fa-star" data-value="1"></i>
                                    <i class="fa-solid fa-star" data-value="2"></i>
                                    <i class="fa-solid fa-star" data-value="3"></i>
                                    <i class="fa-solid fa-star" data-value="4"></i>
                                    <i class="fa-solid fa-star" data-value="5"></i>
                                    <div class="rating-value"><?php echo $restaurant['rating']; ?>/5</div>
                                </div>
                            </div>

                            <div class="redirect">
                                <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn">View Menu</a> 
                                <a href="restaurant_details.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn">Read Details</a>
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

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> FoodieExpress. All rights reserved.</p>
    </footer>
    </body>
            <!--external javascripts -->
        <script src="javaScript/rating.js" type="text/javascript" defer crossorigin="anonymous"></script>
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
