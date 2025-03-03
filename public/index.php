<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../views/auth/login.php");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
    <p>You are logged in as <strong><?php echo $user['role']; ?></strong>.</p>
    <a href="../routes/web.php?action=logout">Logout</a>
</body>
</html>
