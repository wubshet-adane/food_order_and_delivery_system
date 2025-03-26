<header class="header_section">
    <div class="container">
        <div class="logo">
            <a href="home.php" style="color: white; text-decoration: none;"><img src="../../public/images/logo.jpg" alt="G3 Food Order"></a>
        </div>
        <nav class="nav_menu">
            <ul class="nav_list">
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Portfolio</a></li>
                <li><a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Mobile Menu Button -->
        <div class="menu_toggle" onclick="toggleMenu()">
            ☰
        </div>
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
