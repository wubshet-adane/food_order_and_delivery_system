<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Restaurant</title>
    <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="css/restaurant_register.css">
     <!--font ausome for star rating-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
</head>
<body>

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
    <div class="container">
        <h2>Register Your Restaurant</h2>
        <button id="darkModeToggle">🌙</button>

        <form action="../../controllers/restaurant_register_form_controller.php?action=register" method="POST" enctype="multipart/form-data">
            <!-- Basic Details -->
            <fieldset>
                <legend>Basic Information</legend>
                <div class="allImage">
                    <div class="primary-info">
                        <label for="name"><i class="fa-solid fa-utensils"></i> Restaurant Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter restaurant name" required>
                    </div>
                    <div class="primary-info">
                        <label for="phone"><i class="fa-solid fa-phone"> </i> Phone</label>
                        <input type="text" name="phone" id="phone" placeholder="Enter phone number" required>
                    </div>
                </div>
            </fieldset>

            <!-- Contact & Social Media -->
            <fieldset>
                <legend>Contact & Social Media</legend>
                <div class="allImage">
                    <div class="socialMedia">
                        <label for="tiktok"><i class="fa-brands fa-tiktok"> </i> TikTok Account</label>
                        <input type="text" name="tiktok" id="tiktok" placeholder="TikTok link">
                    </div>
                    <div class="socialMedia">
                        <label for="telegram"><i class="fa-brands fa-telegram"> </i> Telegram Account</label>
                        <input type="text" name="telegram" id="telegram" placeholder="Telegram link">
                    </div>
                    <div class="socialMedia">
                        <label for="instagram"><i class="fa-brands fa-instagram"> </i> Instagram link</label>
                        <input type="text" name="instagram" id="instagram" placeholder="Instagram account">
                    </div>
                    <div class="socialMedia">
                        <label for="facebook"><i class="fa-brands fa-facebook"> </i> Facebook link</label>
                        <input type="text" name="facebook" id="facebook" placeholder="facebook account">
                    </div>
                    <div class="socialMedia">
                        <label for="website"><i class="fa-solid fa-globe"> </i> Your Restaurant Website</label>
                        <input type="text" name="website" id="website" placeholder="website URL">
                    </div>
                </div>
            </fieldset>

            <!-- Images Upload -->
            <fieldset>
                <legend>Upload Images</legend>
                <div class="allImage">
                    <div class="fileWithPreview">
                        <label for="imageInput">Restaurant Image <i class="fa-solid fa-circle-info"></i></label>
                        <input type="file" name="image" id="imageInput" required>
                        <img id="imagePreview" class="preview-image">
                    </div>
                    <div class="fileWithPreview">
                        <label for="bannerInput">Banner Image <i class="fa-solid fa-circle-info"></i></label>
                        <input type="file" name="banner" id="bannerInput" required>
                        <img id="bannerPreview" class="preview-image">
                    </div>
                    <div class="fileWithPreview">
                        <label for="licenseInput">Restaurant License Image <i class="fa-solid fa-circle-info"></i></label>
                        <input type="file" name="license" id="licenseInput" required>
                        <img id="licensePreview" class="preview-image">
                    </div>
                </div>
            </fieldset>

            <!-- Location & Map -->
            <fieldset>
                <legend>Location</legend>
                <!--upload location-->
                <div class="allImage">
                    <div class="location">
                        <label for="location">Location  <i class="fa-solid fa-circle-info"></i></label>
                        <input type="text" name="location" id="location" placeholder="Enter location" required>
                    </div>
                </div>

                <!--upload longitude and latitude from map-->
                <div class="allImage">
                    <div class="map">
                        <div class="row">
                            <div>
                                <label for="latitude">Latitude</label>
                                <input type="text" name="latitude" id="latitude" required>
                            </div>
                            <div>
                                <label for="longitude">Longitude</label>
                                <input type="text" name="longitude" id="longitude" required>
                            </div>
                        </div>
                        <div id="google-map"></div>
                    </div>
                </div>
            </fieldset>

            <!--working time-->
            <fieldset>
                <legend>Operating Hours</legend>
                <div class="allImage">
                    <div class="working-time">
                        <label for="working-time">Working Time Description <i class="fa-solid fa-circle-info"></i></label>
                        <textarea name="opening_time" id="working-time" rows="3" placeholder="Enter working time details"></textarea>
                    </div>
                </div>
            </fieldset>

            <!--detail description about restaurant-->
            <fieldset>
                <legend>detail description about restaurant</legend>
                <div class="allImage">
                    <div class="working-time">
                        <label for="detail-description">detail description about restaurant <i class="fa-solid fa-circle-info"></i></label>
                        <textarea name="detail-description" id="detail-description" rows="5" placeholder="Enter detailed description"></textarea>
              </div>
                </div>
            </fieldset>

            <!-- Status & Preferences -->
            <fieldset>
                <legend>Preferences & Availability</legend>
                <div class="allImage">
                    <div class="status">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <button type="submit">Register Restaurant</button>

        </form>
    </div>

    <script>
        /*dark mode functionality
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
        });*/

        // File preview functionality
        document.getElementById('imageInput').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // File preview functionality
        document.getElementById('bannerInput').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('bannerPreview').src = e.target.result;
                    document.getElementById('bannerPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // File preview functionality
        document.getElementById('licenseInput').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('licensePreview').src = e.target.result;
                    document.getElementById('licensePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Google Maps Integration
        function initMap() {
            const map = new google.maps.Map(document.getElementById("google-map"), {
                center: { lat: 10.35, lng: 37.73333 },
                zoom: 8,
            });

            const marker = new google.maps.Marker({
                position: { lat: 10.35, lng: 37.73333 },
                map: map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function(event) {
                document.getElementById('latitude').value = event.latLng.lat();
                document.getElementById('longitude').value = event.latLng.lng();
            });
        }

        // Load Google Maps API
        function loadMapScript() {
            const script = document.createElement("script");
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap`;
            script.async = true;
            script.defer = true;
            document.body.appendChild(script);
        }

        loadMapScript();
    </script>
    <script src="../customers/javaScript/light_and_dark_mode.js"></script>

</body>
</html>
