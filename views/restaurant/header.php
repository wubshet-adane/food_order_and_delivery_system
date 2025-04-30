<header class="header_section">
    <div class="container">
        <div class="logo">
            <a href="../customers/home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 Food Order"></a>
        </div>
        
        <nav class="nav_menu">
            <a href="javascript:void(0)" class="notification_icon"><i class="fas fa-bell"></i></a>
            <img  class="profile_image" src="../../public/images/<?= htmlspecialchars($_SESSION['profile_image']) ?>" alt="Profile Image" title="<?= htmlspecialchars($_SESSION['name'])?>">
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
