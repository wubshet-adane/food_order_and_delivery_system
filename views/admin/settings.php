<?php
/**
 * Get the total number of orders for a restaurant based on a time range.
 *
 * @param mysqli $conn   The MySQLi connection object.
 * @param int    $rid    Restaurant ID.
 * @param string $type   Time range type: 'daily', 'weekly', 'monthly', or any other for yearly.
 *
 * @return int   Total number of orders.
 */

 $user_id = $_SESSION['user_id'];

// Fetch profile data
$query = "
    SELECT * 
    FROM restaurants 
    WHERE owner_id = ?";
if(!$query){
    die($conn->error);
}
$stmt = $conn->prepare($query);
if(!$stmt){
    die($conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
?>

<h3>Restaurant Settings</h3>

<!-- Profile Picture -->
<div class="settings-section">
    <h3>Change Profile Picture</h3>
    <form action="includes/upload_picture.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="profile_image" required>
        <button type="submit">Upload</button>
    </form>
    <img src="../../<?= 'uploads/user_profiles/' . $profile['image']? $profile['image'] : 'public/images/' . $profile['image'] ?>" width="100">
</div>

<!-- Profile Information -->
<div class="settings-section">
    <h3>Update Profile Information</h3>
    <form action="includes/update_profile.php" method="POST">
        <input type="text" name="name" value="<?= $profile['name'] ?>" placeholder="Restaurant Name" required><br>
        <input type="text" name="contact" value="<?= $profile['phone'] ?>" placeholder="Contact" required><br>
        <textarea name="address"><?= $profile['location'] ?></textarea><br>
        <button type="submit">Update</button>
    </form>
</div>

<!-- Change Password -->
<div class="settings-section">
    <h3>Change Password</h3>
    <form action="includes/change_password.php" method="POST">
        <input type="password" name="old_password" placeholder="Old Password" required><br>
        <input type="password" name="new_password" placeholder="New Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <button type="submit">Change Password</button>
    </form>
</div>

<!-- Analytics -->
<div class="settings-section">
    <h3>Statistics</h3>
    <p><strong>Daily Orders:</strong> <?= getOrderCount($conn, $user_id, 'daily') ?></p>
    <p><strong>Weekly Profit:</strong> $<?= getProfit($conn, $user_id, 'weekly') ?></p>
    <p><strong>Total Restaurants Owned:</strong> <?= getRestaurantCount($conn, $profile['owner_id']) ?></p>
    <p><strong>Top Rated Restaurant:</strong> <?= getTopRatedRestaurant($conn, $profile['owner_id']) ?></p>
</div>

<?php
function getOrderCount($conn, $rid, $type) {
    switch ($type) {
        case 'daily':
            $interval = '1 DAY';
            break;
        case 'weekly':
            $interval = '7 DAY';
            break;
        case 'monthly':
            $interval = '30 DAY';
            break;
        default:
            $interval = '365 DAY';
            break;
    }

    $sql = "SELECT COUNT(*) FROM orders WHERE restaurant_id = ? AND order_date >= NOW() - INTERVAL $interval";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return 0;
    }

    $stmt->bind_param("i", $rid);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0;
    }

    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count ?? 0;
}

function getProfit($conn, $rid, $type) {
    switch ($type) {
        case 'daily':
            $interval = '1 DAY';
            break;
        case 'weekly':
            $interval = '7 DAY';
            break;
        case 'monthly':
            $interval = '30 DAY';
            break;
        default:
            $interval = '365 DAY';
            break;
    }

    $sql = "SELECT SUM(total_price) FROM orders WHERE user_id = ? AND order_date >= NOW() - INTERVAL $interval";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return "0.00";
    }

    $stmt->bind_param("i", $rid);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return "0.00";
    }

    $stmt->bind_result($profit);
    $stmt->fetch();
    $stmt->close();

    return number_format($profit ?? 0, 2);
}


function getRestaurantCount($conn, $owner_id) {
    $sql = "SELECT COUNT(*) FROM restaurants WHERE owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count;
}

function getTopRatedRestaurant($conn, $owner_id) {
    $sql = "SELECT name FROM restaurants WHERE owner_id = ? ORDER BY rating DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    return $name ?: 'N/A';
}
?>
