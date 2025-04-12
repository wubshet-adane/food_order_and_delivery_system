<!-- Top Bar -->
<div class="top-bar">
    <!-- Logo -->
    <div class="logo">
        <a href="home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 FoodOrder" sizes="" srcset=""></a>
    </div>

    <!-- Center Navigation Links -->
    <div class="nav-links">
        <a class="back" href="javascript:back()"><i class="fa-solid fa-backward"></i></a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </div>

    <!-- Authentication Links -->
    <div class="auth-links">
        <?php if(!isset($_SESSION['loggedIn'])){
        ?>
            <a href="../auth/customer_login.php">Login</a>
            <a href="register.php">Sign Up</a>
        <?php }else{?>
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
                <a href="javascript:void(0)" class="profile-dropbtn"><img src="../../public/images/<?php echo $_SESSION['profile_image']?>" alt="profile"></a>
                <div class="profile-dropdown-content">
                    <ul>
                        <li><a href="profile.php"><i class="fa-solid fa-user"></i>&nbsp;&nbsp; Profile</a></li>
                        <li><a href="cart.php"><i class="fa-solid fa-cart-plus"></i>&nbsp;&nbsp; Cart</a></li>
                        <li><a href="order_history.php"><i class="fa-solid fa-bars"></i>&nbsp;&nbsp; Order History</a></li>
                        <li><a href="restaurant_list.php"><i class="fa-solid fa-key"></i>&nbsp;&nbsp; Change password</a></li>
                        <li><a href="restaurant_details_for_customers.php"><i class="fa-solid fa-gear"></i>&nbsp;&nbsp; Account settings</a></li>
                        <li><a href="logout.html"><i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp; Logout</a></li>
                    </ul>
                </div>
            </div>
        <?php }?>            
    </div>
</div>