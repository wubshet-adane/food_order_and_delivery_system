<?php
require_once '../../config/database.php';


if (isset($_GET['id'])) {
    $restaurant_id = intval($_GET['id']);
    $query = "SELECT * FROM restaurants WHERE restaurant_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $restaurant = $result->fetch_assoc();
        echo json_encode($restaurant);
    } else {
        echo json_encode(['error' => 'Restaurant not found']);
    }

    exit;
}


if (!isset($_GET['owner_id'])) {
    die('<p class="error-message">Owner ID is required</p>');
}

$owner_id = intval($_GET['owner_id']);
$query = "SELECT * FROM restaurants WHERE owner_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <div class="restaurants-table-container">
        <table class="restaurants-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Confirmation</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($restaurant = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $restaurant['restaurant_id'] ?></td>
                        <td style="position: relative;">
                            <div class="restaurant-name">
                                <?php if ($restaurant['image']): ?>
                                    <img src="../restaurant/restaurantAsset/<?= htmlspecialchars($restaurant['image']) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>" class="restaurant-thumbnail">
                                <?php endif; ?>
                                <?= htmlspecialchars($restaurant['name']) ?>
                            </div>
                            <span class="status-badge status-<?= $restaurant['status'] ?>">
                                <?= ucfirst($restaurant['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(substr($restaurant['location'], 0, 30)) ?>...</td>
                        <td><?= htmlspecialchars($restaurant['phone']) ?></td>
                        <td>
                            <span class="status-<?= $restaurant['confirmation_status'] ?>">
                                <?= ucfirst($restaurant['confirmation_status'] ?? 'pending')?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($restaurant['created_at'])) ?></td>
                        <td class="actions">
                            <?php if ($restaurant['confirmation_status'] != 'approved'): ?>
                                <form method="POST" action="dashboard.php?page=manage_restaurants" class="action-form">
                                    <input type="hidden" name="restaurant_id" value="<?= $restaurant['restaurant_id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-approve">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($restaurant['confirmation_status'] != 'rejected'): ?>
                                <form method="POST" action="dashboard.php?page=manage_restaurants" class="action-form">
                                    <input type="hidden" name="restaurant_id" value="<?= $restaurant['restaurant_id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-reject">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                </form>
                            <?php endif; ?>
                            <!-- In your table row, update the View button to trigger the modal -->
                            <a href="javascript:void()" class="btn-view" onclick="showRestaurantModal(<?= $restaurant['restaurant_id'] ?>)">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="no-restaurants">This owner has no restaurants registered yet.</p>
<?php endif; ?>