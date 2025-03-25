
<?php
require_once __DIR__ . '/../../config/database.php';

?>

<header class="menu-header">
    <h1 class="menu-title">Manage Menu</h1>
</header>

<!-- Display Menu Section -->
<section class="menu-section">
    <h2 class="section-title">Existing Menu Items</h2>
    <table class="menu-table">
        <tr class="table-header">
            <th class="table-cell">Image</th>
            <th class="table-cell">Name</th>
            <th class="table-cell">Description</th>
            <th class="table-cell">Price (ETB)</th>
            <th class="table-cell">Actions</th>
        </tr>
        <?php foreach ($menuItems as $item): ?>
        <tr class="menu-item-row">
            <td class="table-cell image-column">
                <div class="image-box">
                    <img src="../../uploads/menu_images/<?= $item['image']; ?>" alt="img<?= $item['menu_id']; ?>" class="menu-item-img">
                </div>
            </td>
            <td class="table-cell"><?= $item['name']; ?></td>
            <td class="table-cell"><?= $item['description']; ?></td>
            <td class="table-cell"><?= number_format($item['price'], 2); ?></td>
            <td class="table-cell">
                <form action="../../controllers/restaurant_menu_controller.php" method="POST" class="delete-form">
                    <input type="hidden" name="id" value="<?= $item['menu_id']; ?>">
                    <button type="submit" name="delete_menu" class="delete-btn">❌ </button>
                </form>
                <button class="edit-btn" data-id="<?= $item['menu_id']; ?>" data-name="<?= $item['name']; ?>"
                        data-description="<?= $item['description']; ?>" data-price="<?= $item['price']; ?>" data-image="<?= $item['image']; ?>"> ✏️ Edit </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>

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
