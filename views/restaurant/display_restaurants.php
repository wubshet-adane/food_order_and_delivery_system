<section class="restaurant-container">
    <h2>📍 My Restaurant Locations</h2>

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
                    <a href="manage_restaurants.php?id=<?= $restaurant['restaurant_id'] ?>" class="view-details-btn">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-restaurant">No restaurants found.</p>
        <?php endif; ?>
    </div>
</section>
