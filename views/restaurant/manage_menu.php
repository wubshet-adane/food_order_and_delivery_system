
<?php
require_once __DIR__ . '/../../config/database.php';
require_once '../../models/manage_menu.php';
?>

<div class="manage_menu">
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
                    <div class="restaurant_info">
                        <div class="res_detail">
                            <div class="res_card_image">
                                <img src="restaurantAsset/<?=$restaurant['image']?>" alt="card image">
                            </div>
                            <div class="restaurant_name">
                                <p><strong><?= htmlspecialchars($restaurant['name']) ?></strong> </p>
                                <p> <?= htmlspecialchars($restaurant['location']) ?></p>
                            </div>
                        </div>
                        <div class="actions">
                            <button id="addMenu" class="add_menu action_btn" onclick="location.href='add menu.php?resId=<?php echo $restaurant['restaurant_id'];?>'">Add Menu</button>
                            <button id="expandBtn<?php echo $restaurant['restaurant_id'];?>" onclick="toggleMenu(<?php echo $restaurant['restaurant_id'];?>)" class="expandBtn action_btn"><i class="fa fa-solid fa-chevron-down"></i></button>                       </div>
                    </div>
                    
                    <!-- Display Menu Section -->
                    <section class="menu-section" id="menu_section<?php echo $restaurant['restaurant_id'];?>">
                        <div class="section_title_box">
                            <h4 class="section-title">Existing Menu Items at <i style="color:#ff9900;"><?= htmlspecialchars($restaurant['name']) ?></i></h4>
                            <button id="deleteAllMenu" class="delete_all_btn" onclick="deleteAllMenu('<?php echo $restaurant['restaurant_id'];?>', '<?php echo $restaurant['name'];?>')">Delete all</button>
                            <script>
                                function deleteAllMenu(resId, resName) {
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        html: `You want to delete <span style="color: red; font-weight: bold; font-family: Arial, sans-serif;">All menu items</span> at <span style="color: blue; font-weight: bold; font-style:italic;">${resName}</span>?`,
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Delete all menu!',
                                        cancelButtonText: 'Cancel'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = `../../controllers/restaurant_menu_controller.php?action=deleteAll&res_id=${resId}`;
                                            Swal.fire(
                                                'Deleted!',
                                                `Menu items ${resName} have been deleted.`,
                                                'success'
                                            );
                                        } else {
                                            Swal.fire(
                                                'Cancelled',
                                                'Your menu item is safe:)',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            </script>
                        </div>
                        <table class="menu-table">
                            <tr class="table-header">
                                <th class="table-cell">Image</th>
                                <th class="table-cell">Name</th>
                                <th class="table-cell">Category</th>
                                <th class="table-cell hide_desc">Content</th>
                                <th class="table-cell hide_desc">Description</th>
                                <th class="table-cell">Price</th>
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
                                <td class="table-cell"><?= $item['catagory']; ?></td>
                                <td class="table-cell hide_desc"><?= $item['description']; ?></td>
                                <td class="table-cell hide_desc"><?= $item['content']; ?></td>
                                <td class="table-cell"><?= number_format($item['price'], 2); ?> birr</td>
                                <td class="table-cell">
                                    <form action="../../controllers/restaurant_menu_controller.php?action=delete&id=<?php echo $item['menu_id'];?>" method="POST" id="delete-form-<?= $item['menu_id'];?>" class="delete-form">
                                        <input type="hidden" name="id" value="<?= $item['menu_id']; ?>">
                                        <button type="button" name="delete_menu" class="delete-btn" onclick="confirmDelete('<?=$item['menu_id'];?>','<?=$item['name'];?>')" title="delete"><i class="fa-solid fa-trash"></i></button>
                                        <script>
                                            function confirmDelete(menuId, menuName) {
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    html: `You want to delete <span style="color: red; font-weight: bold; font-family: Arial, sans-serif;">${menuName}</span>?`,
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Delete menu!',
                                                    cancelButtonText: 'Cancel'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        document.getElementById(`delete-form-${menuId}`).submit();
                                                        Swal.fire(
                                                            'Deleted!',
                                                            `Menu item ${menuName} has been deleted.`,
                                                            'success'
                                                        );
                                                    } else {
                                                        Swal.fire(
                                                            'Cancelled',
                                                            'Your menu item is safe:)',
                                                            'error'
                                                        );
                                                    }
                                                });
                                            }
                                        </script>
                                    </form>
                                    <button class="edit-btn" id="edit-btn-<?= $item['menu_id'];?>" data-name="<?= $item['name']; ?>"
                                        data-description="<?= $item['description']; ?>" data-catagory="<?= $item['catagory']; ?>" 
                                        data-content="<?= $item['content']; ?>" data-price="<?= $item['price']; ?>" data-discount="<?=$item['discount']?>" 
                                        data-image="<?= $item['image']; ?>" onclick="editMenu(<?= $item['menu_id'];?>)" title="edit">
                                        <i class="fa fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </section>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
        <p class="no-restaurant">No restaurants found.</p>
        <?php endif; ?>
        </div>
    </div>

    
    <!-- Edit Menu Modal Section -->
    <section id="edit-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="close-modal">&times;</span>
            <h2 class="modal-title">Edit Menu Item</h2>
            <form action="../../controllers/restaurant_menu_controller.php?action=editMenu&id=<?php echo $item['menu_id'];?>" method="POST" enctype="multipart/form-data" class="edit-form">
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
                            <option value="Appetizer">Appetizer</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Beverages">Beverages</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="edit-discount">Discount</label>
                    <input type="number" name="discount" id="edit-discount" placeholder="in percent but cant add % sign"  class="edit-input">
                </div>
                
                <div class="input-group">
                    <label for="edit-ingredients">Ingredients (contents)</label>
                    <textarea name="content" id="edit-content" placeholder="Ingredients (separate with commas)" class="edit-input"></textarea>
                </div>

                <div class="input-group">
                    <div>
                        <label for="edit-image">Image</label>
                        <input type="file" name="image" id="edit-image" class="edit-input">
                    </div>
                    <div class="img_view" id="img_view" style="width: 200px; height: 150px;">
                        <img src="" id="imagePreview" style="display: none; width: 100px; height: 100px;">
                    </div>
                </div>

                <button type="submit" name="edit_menu" class="save-btn">Save Changes</button>
            </form>
        </div>
    </section>
    <script>
        // File preview functionality
        document.getElementById('edit-image').addEventListener('change', function(event) {
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
    </script>
</div>
