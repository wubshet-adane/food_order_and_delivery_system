<header class="header_section">
    <div class="container">
        <div class="logo">
            <h1>🌟 Wubshet & Esrael</h1>
        </div>
        <nav class="nav_menu">
            <ul class="nav_list">
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Portfolio</a></li>
                <li><a href="#">Contact</a></li>
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
                <p style="color:red;"><?php echo $message;?>.</p>
            <?php }
            if (isset($_GET['success'])) {
                $message = $_GET['success'];
            ?>
                <p style="color:green;"><?php echo $message;?>.</p>
            <?php }?>
        </div>
</header>
<!--script used to toggle the header-->
<script>
    function toggleMenu() {
        document.querySelector(".nav_list").classList.toggle("active");
    }
</script>
