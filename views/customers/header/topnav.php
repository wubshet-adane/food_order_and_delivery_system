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