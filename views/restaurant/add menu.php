<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
    <link rel="stylesheet" href="css/add_menu.css">
    <link rel="stylesheet" href="css/restaurant_register.css">
    <style>

    </style>
</head>
<body>

<div class="container">
    <?php 
        $thisRestaurantId = $_GET['resId'];
    ?>

    <h2>Add New Menu Item</h2>
    <div style="display: flex; justify-content: space-between;" >
        <button id="darkModeToggle">🌙</button>
        <a href="javascript:history.back()" class="back"><i class="fa-solid fa-backward">&nbsp;&nbsp;Back</i></a>
    </div>
    <form action="../../controllers/restaurant_menu_controller.php?res_id=<?php echo $thisRestaurantId;?>" method="POST" enctype="multipart/form-data">
        
        <div class="input-group">
            <label for="name">Menu Name <i class="fa-solid fa-circle-info"></i></label>
            <input type="text" name="name" placeholder="Dish Name" required>
        </div>

        <div class="input-group">
            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="Appetizer">Appetizer</option>
                <option value="Main Course">Main Course</option>
                <option value="Dessert">Dessert</option>
                <option value="Beverages">Beverages</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="input-group">
            <label for="content">Ingredients <i class="fa-solid fa-circle-info"></i> <p class="detail_info">
                Enter ingredients or contents that your restaurant 
                used to prepare this meal, separate ingredients with commas, like "meat, salad, rice ..."</p> </label>
            <textarea name="content" rows="3" placeholder="List ingredients to prepare the meal"></textarea>
        </div>

        <div class="input-group">
            <label for="description">Description <i class="fa-solid fa-circle-info"></i><p class="detail_info">
                Describe detail this menu simply understand to customers like "uses for bodies, for motivation, e t c..."</p> </label>
            <textarea name="description" placeholder="Description about this menu item" rows="4"></textarea>
        </div>

        <div class="input-group">
            <label for="price">Price <i class="fa-solid fa-circle-info"></i><p class="detail_info">
                write price of this meal in ethiopian currency, hence don't add above two digit after point..."</p> </label>
            <input type="number" name="price" id="price" placeholder="Price" step="0.01" required>
        </div>

        <div class="input-group">
            <label for="discount">Discount <i class="fa-solid fa-circle-info"></i><p class="detail_info">
                If you want, write discount amount in number format with out percent sign like if you want to add "10%" discount just write as '10'.</p> </label>
            <input type="number" name="discount" id="discount" placeholder="discount" min="1" max="100">
        </div>

        <div class="input-group">
            <label for="image">Menu Image <i class="fa-solid fa-circle-info"></i> <p class="detail_info">
                select square sized image and then post that image that will display at customers interface, ..."</p> </label>
            <input type="file" name="image" id="image" required>
            <img src="" id="imagePreview">
        </div>

        <button type="submit" name="add_menu" class="btn">Add Item</button>
    </form>
</div>

<div class="footer">
    <p>© <span id="year"></span> All rights reserved. Developed by - <a href="#">G3 Online Food Order</a></p>
</div>

<script>
    // Live Image Preview
    document.getElementById('image').addEventListener('change', function(event) {
        let file = event.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Set current year in footer
    document.getElementById('year').innerText = new Date().getFullYear();
</script>
<script src="../customers/javaScript/light_and_dark_mode.js"></script>

</body>
</html>
