<!-- Promotion Banner -->
<?php if($recomendation): ?>
    <section class="recommendation">
        <div class="top-rec">
            <h2 class="recomend_title">Top rated restaurants that you maybe glad with their dish's!!!</h2>
            <div class="slider-controls">
                <button class="close-banner" title="Close banner">×</button>
                <div class="slider">
                    <button class="arrow-button arrow-left" aria-label="Scroll left">‹</button>
                    <button class="arrow-button arrow-right" aria-label="Scroll right">›</button>
                </div>
            </div>
        </div>
        <div class="marquee-container">
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
                                        $rating = round($rec['avg_rating'], 1);
                                        $fullStars = floor($rating);
                                        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                                        $emptyStars = 5 - ($fullStars + $halfStar);
                                        
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        }
                                        if ($halfStar) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        }
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            echo '<i class="fa-regular fa-star"></i>';
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
        </div>
    </section>
<?php endif; ?>