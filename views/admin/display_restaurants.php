<p class="greeting_message">
        "Here you can manage your restaurant’s menu, view incoming orders, track deliveries, and update business info. Use the sidebar to navigate between different sections of your dashboard."
    </p>

<section class="restaurant-container">
    
    <div class="restaurant-header">
        <h2>Your Registered Restaurants</h2>
        <button class="add-restaurant-btn" onclick="location.href='restaurant_register_form.php'">Add Restaurant</button>
    </div>

    <div class="restaurant-grid">
        <?php if (count($restaurants) > 0): ?>
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="restaurant-card">
                    <div class="restaurant-info">
                        <div class="logo">
                            <img src="restaurantAsset/<?=$restaurant['image']?>" alt="logo">
                        </div>
                        <p><strong>Name:</strong> <?= htmlspecialchars($restaurant['name']) ?></p>
                        <p><strong>Adress:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                    </div>
                    <a href="manage_restaurants.php?id=<?= $restaurant['restaurant_id']?>&name=<?= $restaurant['name']?>" class="view-details-btn">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-restaurant">No restaurants found.</p>
        <?php endif; ?>
    </div>
</section>
