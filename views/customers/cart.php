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

    <table>
        <thead>
            <tr>
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

    <div class="total">
        <strong>Total: <span id="total"><?= number_format($subtotal, 2) ?></span> ETB</strong>
    </div>

    <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
</div>

</body>
</html>
