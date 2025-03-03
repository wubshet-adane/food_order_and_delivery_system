<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'restaurant') {
    header("Location: /views/auth/login.php");
    exit();
}

$restaurant_id = $_SESSION['user']['restaurant_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE restaurant_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
</head>
<body>
    <h1>Incoming Orders</h1>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Update Status</th>
        </tr>
        <?php while($order = $orders->fetch_assoc()): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= $order['total_price'] ?></td>
            <td><?= $order['status'] ?></td>
            <td>
                <form action="/controllers/UpdateOrderStatusController.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <select name="status">
                        <option value="Accepted">Accepted</option>
                        <option value="Preparing">Preparing</option>
                        <option value="Out for Delivery">Out for Delivery</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
