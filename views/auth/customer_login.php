<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Login</title>
    <link rel="stylesheet" href="../customers/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Customers Login</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <p style="color:red;">Invalid email or password.</p>
        <?php endif; ?>

        <form action="../../controllers/customer_login_controller.php?action=login" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p class="register-link">
            Don't have an account? <a href="register.html">Register here</a>
        </p>
    </div>
</body>
</html>
