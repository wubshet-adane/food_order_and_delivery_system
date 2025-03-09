 <!--add new menu section-->
 <section>
    <h2>Add New Menu Item</h2>
    <form action="../../controllers/restaurant_menu_controller.php" method="POST">
        <input type="text" name="name" placeholder="Dish Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <input type="text" name="image" placeholder="Image URL (for now)">
        <button type="submit" name="add_menu">Add Item</button>
    </form>
</section>