<!-- Top Bar -->
<div class="top-bar">
    <!-- Logo -->
    <div class="logo">
        <a href="home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 FoodOrder" sizes="" srcset=""></a>
    </div>

    <!-- Center Navigation Links -->
    <div class="nav-links">
        <!--<a class="back" href="javascript:history.back()"><i class="fa-solid fa-backward"></i></a>-->
        <a href="home.php">Home</a>
        <a href="/../food_ordering_system/views/customers/home.php">Resrtaurants</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </div>

    <!-- Authentication Links -->
    <div class="auth-links">
        <?php if(!isset($_SESSION['loggedIn'])){
        ?>
            <div class="login_box">
                <a class="login" href="../auth/customer_login.php" title="continue as customer">Login</a>
            </div>
            <div class="sign_up_box">
                <a class="signup" href="register.php" title="continue as customer">SignUp</a>
            </div>
            <div class="login_box">
                <a class="login" href="../auth/restaurant_login.php" title="continue as restaurant">Login as Restaurant</a>
            </div>
        <?php }else{?>
            <div class="notification-dropdown">
                <a href="javascript:void(0)" class="notification-dropbtn"><i class="fa-solid fa-bell"></i></a>
                <div class="notification-dropdown-content">
                    <ul>
                        <li><a href="#">Notification 1</a></li>
                        <li><a href="#">Notification 2</a></li>
                        <li><a href="#">Notification 3</a></li>
                    </ul>
                </div>
            </div>
            <div>
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
            </div>
                
            <!-- Dark Mode Toggle Button -->
            <button id="darkModeToggle"><i class="fa-solid fa-moon"></i></button>   

            <div class="profile-dropdown">
                <a href="javascript:void(0)" class="profile-dropbtn"><img src="<?php echo !empty($_SESSION['profile_image']) ? '../../uploads/user_profiles/' . $_SESSION['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['name']) . '&size=200';?>" alt="<?=$_SESSION['name']?>"></a>
                <div class="profile-dropdown-content">
                    <ul>
                        <p style="text-align: center;">
                            <img style = "border: 2px dotted black;  border-radius: 50%;" src="<?php echo !empty($_SESSION['profile_image']) ? '../../uploads/user_profiles/' . $_SESSION['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['name']) . '&size=200';?>" alt="<?=$_SESSION['name']?>" width = "80px" height = "80px">
                            <p style="font-size: 20px; font-weight: bold; color: black; text-align: center; text-transform: capitalize;"><?=$_SESSION['name']?></p>
                        </p>
                        <li><a href="customer_profile_page.php"><i class="fa-solid fa-user"></i>&nbsp;&nbsp; Profile</a></li>
                        <li><a href="cart.php"><i class="fa-solid fa-cart-plus"></i>&nbsp;&nbsp; Cart</a></li>
                        <li><a href="order_history.php"><i class="fa-solid fa-bars"></i>&nbsp;&nbsp; Order History</a></li>
                        <li><a href="profile.php"><i class="fa-solid fa-key"></i>&nbsp;&nbsp; Change password</a></li>
                        <li><a href="account.php"><i class="fa-solid fa-gear"></i>&nbsp;&nbsp; Account settings</a></li>
                        <li><a href="logout.html"><i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp; Logout</a></li>
                    </ul>
                </div>
            </div>
        <?php }?>            
    </div>
</div>