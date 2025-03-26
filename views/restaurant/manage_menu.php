
<?php
require_once __DIR__ . '/../../config/database.php';
require_once '../../models/manage_menu.php';
?>

<header class="menu-header">
    <h3 class="menu-title">Manage Menu here based on the restaurants you post menu</h3>
    <div class="searching_and_sorting">
        <div class="searching">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                <input type="text" name="search" placeholder="Search Menu Items" class="search-input">
                <button type="submit" name="search_menu" class="search-btn">Search</button>
            </form>
        </div>
        <div class="sorting">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                <select name="sort" class="sort-select">
                    <option value="name ASC">Name A-Z</option>
                    <option value="name DESC">Name Z-A</option>
                    <option value="catagory ASC">Category A-Z</option>
                    <option value="price ASC">Price ascending</option>
                    <option value="price DESC">Price descending</option>
                </select>
                <button type="submit" name="sort_menu" class="sort-btn">Sort</button>
            </form>
        </div>
    </div>
</header>

<div class="restaurant_with_menu">
    <div class="all_restaurant">

    <?php if (count($restaurants) > 0): ?>
        <?php foreach ($restaurants as $restaurant): 
            //assign restaurant id
            $res_Id = $restaurant['restaurant_id'];
            $menuItems = Menu::getAllItems($res_Id);
        ?>
            <div class="each_restaurant">
                <div class="restaurant-info">
                    <div class="res_card_image">
                        <img src="restaurantAsset/<?=$restaurant['image']?>" alt="card image">
                    </div>
                    <p><strong>Name:</strong> <?= htmlspecialchars($restaurant['name']) ?></p>
                    <p><strong>Adress:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                </div>
                
            </div>
        <?php endforeach; ?>

    <?php else: ?>
    <p class="no-restaurant">No restaurants found.</p>
    <?php endif; ?>

    </div>
</div>

<!-- Edit Menu Modal Section -->
<section id="edit-modal" class="modal">
<div class="modal-content">
    <span class="close" id="close-modal">&times;</span>
    <h2 class="modal-title">Edit Menu Item</h2>
    <form action="../../controllers/restaurant_menu_controller.php" method="POST" class="edit-form">
        <input type="hidden" name="id" id="edit-id" class="edit-input">

        <div class="input-group">
            <label for="edit-name">Dish Name</label>
            <input type="text" name="name" id="edit-name" placeholder="Dish Name" required class="edit-input">
        </div>

        <div class="input-group">
            <label for="edit-description">Description</label>
            <textarea name="description" id="edit-description" placeholder="Description" class="edit-input"></textarea>
        </div>

        <!-- Horizontal Fields: Price and Category -->
        <div class="input-group-horizontal">
            <div class="input-group">
                <label for="edit-price">Price</label>
                <input type="number" name="price" id="edit-price" placeholder="Price" step="0.01" required class="edit-input">
            </div>

            <div class="input-group">
                <label for="edit-category">Category</label>
                <select name="category" id="edit-category" class="edit-input">
                    <option value="appetizer">Appetizer</option>
                    <option value="main">Main Course</option>
                    <option value="dessert">Dessert</option>
                    <option value="beverages">Beverages</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <!-- Horizontal Fields: Dietary Options and Preparation Time -->
        <div class="input-group-horizontal">
            <div class="input-group">
                <label for="edit-dietary-options">Dietary Options</label>
                <input type="text" name="dietary_options" id="edit-dietary-options" placeholder="e.g., Vegan, Gluten-Free" class="edit-input">
            </div>

            <div class="input-group">
                <label for="edit-preparation-time">Preparation Time (in minutes)</label>
                <input type="number" name="preparation_time" id="edit-preparation-time" placeholder="Preparation Time" class="edit-input">
            </div>
        </div>

        <div class="input-group">
            <label for="edit-ingredients">Ingredients</label>
            <textarea name="ingredients" id="edit-ingredients" placeholder="Ingredients (separate with commas)" class="edit-input"></textarea>
        </div>

        <div class="input-group">
            <label for="edit-image">Image URL</label>
            <input type="text" name="image" id="edit-image" placeholder="Image URL" class="edit-input">
        </div>

        <div class="input-group">
            <label for="edit-availability">Available</label>
            <select name="availability" id="edit-availability" class="edit-input">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <button type="submit" name="edit_menu" class="save-btn">Save Changes</button>
    </form>
</div>

</section>
