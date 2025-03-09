<?php 
require_once '../../config/database.php';
include '../../models/order_to_restaurant.php';

$restaurant_id = $_SESSION['restaurant_id'];
$orders = Order::getOrdersByRestaurant($restaurant_id);
?>

<h2>Order Management</h2>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order) : ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= $order['customer_id'] ?></td>
            <td>$<?= number_format($order['total_price'], 2) ?></td>
            <td><?= $order['status'] ?></td>
            <td>
                <form action="../app/controllers/OrderController.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status">
                        <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $order['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_order_status">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
