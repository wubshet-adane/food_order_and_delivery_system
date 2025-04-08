<?php
session_start();
require '../../config/database.php'; // your DB connection
require_once __DIR__ . "/../../models/cart.php";

//check if user is logged in or not...
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn'])){
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
    
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/checkout.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Optional: Mobile-first layout styling -->
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
        $_SESSION['qty'] = 0; // Initialize session variable for quantity
        $res_id = 0; // Get restaurant_id from the first row if available

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
                                $total += $row['sub_total']; // Add each item's subtotal to total
                                $qty += $row['quantity']; // Add each item's quantity to total quantity
                                $_SESSION['qty'] = $qty; // Update session variable with total quantity
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
                                                this menu is contains `<?= $row['content']?>` <i>from</i> <?= $row['restaurant_name']?>.
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

        <!--checkout section -->
        <section class="checkout_section_box">
            <div class="left_side_checkout_section">
                
                <form id="checkoutForm" class="checkout-form" autocomplete="off">
                    <h2>Checkout</h2>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" id="full_name" required>
                        <span class="error" id="error_name"></span>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" id="email" required>
                        <span class="error" id="error_email"></span>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" id="phone" required pattern="^\d{10}$">
                        <span class="error" id="error_phone"></span>
                    </div>

                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" id="address" required></textarea>
                        <span class="error" id="error_address"></span>
                    </div>

                    <div class="form-group">
                        <label>Confirm Your Location on Map</label>
                        <div id="map" style="height: 250px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"></div>
                    </div>

                    <!-- Hidden coords -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">

                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" id="city" required>
                    </div>

                    <div class="form-group">
                        <label>Order Note (optional)</label>
                        <textarea name="note" id="note"></textarea>
                    </div>

                    <div class="form-group confirm">
                        <label>
                        <input type="checkbox" id="confirm" required>
                        I confirm the above details are correct.
                        </label>
                    </div>

                    <button type="submit" id="submitBtn">Place Order</button>
                    <div id="responseMsg" class="response-msg"></div>
                </form>
                
                    <!--payment method section-->
                <div class="payment_section">
                    <h2>Payment Method</h2>
                    <div class="payment_method">
                        <input type="radio" id="cash" name="payment_method" value="cash" checked>
                        <label for="cash">Cash on Delivery</label>
                    </div>
                    <div class="payment_method">
                        <input type="radio" id="credit_card" name="payment_method" value="credit_card">
                        <label for="credit_card">Credit Card</label>
                    </div>
                    <div class="payment_method">
                        <input type="radio" id="mobile_money" name="payment_method" value="mobile_money">
                        <label for="mobile_money">Mobile Money</label>
                    </div>
                    <div class="payment_method">
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                        <label for="bank_transfer">Bank Transfer</label>
                    </div>
                    <div class="payment_method">
                        <input type="radio" id="paypal" name="payment_method" value="paypal">
                        <label for="paypal">PayPal</label>
                    </div>
                    <div class="payment_method">
                        <input type="radio" id="screenshot" name="payment_method" value="screenshot">
                        <label for="screenshot">Screenshot</label>
                    </div>
                </div>
            </div>

            <!--cart summary section-->
            <div class="right_side_checkout_section">
                <div class="detail_section">
                    <h2>Order Summary</h2>
                    <p>Discount: <span>--ETB</span> </p>
                    <p>Delivery Fee: <span>---ETB</span> </p>
                    <p>Subtotal: <span>---ETB</span> </p>
                    <p>Shipping: <span>NA</span> </p>
                    <p>Grand Total: <span id="total"><?php echo number_format($total, 2); ?> ETB</span> </p>
                </div>
                <button onclick = "window.location.href='checkout.php'" class="btn btn-checkout" id="btn-checkout">Proceed to Checkout</button>
                <input type="hidden" id="qqqqty" value="<?=$_SESSION['qty']?>">
            </div>
        </section>
    </div>

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
    <script src="javaScript/checkoutInfoVlidation_AJAX.js"></script>
</body>
</html>
