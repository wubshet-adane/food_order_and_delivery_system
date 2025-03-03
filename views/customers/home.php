<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Uncomment this in production to require login
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
//     header("Location: ../auth/login.php");
//     exit();
// }

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'name';

$sql = "SELECT * FROM restaurants WHERE name LIKE ? ORDER BY $sort ASC LIMIT 7";
$stmt = $conn->prepare($sql);
$searchQuery = "%" . $search . "%";
$stmt->bind_param("s", $searchQuery);
$stmt->execute();
$result = $stmt->get_result();
$restaurants = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Home - Online Food Ordering</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/recommendation.css">
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar">
        <div class="container">
            <h1>FoodieExpress</h1>
            <ul>
                <li>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Guest'); ?></li>
                <li><a href="../../public/index.php">Dashboard</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

   <?php
        include "recommendation/recommendation.php";
   ?>

    <!-- Search & Sort -->
    <section class="search-sort container">
        <form method="GET">
            <input type="text" name="search" placeholder="Search restaurants..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="sort">
                <option value="name" <?php if ($sort === 'name') echo 'selected'; ?>>Sort by Name</option>
                <option value="location" <?php if ($sort === 'location') echo 'selected'; ?>>Sort by Location</option>
            </select>
            <button type="submit">Find</button>
        </form>
    </section>

    <!-- Restaurant Section -->
    <section class="restaurants container">
        <h2>Top Restaurants Near You</h2>
        <?php if ($restaurants): ?>
            <div class="restaurant-grid">
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="restaurant-card">
                        <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['location']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($restaurant['phone']); ?></p>
                        <a href="menu.php?restaurant_id=<?php echo $restaurant['restaurant_id']; ?>" class="btn">View Menu</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No restaurants found. Please try again.</p>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> FoodieExpress. All rights reserved.</p>
    </footer>

</body>
</html>
