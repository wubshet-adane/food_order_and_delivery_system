<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <style>
    body {
      background: #f2f2f2;
      font-family: Arial, sans-serif;
    }
    .login-container {
      width: 100%;
      max-width: 400px;
      margin: 80px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
    }
    .login-container img {
        display: block;
        margin: 0 auto 20px auto; /* center the image and add bottom space */
        height: 60px; /* or adjust as needed */
        max-width: 100%; /* responsive */
        object-fit: contain; /* keeps image proportionate */
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    .login-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ddd;
    }
    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: #ff9900;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2><img src="../../public/images/logo-icon.png" alt=""></h2>
    <h2>Admin Login</h2>
    <?php if ($_GET['error']): $error = $_GET['error'] ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="../../controllers/admin_login_controller.php">
      <input type="email" name="email" placeholder="email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
