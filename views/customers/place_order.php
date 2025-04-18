<?php
session_start();
    require '../../config/database.php';
    require_once __DIR__ . "/../../models/get_delivery_info.php";

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn'])){
        header("Location: ../auth/customer_login.php");
        exit();
    }

    //get restaurant specific lat and lng
    $user_Id = $_SESSION['user_id'];
    $res_lat = $_SESSION['restaurant_latitude'];
    $res_lng = $_SESSION['restaurant_longitude'];
    $res_id = $_SESSION['restaurant_id'];

    //get delivery address information
    $delivery_info = new Delivery_info($conn);
    $delivery = $delivery_info->getDeliveryInfo($user_Id);

    //get selected payment method from redirect url
    $paymnet_method = $_GET['paymentMethod'] ?? null;
    $note = $_GET['orderNote'] ?? null;
    $discount = $_GET['discount'] ?? null;
    $service_fee = $_GET['service_fee'] ?? null;
    $sub_total = $_GET['sub_total'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Place Order</title>
        <link rel="icon" href="../../public/images/logo-icon.png" type="image/png" sizes="16x16">
        <link rel="stylesheet" href="css/topbar.css">
        <link rel="stylesheet" href="css/place_order.css">
        <link rel="stylesheet" href="css/footer.css">
        <!--font awsome-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!--sweetalert js library-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    </head>
<body>
    <header class="header_section">
        <?php include 'topbar.php'; ?>
    </header>
    <?php
        //fetch data from database
    if (!empty($delivery)){

        //distance calculator
        function calculateDistance($lat1, $lon1, $lat2, $lon2) {
            $earthRadius = 6371; // Radius of the Earth in kilometers
        
            // Convert degrees to radians
            $lat1 = deg2rad($lat1);
            $lon1 = deg2rad($lon1);
            $lat2 = deg2rad($lat2);
            $lon2 = deg2rad($lon2);
        
            // Differences
            $deltaLat = $lat2 - $lat1;
            $deltaLon = $lon2 - $lon1;
        
            // Haversine formula
            $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                 cos($lat1) * cos($lat2) *
                 sin($deltaLon / 2) * sin($deltaLon / 2);
        
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;
        
            return $distance; // in kilometers by default
        }

        foreach($delivery as $del):

            $del_lat = $del['latitude'];
            $del_lon = $del['longitude'];
            
            //call funtion
            $distance = calculateDistance($res_lat, $res_lng, $del_lat, $del_lon);
            //calculate delivery_fee
            $delivery_fee = $distance *30;
            //calculate grand total
            $grand_total = (floatVal($sub_total) + floatVal($delivery_fee) + floatVal($service_fee)) - floatVal($discount);
            ?>
            <section class="place_order_container">
                <div class="go_back">
                <a href="menu.php"> menu </a><a href="cart.php"><i class="fas fa-angle-right"></i> cart </a> <a href=""> <i class="fas fa-angle-right"></i> Place order </a>
                </div>
                <div class="delivery_address_section">
                    <!--contact related-->
                    <div class="contact_information">
                        <h2>Your Delivery Contact Information</h2>
                        <p><strong>Full Name:</strong> <span class="delivery_name"><?php echo htmlspecialchars($del['name']);?></span></p>
                        <p><strong>Email:</strong> <span class="checkoutEmail"><?php echo htmlspecialchars($del['email']);?></span></p>
                        <p><strong>Phone:</strong> <span calss="checkoutPhone"><?php echo htmlspecialchars($del['phone']);?></span></p>
                    </div>
                    <!--address related-->
                    <div class="location_section">
                        <h2>Delivery Address order related Information</h2>
                        <p><strong>distance between your delivery address and the restaurant which you are ordering is (absolutly shortest):</strong> <span><?php echo round($distance, 2);?></span> km</p>
                        <p><strong>your prompt about delivery:</strong> <span class="note" id="note"><?php echo htmlspecialchars($note);?></span></p>
                    </div>
                    <!--price related-->
                    <div class="location_section">
                        <h2>payment and total price information</h2>
                        <p><strong>Normal price:</strong>
                            <span id="subtotal"><?php echo round($sub_total, 2);?> </span>birr
                            <i>original price before any discounts or extra fees.</i>
                        </p>
                        <p><strong>service fee:</strong>
                            <span id="service_fee"><?php echo round($service_fee, 2);?> </span>birr
                        </p>
                        <p><strong>discount or Price Reduction:</strong>
                            <span id="discount"><?php echo round($discount, 2);?> </span>birr
                        </p>
                        <p><strong>delivery fee:</strong>
                            <span id="delivery_fee"><?php echo round($delivery_fee, 2);?> </span>birr
                        </p>
                        <p><strong>Total Amount Due:</strong> 
                            <span id="delivery_fee"><?php echo round($grand_total, 2);?> </span>birr
                        </p>
                    </div>
                </div>

                <div class="payment_section">
                    <?php if ($paymnet_method): ?>
                        <div>
                            <h2>Your selected payment method is:</h2>
                            <?php if ($paymnet_method == "telebirr"): ?>
                                <div class="telebirr_payment_method">
                                    <h3>Telebirr ✔️</h3>
                                    <div class="payment_method" id="screenshot_payment_method">
                                            <div class="payment_method_text">
                                                <h2>Telebirr payment method</h2>
                                                <p><img src="../../public/images/telebirr icon.jpg" alt="" width="50px" height="50px"> <span> Pay with telebirr</span></p>
                                            </div>
                                            <div class="payment_method_radio" id="screenshotchecked">
                                                
                                            </div>
                                        </div>
                                </div>
                            <?php else: ?>
                                <div class="screenshot_payment_method">
                                    <h3>Screenshot Payment ✔️</h3>
                                    <!--screenshot payment method-->
                                    <section class="screenshot_section">
                                        <div class="payment_method" id="screenshot_payment_method">
                                            <div class="payment_method_text">
                                                <h3>Screenshot payment method</h3>
                                                <p><img src="../../public/images/screenshot icon.jpg" alt="" width="50px" height="50px"> 
                                                <span> Upload Screenshot bellow</span></p>
                                            </div>
                                            <div class="payment_method_radio" id="screenshotchecked">
                                            <i class="fa-solid fa-circle-check" style="font-size: 32px; color: green;"></i>
                                            </div>
                                        </div>
                                        <!-- screenshot upload section-->
                                        <div class="upload_section" id="upload_section">
                                            <p>pay with the following bank account, telebirr and upload screenshot</p>
                                            <ul>
                                                <li><strong>CBE Account:</strong> <span>G3 food online food ordering sytem, 10000456789</span></li>
                                                <li><strong>Telebirr:</strong>  <span>Esrael-Admasu = 0920466472 / Wubshet-Adane = 0965868933</span></li>
                                                <li><strong>Mpesa:</strong> <span>Esrael Admasu = 0703978283</span></li>
                                            </ul>
                                            <label for="screenshot" class="upload_label">Upload Screenshot Here:</label><br>
                                            <div>
                                                <input type="file" id="screenshot_img" name="screenshot" accept="image/*" capture="camera" required onchange="readURL(this)">
                                                <span><button type="button" id="camera_on_off">camera</button></span>
                                            </div>
                                            <span class="error" id="error_screenshot"></span>
                                            <div id="preview"></div><!--display upload image from file-->
                                            <div class="transactionid">
                                                <input type="text" id="transaction_id_input" class="transaction_id_input" required placeholder="enter transaction id">
                                            </div>
                                            <!--camera settings-->
                                            <div class="camera_section">
                                                 <!-- Video element for the camera preview -->
                                                <video id="video" width="300" height="200" autoplay></video>
                                                <!-- Button to take a picture -->
                                                <button type="button" id="captureButton">Capture</button>
                                                <!-- Canvas element to store the captured image -->
                                                <canvas id="canvas" style="display:none;"></canvas>
                                            </div>



                                            <!--upload screenshot preview-->
                                            <script>
                                                    // Wait for the DOM to be fully loaded
                                                    window.addEventListener('DOMContentLoaded', function () {
                                                        const img = document.getElementById('screenshot_img');
                                                        const on_off = document.getElementById('camera_on_off');

                                                        // Function to preview image as background
                                                        function readURL(input) {
                                                            if (input.files && input.files[0]) {
                                                                var reader = new FileReader();
                                                                reader.onload = function (e) {
                                                                    var preview = document.getElementById('preview');
                                                                    preview.style.backgroundImage = 'url(' + e.target.result + ')';
                                                                    preview.style.opacity = '0'; // for transition
                                                                    setTimeout(function () {
                                                                        preview.style.display = "block";
                                                                        preview.style.opacity = "1";
                                                                    }, 500);
                                                                };
                                                                reader.readAsDataURL(input.files[0]);
                                                            }
                                                        }
                                                        // Attach event listener if needed (or use inline `onchange`)
                                                        img.addEventListener('change', function () {
                                                            readURL(this);
                                                        });


                                                        // Access the user's camera
                                                        on_off.addEventListener('click', function(){
                                                            const camera_section = document.querySelector('.camera_section');
                                                            const video = document.getElementById('video');
                                                            const canvas = document.getElementById('canvas');
                                                            const captureButton = document.getElementById('captureButton');
                                                            const paymentProofInput = document.getElementById('screenshot_img');

                                                            camera_section.style.display = "block";
                                                            
                                                            // Start the video stream from the camera
                                                            navigator.mediaDevices.getUserMedia({ video: true })
                                                            .then(stream => {
                                                                video.srcObject = stream;
                                                            })
                                                            .catch(err => {
                                                                console.error("Error accessing the camera", err);
                                                                Swal.fire({
                                                                    icon: 'error',
                                                                    title: 'Camera Error',
                                                                    text: 'Unable to access the camera.',
                                                                    confirmButtonText: 'OK'
                                                                });
                                                            });

                                                        // Capture the image when the capture button is clicked
                                                        captureButton.addEventListener('click', function() {
                                                            const context = canvas.getContext('2d');
                                                            context.drawImage(video, 0, 0, canvas.width, canvas.height);
                                                            
                                                            // Get the image data
                                                            const dataUrl = canvas.toDataURL('image/png');
                                                            
                                                            // Create a file from the data URL (optional: for uploading)
                                                            const imageFile = dataURLtoFile(dataUrl, 'payment_proof.png');
                                                            
                                                            // Set the captured image to the hidden input (for uploading)
                                                            const fileList = new DataTransfer();
                                                            fileList.items.add(imageFile);
                                                            paymentProofInput.files = fileList.files;
                                                            
                                                            // Display captured image (optional: for preview)
                                                            Swal.fire({
                                                                icon: 'success',
                                                                title: 'Image Captured',
                                                                text: 'You can now proceed with uploading.',
                                                                confirmButtonText: 'OK'
                                                            });
                                                        });

                                                        // Convert data URL to File object
                                                        function dataURLtoFile(dataUrl, filename) {
                                                            const arr = dataUrl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                                                                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                                                            while (n--) u8arr[n] = bstr.charCodeAt(n);
                                                            return new File([u8arr], filename, { type: mime });
                                                        }
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </section>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <input type="hidden" id="cust_id" value="<?php echo $user_Id?>">
                            <input type="hidden" id="res_id" value="<?php echo $res_id?>">
                            <input type="hidden" id="order_total" value="<?php echo round($grand_total, 2);?>">
                            <input type="hidden" id="order_note" value="<?php echo htmlspecialchars($note);?>">
                            <input type="hidden" id="order_payment_method" value="<?php echo $paymnet_method?>">
                        </div>

                        <div class="place_order_section">
                            <button id="place_order_btn">Place Order</button>
                        </div>

                    <?php else: ?>
                        <div class="payment_method_error">
                            <div>
                                <img src="../../public/images/no payment method.jpg" alt="" width="auto" height="auto">
                            </div>
                            <p><strong>Payment method error!</strong></p>
                            <p>Go back to the cart page and select a valid payment method. 
                                <a href="cart.php#submitBtn">Select payment method</a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php
        endforeach;
    }else{
    ?>
    <section>
        <div>
            <p>not delivereviled information</p>

        </div>
    </section>
    <?php
    }
    //footer
    include "footer.php"; 
    ?>

    <script>
        /*
        // Get checkoutData from localStorage
        document.addEventListener("DOMContentLoaded", function () {
            const data = JSON.parse(localStorage.getItem("checkoutData"));

            if (data) {
                document.getElementById("checkoutFullName").innerText = data.full_name || "N/A";
                document.getElementById("checkoutEmail").innerText = data.email || "N/A";
                document.getElementById("checkoutPhone").innerText = data.phone || "N/A";
                document.getElementById("checkoutAddress").innerText = data.delivery_address || "N/A";
                document.getElementById("latitude").innerText = data.latitude || "N/A";
                document.getElementById("longitude").innerText = data.longitude || "N/A";
                
            }

            document.getElementById("place_order_btn")?.addEventListener("click", function () {
                if (data) {
                    alert("Checkout data missing!");
                    return;
                }

                // Confirmation modal
                if (confirm("Are you sure you want to place this order?")) {
                    fetch("ajax/place_order_ajax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            alert("✅ Order placed successfully!");
                            localStorage.removeItem("checkoutData");
                            window.location.href = "my_orders.php";
                        } else {
                            alert("❌ Failed to place order: " + res.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("❌ An error occurred!");
                    });
                }
            });
        });
        */
    </script>
    <!--scroll top btn-->
    <script src="javaScript/scroll_up.js"></script>

    <!--sweetalet message-->
    <script src="javaScript/placeorder.js"></script>
</body>
</html>
