<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Invalid email or password.</p>
    <?php endif; ?>
    <form action="../../controllers/AuthController.php?action=login" method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>
