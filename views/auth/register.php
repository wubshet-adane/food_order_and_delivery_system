<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <h2>Register</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Registration failed. Try again.</p>
    <?php endif; ?>
    <form action="../../controllers/AuthController.php?action=register" method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role">
            <option value="customer">Customer</option>
            <option value="restaurant">Restaurant Owner</option>
            <option value="delivery">Delivery Person</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
