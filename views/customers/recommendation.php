 <!-- Promotion Banner -->
  <?php 
    if($recomendation): 
    ?>
        <section class="recommendation">
            <div class="top-rec">
                <h2 class="recomend_title">Top rated restaurants that you maybe glad with their dish's!!! </h2>
                <div class="slider">
                    <button class="arrow-button arrow-left">‹</button>
                    <button class="arrow-button arrow-right">›</button>
                </div>
            </div>
            <marquee behavior="" direction="left">
                <div class="recommendation-cards">
                    <?php foreach($recomendation as $rec):?>
                        <a href="menu.php?restaurant_id=<?=$rec['restaurant_id']?>">
                            <div class="recommendation-card">
                                <div class="image">
                                    <img src="../restaurant/restaurantAsset/<?=$rec['image']?>" alt="<?=$rec['image']?>">
                                </div>
                                <div class="content">
                                    <h3><?=$rec['name']?></h3>
                                    <p>Review: 
                                        <?php 
                                            $rating = round($rec['avg_rating'], 1); // Round to 1 decimal place
                                        
                                            // Display stars based on the average rating
                                            $fullStars = floor($rating); // Count full stars
                                            $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0; // Check for a half star
                                            $emptyStars = 5 - ($fullStars + $halfStar); // Remaining empty stars
                                    
                                            // Display full stars
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="fa-solid fa-star"></i>'; // Full Star
                                            }
                                    
                                            // Display half star if needed
                                            if ($halfStar) {
                                                echo '<i class="fa-solid fa-star-half-stroke"></i>'; // Half Star
                                            }
                                    
                                            // Display empty stars
                                            for ($i = 0; $i < $emptyStars; $i++) {
                                                echo '<i class="fa-regular fa-star"></i>'; // Empty Star
                                            }
                                        ?>
                                        <strong> <?=$rating?>/5</strong>
                                        <span class="reviewer"><?=$rec['no_of_reviewers']?> <i class="fa-solid fa-person"></i></span>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach;?>   
                </div>                                                                                 
            </marquee>
        </section>
    <?php endif;?>