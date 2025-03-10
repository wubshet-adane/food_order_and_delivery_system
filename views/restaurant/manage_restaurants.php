<?php
require_once '../../models/manage_restaurant.php';
require_once '../../config/database.php';

$restaurantModel = new Restaurant($conn);
$restaurants = $restaurantModel->getAllRestaurants($userId);
?>

<section class="restaurant-management">
    <h2>🏢 Manage Your Restaurants</h2>
    
    <button id="addRestaurantBtn">➕ Add New Restaurant</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Restaurant Name</th>
                <th>Physical Location</th>
                <th>Google map Location</th>
                <th>Description</th>
                <th>Working Hours</th>
                <th>Tiktok</th>
                <th>Telegram</th>
                <th>Instagram</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($restaurants as $restaurant) : ?>
                <tr>
                    <td><?= $restaurant['restaurant_id'] ?></td>
                    <td><?= $restaurant['name'] ?></td>
                    <td><?= $restaurant['location'] ?></td>
                    <td><?= $restaurant['MAP_location'] ?></td>
                    <td><?= $restaurant['description'] ?></td>
                    <td><?= $restaurant['opening_and_closing_hour'] ?></td>
                    <td><?= $restaurant['tiktokAccount'] ?></td>
                    <td><?= $restaurant['telegramAccount'] ?></td>
                    <td><?= $restaurant['instagramAaccount'] ?></td>
                    <td><span class="status <?= strtolower($restaurant['status']) ?>"><?= $restaurant['status'] ?></span></td>
                    <td>
                        <button class="edit-btn" data-id="<?= $restaurant['restaurant_id'] ?>">✏️ Edit</button>
                        <button class="delete-btn" data-id="<?= $restaurant['restaurant_id'] ?>">🗑️ Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
