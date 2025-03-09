<?php
session_start();
require '../../config/database.php'; // your DB connection

//$user_id = $_SESSION['user_id']; // assuming logged-in user

$menu_id = $_GET['menu_id'] ?? null;
$quantity = $_GET['quantity'] ?? 1;

// Fetch cart items from DB
$sql = "SELECT * FROM menu WHERE menu_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $menu_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/cart.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="cart-container">
    <h1>Your Shopping Cart</h1>

    <div class="table_box">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price (ETB)</th>
                    <th>Quantity</th>
                    <th>Subtotal (ETB)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-body">
                <?php
                    $subtotal = $item['price'] * $quantity;
                ?>
                <tr data-id="<?= $item['menu_id'] ?>">
                    <td><img src="../../uploads/menu_images/<?= $item['image'] ?>" alt="men image"></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td>
                        <input type="number" class="quantity" value="<?= $quantity ?>" min="1">
                    </td>
                    <td class="subtotal"><?= number_format($subtotal, 2) ?></td>
                    <td>
                        <button class="btn-remove">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>
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
                <p>Shipping: <span>na</span> </p>
                <p>Grand Total: <span id="total"><?= number_format($subtotal, 2) ?>ETB</span> </p>
            </div>
            <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
        </div>
    </section>
</div>

</body>
</html>
