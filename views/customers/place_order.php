<?php
session_start();
    require '../../config/database.php';
    require_once __DIR__ . "/../../models/get_delivery_info.php";

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn'])){
        header("Location: ../auth/customer_login.php");
        exit();
    }

    //get restaurant specific lat and lng
    $user_Id = $_SESSION['user_id'];
    $res_lat = $_SESSION['restaurant_latitude'];
    $res_lng = $_SESSION['restaurant_longitude'];
    $res_id = $_SESSION['restaurant_id'];

    //get delivery address information
    $delivery_info = new Delivery_info($conn);
    $delivery = $delivery_info->getDeliveryInfo($user_Id);

    //get selected payment method from redirect url
    $paymnet_method = $_GET['paymentMethod'] ?? null;
    $note = $_GET['orderNote'] ?? null;
    $discount = $_GET['discount'] ?? 0;
    $service_fee = $_GET['service_fee'] ?? 0;
    $sub_total = $_GET['sub_total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Place Order</title>
        <link rel="icon" href="../../public/images/logo-icon.png" type="image/png" sizes="16x16">
        <link rel="stylesheet" href="css/topbar.css">
        <link rel="stylesheet" href="css/place_order.css">
        <link rel="stylesheet" href="css/footer.css">
        <!--font awsome-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!--sweetalert js library-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    </head>
<body>
    <header class="header_section">
        <?php include 'topbar.php'; ?>
    </header>
    <?php
        //fetch data from database
    if (!empty($delivery)){

        //distance calculator
        function calculateDistance($lat1, $lon1, $lat2, $lon2) {
            $earthRadius = 6371; // Radius of the Earth in kilometers
        
            // Convert degrees to radians
            $lat1 = deg2rad($lat1);
            $lon1 = deg2rad($lon1);
            $lat2 = deg2rad($lat2);
            $lon2 = deg2rad($lon2);
        
            // Differences
            $deltaLat = $lat2 - $lat1;
            $deltaLon = $lon2 - $lon1;
        
            // Haversine formula
            $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                 cos($lat1) * cos($lat2) *
                 sin($deltaLon / 2) * sin($deltaLon / 2);
        
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;
        
            return $distance; // in kilometers by default
        }

        foreach($delivery as $del):

            $del_lat = $del['latitude'];
            $del_lon = $del['longitude'];
            
            //call funtion
            $distance = calculateDistance($res_lat, $res_lng, $del_lat, $del_lon);
            //calculate delivery_fee
            $delivery_fee = $distance * 30;
            //actual delivery balance for deliivery partners after 3% paid for service fee
            $delivery_parson_fee =  $delivery_fee - $delivery_fee * 0.03;
            //total service fee calculated from restauranuts and delivery persons
            $full_service_fee = floatVal($service_fee) + floatVal($delivery_fee) * 0.03;
            //calculate grand total
            $grand_total = floatVal($sub_total) + $delivery_fee- floatVal($discount);
          //  grand total for hotel and restaurant
            $grand_total_for_restaurant = floatVal($sub_total) - floatVal($discount) - floatVal($service_fee);
            ?>
            <section class="place_order_container">
               
                <div class="delivery_address_section">
                    <div class="go_back">
                        <a href="menu.php"> menu </a><a href="cart.php"><i class="fas fa-angle-right"></i> cart </a> <a href=""> <i class="fas fa-angle-right"></i> Place order </a>
                    </div>
                    <!--contact related-->
                    <div class="contact_information">
                        <h2>Your Delivery Contact Information</h2>
                        <p><strong>Full Name:</strong> <span class="delivery_name"><?php echo htmlspecialchars($del['name']);?></span></p>
                        <p><strong>Email:</strong> <span class="checkoutEmail"><?php echo htmlspecialchars($del['email']);?></span></p>
                        <p><strong>Phone:</strong> <span class="checkoutPhone"><?php echo htmlspecialchars($del['phone']);?></span></p>
                    </div>
                    <!--address related-->
                    <div class="location_section">
                        <h2>Delivery Address order related Information</h2>
                        <p><strong>distance between your delivery address and the restaurant which you are ordering is (absolutly shortest):</strong> <span><?php echo round($distance, 2);?></span> km</p>
                        <p><strong>your prompt about delivery:</strong> <span class="note" id="note"><?php echo htmlspecialchars($note);?></span></p>
                    </div>
                    <!--price related-->
                    <div class="location_section">
                        <h2>payment and total price information</h2>
                        <p><strong>Normal price:</strong>
                            <span id="subtotal"><?php echo round($sub_total, 2);?> </span>birr
                            <i>original price before any discounts or extra fees.</i>
                        </p>
                        <p><strong>service fee:</strong>
                            <span id="service_fee">0.00 </span>birr
                        </p>
                        <p><strong>discount or Price Reduction:</strong>
                            <span id="discount"><?php echo round($discount, 2);?> </span>birr
                        </p>
                        <p><strong>delivery fee:</strong>
                            <span id="delivery_fee"><?php echo round($delivery_fee, 2);?> </span>birr
                        </p>
                        <p><strong>Total Amount Due:</strong> 
                            <span id="delivery_fee"><?php echo round($grand_total, 2);?> </span>birr
                        </p>
                    </div>
                </div>

                <div class="payment_section">
                    <?php if ($paymnet_method): ?>
                        <div>
                            <h2>Your selected payment method is:</h2>
                            <?php if ($paymnet_method == "telebirr"): ?>
                                <div class="telebirr_payment_method">
                                    <h3>Telebirr ✔️</h3>
                                    <div class="payment_method" id="screenshot_payment_method">
                                            <div class="payment_method_text">
                                                <h2>Telebirr payment method</h2>
                                                <p><img src="../../public/images/telebirr icon.jpg" alt="" width="50px" height="50px"> <span> Pay with telebirr</span></p>
                                            </div>
                                            <div class="payment_method_radio" id="screenshotchecked">
                                                
                                            </div>
                                        </div>
                                </div>
                            <?php else: ?>
                                <div class="screenshot_payment_method">
                                    <h3>Screenshot Payment ✔️</h3>
                                    <!--screenshot payment method-->
                                    <section class="screenshot_section">
                                        <div class="payment_method" id="screenshot_payment_method">
                                            <div class="payment_method_text">
                                                <h3>Screenshot payment method</h3>
                                                <p><img src="../../public/images/screenshot icon.jpg" alt="" width="50px" height="50px"> 
                                                <span> Upload Screenshot bellow</span></p>
                                            </div>
                                            <div class="payment_method_radio" id="screenshotchecked">
                                            <i class="fa-solid fa-circle-check" style="font-size: 32px; color: green;"></i>
                                            </div>
                                        </div>
                                        <!-- screenshot upload section-->
                                        <div class="upload_section" id="upload_section">
                                            <p>pay with the following bank account, telebirr and upload screenshot</p>
                                            <ul>
                                                <li><strong>CBE Account:</strong> <span>G3 food online food ordering sytem, 10000456789</span></li>
                                                <li><strong>Telebirr:</strong>  <span>Esrael-Admasu = 0920466472 / Wubshet-Adane = 0965868933</span></li>
                                                <li><strong>Mpesa:</strong> <span>Esrael Admasu = 0703978283</span></li>
                                            </ul>
                                            <label for="screenshot" class="upload_label">Upload Screenshot Here:</label><br>
                                            <div>
                                                <input type="file" id="screenshot_img" name="screenshot" accept="image/*" required onchange="readURL(this)">
                                            </div>
                                            <span class="error" id="error_screenshot"></span>
                                            <div id="preview"></div><!--display upload image from file-->
                                            <div class="transactionid">
                                                <input type="text" id="transaction_id_input" class="transaction_id_input" required placeholder="enter transaction id">
                                            </div>
                                            
                                            <!--upload screenshot preview-->
                                            <script>
                                                // Wait for the DOM to be fully loaded
                                                window.addEventListener('DOMContentLoaded', function () {
                                                    const img = document.getElementById('screenshot_img');
                                                    //const on_off = document.getElementById('camera_on_off');

                                                    // Function to preview image as background
                                                    function readURL(input) {
                                                        if (input.files && input.files[0]) {
                                                            var reader = new FileReader();
                                                            reader.onload = function (e) {
                                                                var preview = document.getElementById('preview');
                                                                preview.style.backgroundImage = 'url(' + e.target.result + ')';
                                                                preview.style.opacity = '0'; // for transition
                                                                setTimeout(function () {
                                                                    preview.style.display = "block";
                                                                    preview.style.opacity = "1";
                                                                }, 500);
                                                            };
                                                            reader.readAsDataURL(input.files[0]);
                                                        }
                                                    }
                                                    // Attach event listener if needed (or use inline `onchange`)
                                                    img.addEventListener('change', function () {
                                                        readURL(this);
                                                    });

                                                });
                                            </script>
                                        </div>
                                    </section>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="hidden" id="cust_id" value="<?php echo $user_Id?>">
                            <input type="hidden" id="res_id" value="<?php echo $res_id?>">
                            <input type="hidden" id="delivery_parson_fee" value="<?php echo round($delivery_parson_fee, 2)?>">
                            <input type="hidden" id="order_total" value="<?php echo round($grand_total_for_restaurant, 2);?>">
                            <input type="hidden" id="full_service_fee" value="<?php echo round(floatVal($full_service_fee), 2);?>">
                            <input type="hidden" id="order_note" value="<?php echo htmlspecialchars($note);?>">
                            <input type="hidden" id="order_payment_method" value="<?php echo $paymnet_method?>">
                        </div>


                        <div class="place_order_section">
                            <button id="place_order_btn">Place Order</button>
                        </div>

                    <?php else: ?>
                        <div class="payment_method_error">
                            <div>
                                <img src="../../public/images/no payment method.jpg" alt="" width="auto" height="auto">
                            </div>
                            <p><strong>Payment method error!</strong></p>
                            <p>Go back to the cart page and select a valid payment method. 
                                <a href="cart.php#submitBtn">Select payment method</a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php
        endforeach;
    }else{
    ?>
    <section>
        <div>
            <p>not delivery information</p>
        </div>
    </section>
    <?php
    }
    //footer
    include "footer.php"; 
    ?>

    <!--scroll top btn-->
    <script src="javaScript/scroll_up.js"></script>

    <!--sweetalet message-->
    <script src="javaScript/placeorder.js"></script>
</body>
</html>
