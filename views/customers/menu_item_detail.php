<?php
// Include your database connection file (update the path as needed)
include('../../config/database.php');

// Start the session for possible cart functionality (optional)
session_start();

// Check if the menu_id is set in the URL
if (isset($_GET['menu_id'])) {
    $menu_id = $_GET['menu_id'];

    // Prepare a SQL query to fetch the menu item details
    $query = "SELECT * FROM menu WHERE menu_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $menu_id); // "i" stands for integer binding
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a result was returned
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc(); // Fetch the item details
    } else {
        $error_message = "Item not found.";
    }

    //fetch information about restaurant where the menu item is located
    $sql = "SELECT * FROM restaurants WHERE restaurant_id = ?";
    $cafe_stmt = $conn->prepare($sql);
    $cafe_stmt->bind_param("i", $item['restaurant_id']);
    $cafe_stmt->execute();
    $cafe_result = $cafe_stmt->get_result();
    $cafe = $cafe_result->fetch_assoc();
    
} else {
    $error_message = "No menu item selected.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Item Details</title>
    <link rel="stylesheet" href="css/menu_item_detail.css"> <!-- Link to external stylesheet -->
    <!--font awsome-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script> <!-- For icons -->
</head>
<body>

    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php else: ?>
            <div class="menu-item-detail">
                <div class="item-image">
                    <img src="../../uploads/menu_images/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="zoom-img">
                </div>

                <div class="item-info">
                <button class="back-to-menu" onclick="window.history.back()"><i class="fa-solid fa-backward-step"></i> Back to Menu</button>

                    <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                    <h4 class="description"><?php echo htmlspecialchars($item['description']); ?></h4><br>
                    <div class="detail_info">
                        <h4>Detail information about this food item:</h4>
                        <h4>This food Prepared at <?php echo $cafe['name']; ?></h4>
                        <p><?php echo htmlspecialchars($item['name']);?> included in the catagory of<?php echo htmlspecialchars($item['catagory']); ?></p>
                        <p><?php echo htmlspecialchars($item['description']);?></p>
                        <h4>To make this type of meal we were contained the following type of ingrediants:</h4>
                        <p><?php echo htmlspecialchars($item['content']);?></p>
                        <input type="hidden"  name="quantity[<?php echo $item['menu_id']; ?>]" id="quantity_<?php echo $item['menu_id']; ?>" min="1" value="1" style="width: 50px;">
                        <h4>related informations</h4>
                        <code>
                            <p>Location: <?php echo htmlspecialchars($cafe['location']); ?></p>
                            <p>Phone: <?php echo htmlspecialchars($cafe['phone']); ?></p>
                        </code>
                    </div>
                    <h4>price for single item:
                        <span class="price"><?php echo number_format($item['price'], 2); ?> Birr.</span>
                    </h4><br>

                    <div class="item-actions">
                        <button type="button" data-menu-id="<?php echo $item['menu_id']; ?>" class="add_to_cart" title="Add item to Cart"> Add to cart <i class="fa-solid fa-cart-plus"></i></button>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>

    <script>
        function addToCart(menuId) {
            // You can implement AJAX to add the item to the cart dynamically
            alert("Item " + menuId + " added to cart!");
            // Optionally, you can store the item in the session or use local storage.
        }
    </script>
    <!-- script for add items to cart automaticalky without reload the page by using AJAX -->
     <script src="javaScript/add_menuto_cart_AJAX.js"></script>
    
</body>
</html>
