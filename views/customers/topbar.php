<!-- Top Bar -->
<div class="top-bar">
    <!-- Logo -->
    <div class="logo">
        <a href="home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 FoodOrder" sizes="" srcset=""></a>
    </div>

    <!-- Center Navigation Links -->
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="menu.php">Menu</a>
    </div>

    <!-- Authentication Links -->
    <div class="auth-links">
        <?php if(!isset($_SESSION['loggedIn'])){
        ?>
            <a href="../auth/customer_login.php">Login</a>
            <a href="register.php">Sign Up</a>
        <?php }else{?>
            <a href="cart.php"><i class="fa-solid fa-cart-plus" style="position: relative">
                <sup style="position: absolute; top: -12px; left: 12px; background: #0f1; color: #111; padding: 3px 2px; font-size: 10px; border-radius: 50%;">
                    <?php 
                    if(isset($_SESSION['qty'])){
                        echo $_SESSION['qty'];
                    }else{
                        echo 0;
                    }?>
                    </sup>
                </i>
            </a>
            <a href="../auth/logout.php">Logout</a>
            <?php }?>
        <button id="darkModeToggle"><i class="fa-solid fa-moon"></i></button>
    </div>
</div>