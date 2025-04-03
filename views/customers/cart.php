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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'topbar.php';?>

<div class="cart-container">
    <h1>Your Shopping Cart</h1>
    <h3> it will be deleted after 24 hours, place your order as soon as possible!</h3> 

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
                    $total = 0;
                    $qty = 0;
                    $res_id = $row['res_id'] ?? null; // Get restaurant_id from the first row if available

                    if ($cart->num_rows>0){
                        while ($row = $cart->fetch_assoc()){
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
                                            this menu is contains `<?= $row['content']?>`.
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
                        <tr>
                            <td colspan="6" style="text-align: center; font-size: 20px; color: #ff0000;">
                                Your cart is empty!
                            </td>
                        </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
        <button class="add_more_menu" onclick="window.location.href='menu.php?restaurant_id=<?php echo $res_id;?>'">add more menu</button>
    </div>

    <!--checkout section -->
    <section class="checkout_section_box">
        <div class="left_side_checkout_section">
        
        </div>
        <div class="right_side_checkout_section">

            <div class="detail_section">
                <h2>Order Summary</h2>
                <p>Discount: <span>--ETB</span> </p>
                <p>Delivery Fee: <span>---ETB</span> </p>
                <p>Subtotal: <span>---ETB</span> </p>
                <p>Shipping: <span>NA</span> </p>
                <p>Grand Total: <span id="total"><?php echo number_format($total, 2); ?> ETB</span> </p>
            </div>
            <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
        </div>
    </section>
</div>

    <script>
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
    </script>
<script>
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
    });
</script>


</body>
</html>
