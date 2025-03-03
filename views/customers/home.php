<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

/*if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit();
}*/

// Handle search and sorting
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'name';

// Get list of restaurants
$sql = "SELECT * FROM restaurants WHERE name LIKE ? ORDER BY $sort ASC";
$stmt = $conn->prepare($sql);
$searchQuery = "%" . $search . "%";
$stmt->bind_param("s", $searchQuery);
$stmt->execute();
$result = $stmt->get_result();
$restaurants = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Home</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
    <h2>Find Restaurants</h2>

    <!-- Search and Sort Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search restaurants..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="sort">
            <option value="name" <?php if ($sort === 'name') echo 'selected'; ?>>Sort by Name</option>
            <option value="location" <?php if ($sort === 'location') echo 'selected'; ?>>Sort by Location</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <!-- Restaurant List -->
    <?php if ($restaurants): ?>
        <ul>
            <?php foreach ($restaurants as $restaurant): ?>
                <li>
                    <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                    <p>Location: <?php echo htmlspecialchars($restaurant['location']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($restaurant['phone']); ?></p>
                    <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>">View Menu</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No restaurants found.</p>
    <?php endif; ?>

    <a href="../../public/index.php">Back to Dashboard</a>
</body>
</html>
