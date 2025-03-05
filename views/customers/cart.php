<?php
session_start();

// Dummy cart items for testing (Replace with database data)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        ['id' => 1, 'name' => 'Pizza', 'price' => 12, 'quantity' => 1],
        ['id' => 2, 'name' => 'Burger', 'price' => 8, 'quantity' => 1]
    ];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['quantity'] as $id => $qty) {
            $_SESSION['cart'][$id]['quantity'] = max(1, intval($qty));
        }
    }
    if (isset($_POST['remove'])) {
        $id = $_POST['remove'];
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background: #333; color: white; }
        .cart-container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); }
        .total { text-align: right; font-size: 18px; margin-top: 20px; }
        .btn { padding: 10px; border: none; cursor: pointer; border-radius: 5px; font-size: 14px; }
        .btn-update { background: #007bff; color: white; }
        .btn-remove { background: #dc3545; color: white; }
        .btn-checkout { background: #28a745; color: white; display: block; width: 100%; margin-top: 20px; padding: 10px; text-align: center; }
        .btn:hover { opacity: 0.8; }
        input[type="number"] { width: 50px; text-align: center; }
    </style>
</head>
<body>

<div class="cart-container">
    <h1>Your Shopping Cart</h1>

    <form method="POST" action="cart.php">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price ($)</th>
                    <th>Quantity</th>
                    <th>Subtotal ($)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td>
                        <input type="number" name="quantity[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1">
                    </td>
                    <td><?= number_format($subtotal, 2) ?></td>
                    <td>
                        <button type="submit" name="remove" value="<?= $id ?>" class="btn btn-remove">Remove</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <strong>Total: $<?= number_format($total, 2) ?></strong>
        </div>

        <button type="submit" name="update" class="btn btn-update">Update Cart</button>
    </form>

    <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
</div>

</body>
</html>
