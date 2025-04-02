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

        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'name';
        $sort_order = $_GET['sort_order'] ?? 'ASC';

        $allowedSortColumns = ['catagory', 'name', 'price'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'name';
        }
        if ($sort == 'nameASC' || $sort == 'priceASC' || $sort == 'catagory'){
            $sort_order = 'ASC';
        }else{
            $sort_order = 'DESC';
        }

        if ($restaurant_id) {
            $sql = "SELECT * FROM menu WHERE restaurant_id = ? AND (name LIKE ? OR description LIKE ? OR catagory LIKE ?) ORDER BY $sort $sort_order";
            $stmt = $conn->prepare($sql);
            $searchQuery = "%" . $search . "%";
            $stmt->bind_param("isss", $restaurant_id, $searchQuery, $searchQuery, $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $menu_items = $result->fetch_all(MYSQLI_ASSOC);
        }
        else {
        header("Location: home.php");
        exit();
    }

    $sql = "SELECT * FROM restaurants WHERE restaurant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant = $result->fetch_assoc();

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($restaurant['name']); ?></title>
        <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
        <!--font ausome for star rating-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/topbar.css">
        <link rel="stylesheet" href="../footer.css">
    </head>
    <body>

    <section class="header">
        <!-- Top Bar -->
        <?php
            include "topbar.php";
        ?>
        <h1>Menu for <?php echo htmlspecialchars($restaurant['name']); ?></h1>
        <p>Location: <?php echo htmlspecialchars($restaurant['location']); ?></p>
        <p>Phone: <?php echo htmlspecialchars($restaurant['phone']); ?></p>
        <h2>Menu</h2>
    </section>

    <!--search and sort section-->
    <section class="search-sort container">
        <form id="searchForm" method="GET" class="search-form">
            <label for="search" class="label">Search Menus:</label>
            <input type="text" id="searchInput" name="search" placeholder="Search restaurants..." value="<?php echo htmlspecialchars($search); ?>">
            
            <select id="sortSelect" name="sort">
                <option value="catagory" <?php if ($sort === 'catagory') echo 'selected'; ?>>Catagory</option>
                <option value="nameASC" <?php if ($sort === 'nameASC') echo 'selected'; ?>>Name: A to Z</option>
                <option value="nameDESC" <?php if ($sort === 'nameDESC') echo 'selected'; ?>>Name: Z to A</option>
                <option value="priceASC" <?php if ($sort === 'priceASC') echo 'selected'; ?>>Price: Low to High</option>
                <option value="priceDESC" <?php if ($sort === 'priceDESC') echo 'selected'; ?>>Price: High to Low</option>
            </select>
            <button type="submit">Find</button>
        </form>
    </section>

        <div class="menu_container">
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
                            <p><?php echo htmlspecialchars($item['catagory']); ?></p>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                            <input type="number"  name="quantity[<?php echo $item['menu_id']; ?>]" id="quantity_<?php echo $item['menu_id']; ?>" min="1" value="1" style="width: 50px;">
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

        <a class="back_to_res" href="home.php">Back to Restaurant List</a>

        <?php
            $stmt->close();
            $conn->close();
            include "../footer.php";
         ?>

            <script src="javaScript/light_and_dark_mode.js"></script>
            <script src="../scroll_up.js"></script>

    </body>
    </html>