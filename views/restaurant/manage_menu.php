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
