
    <div class="review_container">
        <h1>Customer Reviews</h1>

        <?php if (count($restaurants) > 0): ?>
            <?php foreach ($restaurants as $restaurant): 
                            
                $res_id = $restaurant['restaurant_id'];
                // Fetch all reviews
                $review = new Review($conn);
                $review_rows = $review->getRestaurantReviews($res_id);
                ?>
                <div class="restaurant-box">
                    <img src="<?php echo !empty($row['restaurant_logo']) ? 'restaurantAsset/' . $row['restaurant_logo']: '../../public/images/restaurant.jpg' ?>" alt="Restaurant Logo" class="restaurant-logo">
                    <div class="restaurant-name"><?php echo htmlspecialchars($restaurant['name']); ?></div>
                <?php
                if (count($review_rows) > 0):
                    foreach($review_rows as $row): ?>
                        <div class="review-box">
                            <div class="review-details">
                                <div class="profile-info">
                                    <img src="<?php echo !empty($row['profile_image']) ? '../../uploads/user_profiles//' .  $row['profile_image'] : '../../public/images/profile icon.jpg'; ?>" alt="User profile" class="profile-image">
                                    <span class="review-meta">
                                        Reviewed by <strong><?php echo htmlspecialchars($row['user_name']); ?></strong> 
                                        on <?php echo date("F j, Y", strtotime($row['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="stars">
                                    <?php echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']) . ' ' . $row['rating'] . '/' . 5; ?>
                                </div>
                                <div class="comment">"<?php echo htmlspecialchars($row['review_text']); ?>"</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-review">
                        No reviews have been submitted yet. Be the first to review a restaurant!
                    </div>
                <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-review">
                😔 No restaurants found. Please add a restaurant to see reviews.
            </div>
        <?php endif; ?>
        
    </div>
