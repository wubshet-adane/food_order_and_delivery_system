<?php
session_start();
require '../../config/database.php';
require_once __DIR__ . "/../../models/cart.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn'])){
    header("Location: ../auth/customer_login.php");
    exit();
}

$paymnet_method = $_GET['payment_method']; // Use session or fallback
echo $paymnet_method;
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

    
    </head>
<body>
    <header class="header_section">
        <?php include 'topbar.php'; ?>
    </header>

    <section class="place_order_container">
        <div class="delivery_address_section">
            <div class="contact_information">
                <h2>Your Delivery Contact Information</h2>
                <p><strong>Full Name:</strong> <span id="checkoutFullName">Loading...</span></p>
                <p><strong>Email:</strong> <span id="checkoutEmail">Loading...</span></p>
                <p><strong>Phone:</strong> <span id="checkoutPhone">Loading...</span></p>
                <button id="edit_delivery_contact_information">Edit</button>
            </div>

            <div class="location_section">
                <h2>Delivery Address Information</h2>
                <p><strong>Address:</strong> <span id="checkoutAddress">Loading...</span></p>
                <p><strong>Latitude:</strong> <span id="latitude">Loading...</span></p>
                <p><strong>Longitude:</strong> <span id="longitude">Loading...</span></p>
                <button id="edit_delivery_address">Edit</button>
            </div>
        </div>

        <div class="payment_section">
            <?php if ($paymnet_method): ?>
                <div>
                    <h2>Your selected payment method is:</h2>
                    <?php if ($paymnet_method == "telebirr"): ?>
                        <div class="telebirr_payment_method">
                            <h3>Telebirr ✔️</h3>
                        </div>
                    <?php else: ?>
                        <div class="screenshot_payment_method">
                            <h3>Screenshot Payment ✔️</h3>
                            <!--screenshot payment method-->
                            <section class="screenshot_section">
                                <div class="payment_method" id="screenshot_payment_method">
                                    <div class="payment_method_text">
                                        <h2>Screenshot payment method</h2>
                                        <p><img src="../../public/images/screenshot icon.jpg" alt="" width="50px" height="50px"> <span> upload screenshot</span></p>
                                    </div>
                                    <div class="payment_method_radio" id="screenshotchecked">
                                        
                                    </div>
                                </div>
                                <!-- screenshot upload section-->
                                <div class="upload_section" id="upload_section">
                                    <p>pay with the following bank account, telebirr and upload screenshot</p>
                                    <ul style="list-style-type: none;">
                                        <li><strong>CBE Account:</strong> G3 food online food ordering sytem, 10000456789</li>
                                        <li><strong>Telebirr:</strong>  0912345678</li>
                                        <li><strong>Mpesa</strong> 0789898989</li>
                                    </ul>
                                    <label for="screenshot" class="upload_label">Upload Screenshot:</label><br>
                                    <input type="file" id="screenshot" name="screenshot" accept="image/*" onchange="readURL(this)">
                                    <span class="error" id="error_screenshot"></span>
                                    <div id="preview"></div>
                                    <!--upload screenshot preview-->
                                    <script>
                                        // Wait for the DOM to be fully loaded
                                        window.addEventListener('DOMContentLoaded', function () {
                                            const img = document.getElementById('screenshot');
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
                                                        }, 10);
                                                    };
                                                    reader.readAsDataURL(input.files[0]);
                                                }
                                            }
                                            // Attach event listener if needed (or use inline `onchange`)
                                            img.addEventListener('change', function () {
                                                readURL(this);
                                            });
                                        });
                                    </script>
                                </div>
                            </section>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="place_order_section">
                    <button id="place_order_btn">Place Order</button>
                </div>

            <?php else: ?>
                <div class="payment_method_error">
                    <p><strong>Payment method error!</strong></p>
                    <p>Go back to the cart page and select a valid payment method. 
                        <a href="cart.php#submitBtn">Select payment method</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include "footer.php"; ?>

    <script>
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
            if (!data) {
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

    </script>
    <!--scroll top btn-->
    <script src="javaScript/scroll_up.js"></script> 

</body>
</html>
