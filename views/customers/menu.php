    <?php
    session_start();
    require_once __DIR__ . '/../../config/database.php';

    /*if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
        header("Location: /views/auth/login.php");
        exit();
    }*/
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
        <title>Menu - <?php echo htmlspecialchars($restaurant['name']); ?></title>
        <link rel="stylesheet" href="css/home.css">
    </head>
    <body>


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


        <h1>Menu for <?php echo htmlspecialchars($restaurant['name']); ?></h1>
        <p>Location: <?php echo htmlspecialchars($restaurant['location']); ?></p>
        <p>Phone: <?php echo htmlspecialchars($restaurant['phone']); ?></p>

        <h2>Menu</h2>
        <?php
            if ($menu_items) {
        ?>
        <form action="order.php" method="POST">
            <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['restaurant_id']; ?>">
            
            <ul>
                <?php foreach ($menu_items as $item): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <input type="number" name="quantity[<?php echo $item['menu_id']; ?>]" min="1" value="1" style="width: 50px;">
                        <input type="hidden" name="menu_id[]" value="<?php echo $item['menu_id']; ?>">
                    </li>
                <?php endforeach; ?>
            </ul>
            <button type="submit">Place Order</button>
        </form>
        <?php
            } else {
                echo "<p>No menu items found. Please try again.</p>";
            }
        ?>
        <br>

        <a href="home.php">Back to Restaurant List</a>

        <?php
            $stmt->close();
            $conn->close();
            include "footer/footer.php";
         ?>
    </body>
    </html>
