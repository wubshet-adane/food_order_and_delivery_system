<?php
session_start();
require '../../config/database.php'; // your DB connection
require_once __DIR__ . "/../../models/cart.php";

//check if user is logged in or not...
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn']) || $_SESSION['userType'] !== "customer") {
    header("Location: ../auth/customer_login.php");
    exit();
}

//assign logged i user id to variable 
$user_id = $_SESSION['user_id'];

//create object using class name and send connection as parameter for constructor created at cart.php at model repository...
$cartModel = new Cart($conn);
//call function with looggewd in user id  as argument by usinng created object
$cart = $cartModel->getCart($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <!--font ausome for star rating-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!--google font-->
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="css/footer.css">
   
    <style>
    #map {
        height: 300px;
        margin-bottom: 1rem;
        border-radius: 8px;
    }

    @media screen and (max-width: 600px) {
        #map {
            height: 250px;
        }
    }
    </style>

</head>
<body>
    
<?php include 'topbar.php';?>

    <div class="cart-container">
        <h1>Your Shopping Cart</h1>
        <h3> it will be deleted after 24 hours, place your order as soon as possible!</h3> 

        <?php
        $total = 0;
        $qty = 0;
        $discount = 0;
        $total_discount = 0;
        $_SESSION['qty'] = 0; // Initialize session variable for quantity
        $res_id = 0; // Get restaurant_id from the first row if available
        //$delivery_distance = $_SESSION['distance']; // Initialize delivery distance

        if ($cart->num_rows>0){
            ?>
        <div class="table_box">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th class="td_price"> Price (ETB) for each</th>
                        <th>Subtotal (ETB)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <?php
                    while ($row = $cart->fetch_assoc()){
                        $res_id = $row['res_id']; // Get restaurant_id from the first row
                        //strore latitude and longitude value in session
                        $_SESSION['restaurant_latitude'] = $row['lat'];
                        $_SESSION['restaurant_longitude'] = $row['lng'];
                        $_SESSION['restaurant_id'] = $res_id;

                        $total += $row['sub_total']; // Add each item's subtotal to total
                        $qty += $row['quantity']; // Add each item's quantity to total quantity
                        $_SESSION['qty'] = $qty; // Update session variable with total quantity
                        $discount = ($row['menu_price'] * $row['discount'] / 100) * $row['quantity'];
                        $total_discount += $discount;
                        ?>
                        <tr>
                            <td>
                                <img src="../../uploads/menu_images/<?= $row['menu_image']?>" alt="<?= $row['menu_item']?>">
                            </td>
                            <td>
                                <div class="cart_item">
                                    <?= $row['menu_item']?> <i class="fa-solid fa-circle-info"></i>
                                    <div class="detail_info">
                                        <strong style="color:rgb(0, 0, 0); font-size:16px;"> information</strong><br>
                                        this menu is contains `<?= $row['content']?>` <i>from</i> <?= $row['restaurant_name']?> and <?=$row['discount']?> % discount per item .
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="originalQuantity" id="originalQuantity_<?=$row['id']?>" onclick="editQty('<?=$row['id']?>')">
                                    <?=$row['quantity']?>
                                </div>
                                <input class="editableQuantity" type="number" name="quantity" id="editableQuantity_<?=$row['id']?>" value="<?=$row['quantity']?>" data-cart-id="<?=$row['id']?>">
                            </td>
                            <td class="td_price">
                                <?php echo number_format($row['menu_price'], 2);?>
                            </td>
                            <td>
                                <?php echo number_format($row['sub_total'], 2);?>
                            </td>
                            <td>
                                <!--<button class="update_btn"><span class="update_txt">update</span> <i class="update_icon fa-regular fa-pen-to-square"></i></button>-->
                                <button class="remove_btn" data-cart-id="<?=$row['id']?>"><span class="remove_txt">remove</span> <i class="remove_icon fa-solid fa-xmark"></i></button>
                            </td>
                        </tr>
                    <?php
                    }
                }else{?>
                        <div>
                            <img src="../../public/images/empty cart.jpg" alt="empty cart" style="width: 400px; height: 300px; margin: 0 auto; display: block;">
                        </div>
                        <p colspan="6" style="text-align: center; font-size: 20px; color: #ff0000;">
                            Your cart is empty!
                        </p>
            <?php
                }
                    ?>
                </tbody>

                <!--
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                        <td colspan="2" id="total_price" style="font-weight: bold;"><?php echo number_format($total, 2); ?> ETB</td>
                    </tr>
                </tfoot>
                    -->
            </table>
            <button class="add_more_menu" onclick="window.location.href='menu.php?restaurant_id=<?php echo $res_id;?>'">add more menu</button>
        </div>

        <?php if ($cart->num_rows>0):?>
            <!--checkout section -->
            <section class="checkout_section_box" id="checkout_section_box">
                <div class="left_side_checkout_section">
                    <!--checkout form-->
                    <form id="checkoutForm" class="checkout-form" method="POST" autocomplete="on">
                        <h2 id="title_h2">order information</h2>
                        <button id="back_btn">⬅️</button>
                        <!--image-->
                        <div class="pre_checkout_form" id="pre_checkout_form">
                            <img src="../../public/images/checkout order.jpg" alt="checkout">
                        </div>

                        <!--detail info section-->
                        <div id="form_info_section">
                            <h4>please fill the form below to proceed to checkout</h4>
                            <h4>Note: All fields marked with * are required.</h4>
                            <!--contcat related information-->
                            <p>contact info:</p>
                            <div class="checkout_contact_information">
                                <div class="form-group">
                                    <input type="text" name="full_name" id="full_name" placeholder="Chaltu Yohannis *" required><br>
                                    <span class="error" id="error_name"></span>
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email" id="email" placeholder="order@gmail.com *" required><br>
                                    <span class="error" id="error_email"></span>
                                </div>

                                <div class="form-group">
                                    <input type="tel" name="phone" id="phone" placeholder="0987654321 *" required pattern="^[+]?[0-9]{10,15}$" title="Please enter a valid phone number."><br>
                                    <span class="error" id="error_phone"></span>
                                </div>
                            </div>

                            <!-- Delivery Address Information -->
                            <br>
                            <div class="delivery_address">delivery address: <i class="fa-solid fa-circle-info"></i>
                                <div class="detail_info">
                                    <strong style="color:rgb(0, 0, 0); font-size:16px;"> How to select your delivery location:</strong><br>
                                    Drag the red marker to your desired delivery address on the map
                                    Then address, Longitude and Latitude will be automatically filled based on your chosen location.
                                </div>
                            </div>
                            <div class="delivery_address_info">
                                <div class="form-group">
                                    <textarea name="address" id="address" placeholder="e.g., House #12, Arat Kilo, Addis Ababa" required></textarea><br>
                                    <span class="error" id="error_address"></span>
                                </div>
                                <!-- lat and lng Coordinates -->
                                <div class="form-group latandlng">
                                    <div>
                                        <label for="latitude">latitude:</label>
                                        <input type="text" id="latitude" name="latitude"><br>
                                        <span class="error" id="error_latitude"></span>
                                    </div>
                                    <div>
                                        <label for="longitude">Longitude:</label>
                                        <input type="text" id="longitude" name="longitude"><br>
                                        <span class="error" id="error_longitude"></span>
                                    </div>
                                </div>
                                <!-- Map Section -->
                                <div class="form-group">
                                    <div id="map"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="note">Order Note (optional)</label>
                                <textarea name="note" id="note" rows="4" placeholder="Write something what you want to say about your order!"></textarea>
                            </div>
                            <div class="form-group confirm">
                                <div >
                                    <input type="checkbox" id="confirm" style="margin: 0; padding: 0; text-align: left; width: 20px;" required>
                                    <span style="margin: 0;">I confirm the above details are correct. </span><br>
                                    <span class="error" id="error_confirm"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!--payment method section-->
                        <h4>Choose your payment method:</h4>
                        <div id="pament_section" class="hidden_payment_section">
                            <div class="flex_column_payment">
                                <!--screenshot payment method-->
                                <section class="screenshot_section">
                                    <div class="payment_method" id="screenshot_payment_method">
                                        <div class="payment_method_text">
                                            <h2>Screenshot payment method</h2>
                                            <p><img src="../../public/images/screenshot icon.jpg" alt="" width="50px" height="50px"> <span> upload screenshot</span></p>
                                        </div>
                                        <div class="payment_method_radio" id="screenshotchecked">
                                            
                                        </div>
                                    </div>
                                </section>

                                <!-- telebirr payment method-->
                                <section class="telebirr_section" id="telebirr_section">
                                    <div class="payment_method" id="telebirr_payment_method">
                                        <div class="payment_method_text">
                                            <h2>telebirr payment method-</h2>
                                            <p><img src="../../public/images/telebirr icon.jpg" alt="" width="50px" height="50px"> <span>pay with telebirr</span></p>
                                        </div>
                                        <div class="payment_method_radio" id="telebirrchecked">
                                            
                                        </div>
                                    </div>
                                </section>
                                <!-- telebirr payment method-->
                                <section class="paypal_section" id="paypal_section">
                                    <div class="payment_method" id="paypal_payment_method">
                                        <div class="payment_method_text">
                                            <h2>paypal payment method-</h2>
                                            <p><img src="../../public/images/paypal icon.jpg" alt="" width="50px" height="50px"> <span>pay with paypal</span></p>
                                        </div>
                                        <div class="payment_method_radio" id="paypalchecked">
                                            
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <button id="saveBtn">Save</button>
                        </div>

                        <button type="submit" id="submitBtn">Proceed to Checkout</button>
                        <div id="responseMsg" class="response-msg"></div>
                    </form>
                </div>

                <!--cart summary section-->
                <div class="right_side_checkout_section">
                    <div class="detail_section">
                        <?php
                            $delivery_fee =" + delivery fee";
                            $service_fee = $total * 0.05; // 5% service fee
                            $grand_total = round($total + $service_fee - $total_discount, 2);
                            $grand_total = $grand_total.$delivery_fee; // Calculate the grand total
                        ?>
                        <h2>Order Summary</h2>
                        <p>Discount:
                            <span style="color: #11ee22"><?php echo round($total_discount, 2);?>  birr
                            <input type="hidden" id="discount" value="<?php echo round($total_discount, 2);?>">
                        </span>
                        </p>
                        <p>Delivery Fee: <span>calculated by address</span> </p>
                        <p>Service Fee: 
                            <span><?php echo round($service_fee, 2)?> birr
                                <input type="hidden" id="service_fee" value="<?php echo round($service_fee, 2)?>">
                            </span> 
                        </p>
                        <p>Subtotal: 
                            <span><?php echo number_format($total, 2); ?> birr
                            <input type="hidden" id="sub_total" value="<?php echo round($total, 2); ?>">
                            </span> </p>
                        <p>Grand Total: <span id="grand_total"><?php echo $grand_total; ?> </span> </p>
                    </div>
                    <button class="btn btn-checkout" id="btn-checkout">Place order</button>
                    <input type="hidden" id="qqqqty" value="<?=$_SESSION['qty']?>">
                </div>
            </section>
        <?php endif; ?>
        <div class="infoSection">
            <p>for each 1 kilometer (km) of distance between the restaurant and the delivery location, 
            we will charge the customer 30 Ethiopian Birr as a delivery fee.</p>
        </div>
    </div>
    <?php include "footer.php";?>

    <script>
        //edit cart quantity
        function editQty(qty_Id) {
            const editable_qty = document.getElementById(`editableQuantity_${qty_Id}`);
            const orignal_qty = document.getElementById(`originalQuantity_${qty_Id}`);

            // Check if the clicked menu is already open
            const isVisible = editable_qty.style.display === 'block';

            // Hide all menus and reset button icons
            document.querySelectorAll('.editableQuantity').forEach(m => m.style.display = 'none');
            document.querySelectorAll('.originalQuantity').forEach(o => o.style.display = 'block');

            // If it was NOT visible, show it; otherwise, leave it hidden
            if (!isVisible) {
                editable_qty.style.display = 'block';
                orignal_qty.style.display = 'none';
            }else{
                orignal_qty.style.display = 'block';
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Update quantity in cart
            document.querySelectorAll(".editableQuantity").forEach(function (input) {
                input.addEventListener("change", function () {
                    let cartId = this.getAttribute("data-cart-id");
                    let newQuantity = this.value;

                    fetch("update_cart.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `cart_id=${cartId}&quantity=${newQuantity}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            location.reload(); // Reload to update totals
                        } else {
                            alert(data.message);
                        }
                    });
                });
            });

            // Delete item from cart
            document.querySelectorAll(".remove_btn").forEach(function (button) {
                button.addEventListener("click", function () {
                    let cartId = this.getAttribute("data-cart-id");

                    if (confirm("Are you sure you want to remove this item?")) {
                        fetch("delete_cart.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: `cart_id=${cartId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                location.reload(); // Reload after removing item
                            } else {
                                alert(data.message);
                            }
                        });
                    }
                });
            });

            //if cart is empty disable checkout button
            function toggleCheckoutButton() {
                const checkoutButton = document.getElementById("btn-checkout");
                const cartQty = parseInt(document.getElementById("qqqqty").value);

                if (cartQty === 0 || isNaN(cartQty)) {
                    checkoutButton.setAttribute("title", "You must fill the cart to proceed to checkout");
                    checkoutButton.disabled = true;
                    checkoutButton.style.cursor = "not-allowed";
                    checkoutButton.style.backgroundColor = "#ccc";
                } else {
                    checkoutButton.setAttribute("title", "click to checkout"); // remove tooltip when it's no longer needed
                    checkoutButton.disabled = false;
                    checkoutButton.style.cursor = "pointer";
                    checkoutButton.style.backgroundColor = "#007bff";
                }
            }
            toggleCheckoutButton();
                        
        });
    </script>

    <script src="javaScript/handle_customers_location.js"></script>
    <script src="javaScript/checkout_form_controlling.js"></script>
    <script src="javaScript/scroll_up.js"></script>

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.25/dist/sweetalert2.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap" async defer loading="async"></script>
</body>
</html>
