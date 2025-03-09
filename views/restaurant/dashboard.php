<?php
require_once '../../models/manage_menu.php';
$menuItems = Menu::getAllItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!--header section-->
    <section class="header_section">
        <div class="responce_message" id="responce_message">
            <?php 
            if (isset($_GET['error'])){
                $message = $_GET['error'];
            ?>
                <p style="color:red;"><?php echo $message;?>.</p>
            <?php }
            if (isset($_GET['success'])) {
                $message = $_GET['success'];
            ?>
                <p style="color:green;"><?php echo $message;?>.</p>
            <?php }?>
        </div>
        wubshet and esrael
    </section>

    <!--sidebar And MainContent section-->
    <section class="sidebarAndMainContent_section">
        <!--sidbar section-->
        <div class="sidebar">
            <div class="sidbar_content">
                 <h2>🍽️ My Restaurant</h2>
                 <ul class="sidebar_ul">
                    <li><a href="dashboard.php">🏠 Dashboard</a></li>
                    
                    <!-- Restaurant Management -->
                    <li><a href="manage_restaurants.php">🏪 Manage Restaurants</a></li>
                    <li><a href="menu.php" class="active">📋 Manage Menu</a></li>
                    <li><a href="orders.php">🛒 Orders</a></li>
                    <li><a href="ratings.php">⭐ Ratings & Reviews</a></li>

                    <!-- Financial Section -->
                    <li class="dropdown">
                        <a href="monetary.php">💰 Monetary ▾</a>
                        <ul class="dropdown-menu">
                            <li><a href="transactions.php">💳 Transactions</a></li>
                            <li><a href="earnings.php">📈 Earnings</a></li>
                            <li><a href="payouts.php">💵 Payouts</a></li>
                            <li><a href="financial_reports.php">📊 Financial Reports</a></li>
                            <hr>
                        </ul>
                    </li>

                    <!-- Analytics & Reports -->
                    <li><a href="reports.php">📊 Reports & Analytics</a></li>

                    <!-- Settings & Logout -->
                    <li><a href="settings.php">⚙️ Settings</a></li>
                    <li><a href="#">🚪 Logout</a></li>
                </ul>

            </div>
        </div>

        <!--main-content section-->
        <div class="main-content">
            <header>
                <h1>Manage Menu</h1>
            </header>

            <!--display menu section-->
            <section>
                <h2>Existing Menu Items</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($menuItems as $item): ?>
                    <tr>
                        <td><?= $item['menu_id']; ?></td>
                        <td><?= $item['name']; ?></td>
                        <td><?= $item['description']; ?></td>
                        <td>$<?= number_format($item['price'], 2); ?></td>
                        <td>
                            <form action="../../controllers/restaurant_menu_controller.php" method="POST">
                                <input type="hidden" name="id" value="<?= $item['menu_id']; ?>">
                                <button type="submit" name="delete_menu">❌ Delete</button>
                            </form>
                        </td>
                        <td>
                            <button class="edit-btn" data-id="<?= $item['menu_id']; ?>" data-name="<?= $item['name']; ?>" 
                                data-description="<?= $item['description']; ?>" data-price="<?= $item['price']; ?>" 
                                data-image="<?= $item['image']; ?>"> ✏️ Edit </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </section>

            <!--edit menu modal section-->
            <section id="edit-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Edit Menu Item</h2>
                    <form action="../../controllers/restaurant_menu_controller.php" method="POST">
                        <input type="hidden" name="id" id="edit-id">
                        <input type="text" name="name" id="edit-name" placeholder="Dish Name" required>
                        <textarea name="description" id="edit-description" placeholder="Description"></textarea>
                        <input type="number" name="price" id="edit-price" placeholder="Price" step="0.01" required>
                        <input type="text" name="image" id="edit-image" placeholder="Image URL">
                        <button type="submit" name="edit_menu">Save Changes</button>
                    </form>
                </div>
            </section>

        </div>
    </section>

    <!--footer section-->
    <footer class="footer_section">
        copywrite &copy; 2017 G3 Ethiopia;
    </footer>

    <!--edit menu modal script-->
    <script src="javaScript/edit_menu_modal.js"></script>
    <script src="javaScript/settimeout.js"></script>
    <script>
        //script for closing responce messages automaticaly
        closeResponseById("responce_message"); // Auto-hide after 5 seconds
    </script>

</body>
</html>
