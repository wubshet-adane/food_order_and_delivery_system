<header class="header_section">
    <div class="container">
        <div class="logo">
            <a href="../customers/home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 Food Order"></a>
        </div>
        
        <nav class="nav_menu">
            <a href="javascript:void(0)" class="notification_icon"><i class="fas fa-bell"></i></a>
            <img  class="profile_image" 
                src="<?= htmlspecialchars($_SESSION['profile_image']) ?  '../../uploads/user_profiles/' . htmlspecialchars($_SESSION['profile_image']): '../../public/images/profile icon.jpg'?>" 
                alt="Profile" 
                title="<?= htmlspecialchars($_SESSION['name'])?>" onclick="window.location.href='restaurant_profile_page.php'">
        </nav>
    </div>
     <!--responce_message section-->
     <div class="responce_message" id="responce_message">
        <?php 
        if (isset($_GET['error'])){
            $message = $_GET['error'];
        ?>
            <p class="error" style="color:#fff;"><?php echo $message;?>.</p>
        <?php }
        if (isset($_GET['success'])) {
            $message = $_GET['success'];
        ?>
            <p class="success" style="color:#FFF;"><?php echo $message;?>.</p>
        <?php }?>
    </div>
</header>
<!--script used to toggle the header-->
<script>
    function toggleMenu() {
        document.querySelector(".nav_list").classList.toggle("active");
    }
</script>
