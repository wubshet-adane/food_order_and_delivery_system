<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: /views/auth/login.php");
    exit();
}

$restaurant_id = $_GET['restaurant_id'] ?? null;

if ($restaurant_id) {
    // Get restaurant details
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE restaurant_id = ?");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $restaurant = $stmt->get_result()->fetch_assoc();

    // Get menu items for the restaurant
    $stmt = $conn->prepare("SELECT * FROM menu WHERE restaurant_id = ?");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $menu_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: /views/customer/home.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu - <?php echo htmlspecialchars($restaurant['name']); ?></title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <h1>Menu for <?php echo htmlspecialchars($restaurant['name']); ?></h1>
    <p>Location: <?php echo htmlspecialchars($restaurant['location']); ?></p>
    <p>Phone: <?php echo htmlspecialchars($restaurant['phone']); ?></p>

    <h2>Menu</h2>
    <form action="order.php" method="POST">
        <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['restaurant_id']; ?>">
        <ul>
            <?php foreach ($menu_items as $item): ?>
                <li>
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                    <input type="number" name="quantity[<?php echo $item['menu_id']; ?>]" min="1" value="1" style="width: 50px;">
                    <input type="hidden" name="menu_id[]" value="<?php echo $item['menu_id']; ?>">
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="submit">Place Order</button>
    </form>

    <a href="home.php">Back to Restaurant List</a>
</body>
</html>
