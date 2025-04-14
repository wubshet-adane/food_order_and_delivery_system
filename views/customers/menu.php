    <?php
    session_start();
    require_once __DIR__ . '/../../config/database.php';


    //check if user is logged in or not

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn'])){
        header("Location: ../auth/customer_login.php");
        exit();
    }
    // redirect with restaurant id data from the first page
    $restaurant_id = $_GET['restaurant_id'] ?? null;
    $_SESSION['distance'] = $_GET['distance'] ?? error_log("Distance not set");

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'name ASC';

        $text = "";
        if ($search) {
            $text = "Search Results for :";
        }

        if ($restaurant_id) {
            $sql = "SELECT * FROM menu WHERE restaurant_id = ? AND (name LIKE ? OR description LIKE ? OR catagory LIKE ?) 
            ORDER BY $sort";
            $stmt = $conn->prepare($sql);
            $searchQuery = "%" . $search . "%";
            $stmt->bind_param("isss", $restaurant_id, $searchQuery, $searchQuery, $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $menu_items = $result->fetch_all(MYSQLI_ASSOC);
        }else {
            header("Location: home.php");
            exit();
        }
    }
    $sql = "SELECT * FROM restaurants WHERE restaurant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant = $result->fetch_assoc();

    //strore latitude and longitude value in session
    $_SESSION['restaurant_latitude'] = $restaurant['latitude'];
    $_SESSION['restaurant_longitude'] = $restaurant['longitude'];
    
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="Menu page for <?php echo htmlspecialchars($restaurant['name']); ?>">
            <meta name="keywords" content="menu, <?php echo htmlspecialchars($restaurant['name']); ?>, food, restaurant">
            <meta name="author" content="Wubshet Adane">
            <meta name="theme-color" content="#ff9900">
            <meta name="robots" content="index, follow">
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap">
            <title><?php echo htmlspecialchars($restaurant['name']); ?></title>
            <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
            <!--font ausome for star rating-->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="css/home.css">
            <link rel="stylesheet" href="css/topbar.css">
            <link rel="stylesheet" href="css/footer.css">
            <link rel="stylesheet" href="css/menu.css">
        </head>
    <body>

    <section class="header" style="background: linear-gradient(to bottom, #002636FF, #0099FF00), url('../restaurant/restaurantAsset/<?php echo $restaurant['banner']?>'); background-position: center; background-repeat: no-repeat; background-size: cover;">
    <!-- Top Bar -->
            <?php
                include "topbar.php";
            ?>
            <div class="header_content">
                <h1 style="color:#ff9900;"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
                <h3 style="font-family: monospace;" class="restaurant-moto">&quot; we are here to serve you the best food in town.&quot; </h3>
                <p>Location: <?php echo htmlspecialchars($restaurant['location']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($restaurant['phone']); ?></p>
            </div>
        </section>

        <?php if ($menu_items):?>    
            <!--search and sort section-->
            <section class="search-sort container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="searchForm" method="GET" class="search-form">
                    <label for="search" class="label">Search Menus:</label>
                    <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant['restaurant_id']); ?>">
                    <input type="text" id="searchInput" name="search" placeholder="Search restaurants..." value="<?php echo htmlspecialchars($search); ?>">
                    
                    <select id="sortSelect" name="sort">
                        <option value="catagory" <?php if ($sort === 'catagory') echo 'selected'; ?>>Catagory</option>
                        <option value="name ASC" <?php if ($sort === 'name ASC') echo 'selected'; ?>>Name: A to Z</option>
                        <option value="name DESC" <?php if ($sort === 'name DESC') echo 'selected'; ?>>Name: Z to A</option>
                        <option value="price ASC" <?php if ($sort === 'price ASC') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="price DESC" <?php if ($sort === 'price DESC') echo 'selected'; ?>>Price: High to Low</option>
                    </select>
                    <button type="submit">Find</button>
                </form>
            </section>

            <div class="menu_container">
                <h2 style="text-align: center;"><span><?php echo $text ?></span> <span style="color: #88ff; font-style: italic;"><?php echo $search?></span> </h2>
                <?php
                    if ($menu_items) {
                ?>
                <div class="form_item">
                    <ul class="menu-items menu_grid">
                        <?php foreach ($menu_items as $item): ?>
                            <li class="menu-item">
                                <div onclick="location.href='menu_item_detail.php?menu_id=<?php echo $item['menu_id']; ?>';" class="food_image">
                                <img src="../../uploads/menu_images/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="food_name">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                </div>
                                <div class="discount">
                                    <p id="discount_value">Discount: <?php echo $item['discount'];?>%</p>
                                </div>
                                <p><?php echo htmlspecialchars($item['catagory']); ?></p>
                                <p>Price: <?php echo number_format($item['price'], 2); ?> birr</p>
                                <input type="number"  name="quantity[<?php echo $item['menu_id']; ?>]" id="quantity_<?php echo $item['menu_id']; ?>" min="1" value="1" style="width: 50px;">
                                <input type="hidden" id="discount_<?php echo $item['menu_id']; ?>" value="<?php echo $item['discount'];?>">
                                <button type="button"  data-menu-id="<?php echo $item['menu_id']; ?>" class="add_to_cart" title="Add item to Cart"> Add to cart <i class="fa-solid fa-cart-plus"></i></button>
                            </li>
                        <?php endforeach; ?>
                        
                        <script src="javaScript/add_menuto_cart_AJAX.js"> </script>
                    </ul>
                </div>

                <?php
                    } else {
                        echo "<p>No menu items found. Please try again.</p>";
                    }
                ?>
            </div>
        <?php else: ?>
            <section class="empty_menu_items">
                <div class="empty_menu_container">
                    <div class="empty_menu_image">
                    </div>
                    <div>
                        <h2>Sorry, we have no menu items available at this time.</h2>
                        <p>We apologize for the inconvenience. Please check back later or contact us for more information.</p>
                        <p>Thank you for your understanding!</p>
                    </div>
                </div>
            </section>
        <?php endif;
        $stmt->close();
        $conn->close();
        ?>

        <div class="back_to_res_container">
            <p>Want to order from another restaurant?</p>
            <p>Click the button below to go back to the restaurant list.</p>
            <div class="back_to_res_buttons">
                <a href="home.php" class="back_to_res">Back to Restaurant List</a>
                <a class="back_to_res" href="cart.php"><i class="fa-solid fa-cart-plus"></i> Go to Cart</a>
            </div>
        </div>

        <?php include "footer.php";?>

        <script src="javaScript/light_and_dark_mode.js"></script>
        <script src="javaScript/scroll_up.js"></script>

    </body>
    </html>