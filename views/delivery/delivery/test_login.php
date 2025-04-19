<?php
session_start();
include '../../config/database.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    
    // Validate the user exists (optional)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = ?");
    $stmt->execute([$user_id, $role]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $user['name'];
        header('Location: Delivery Orders.php');
        exit;
    } else {
        $error = "Invalid user ID or role";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; }
    </style>
</head>
<body>
    <h1>Test Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="user_id">User ID:</label>
            <input type="number" id="user_id" name="user_id" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="delivery">Delivery</option>
                <option value="customer">Customer</option>
                <option value="restaurant">Restaurant</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit">Login</button>
    </form>
    
    <h2>Test Users (from your database):</h2>
    <?php
    $users = $pdo->query("SELECT user_id, name, email, role FROM users")->fetchAll();
    if ($users): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No users found in database.</p>
    <?php endif; ?>
</body>
</html>