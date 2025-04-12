
    <?php
        session_start();
        require '../../config/database.php'; // your DB connection
        require_once __DIR__ . "/../../models/cart.php"; 

        //check if user is logged in or not...
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['loggedIn'])){
            header("Location: ../auth/customer_login.php");
            exit();
        }

        $paymnet_method = "";

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="place order page for customers">
        <meta name="keywords" content="place_order, checkout">
        <meta name="author" content="Wubshet Adane">
        <meta name="theme-color" content="#ff9900">
        <meta name="robots" content="index, follow">
        <title>place order</title>
        <link rel="icon" href="../../public/images/logo-icon.png" type="image/gif" sizes="16x16">
        <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="css/topbar.css">

    </head>
    <body>
        <header class="header_section">
            <!--include header section-->
            <?php include 'topbar.php';?>
        </header>
        <!--place order body-->
        <section class="place_order_container">
            <!--address section-->
            <div class="delivery_address_section">
                <div class="contact_information">
                    <h2>your delivery contact information</h2>
                    <p><strong>full name:</strong>Wubshet Adane</p>
                    <p><strong>email:</strong>wwww@gmail.com</p>
                    <p><strong>phone number:</strong>0987654321</p>
                    <button class="edit_delivery_contact_information" id="edit_delivery_contact_information">edit</button>
                </div>
                <div class="location_section">
                    <h2>delivery address informatin</h2>
                    <p><strong>adreess:</strong>addis ababa bole brass </p>
                    <button class="edit_delivery_address" id="edit_delivery_address">edit</button>
                </div>
            </div>

            <!--payment section-->
            <div class="payment_section">
                <!--check if there is paymanet_method or not-->
                <?php if ($paymnet_method):?>
                    <div>
                        <h2>your selected payment method is:</h2>
                    </div>
                    <!--display based on selected payment mewthod-->
                    <?php if($paymnet_method == "telebirr"):?>
                        <!--when payment method you were selected is telebirr-->
                        <div class="telebirr_payment_method">
                            <h2>official payment webservice</h2>
                            <div>
                                <p>telebirr ✔️</p>
                            </div>
                        </div>
                    <?php else:?>
                        <!--when payment method you were selected is screenshot-->
                        <div class="screenshot_payment_method">
                            <h2>it takes a few time for payment authorization!</h2>
                            <div>
                                <p>screenshot payment method ✔️</p>
                            </div>
                        </div>
                    <?php endif;?>
                    <div class="place_order_section">
                        <button class="place_order_btn" id="place_order_btn">place order</button>
                    </div>

                <!--if it doesnt get correct payment method redirceted with checkout information-->
                <?php else:?>
                    <div class="payment_method_error">
                        <div>
                            <p>Payment method error!</p>
                        </div>
                        <p>go back to cart page and select select correct payment method! <a href="cart.php#submitBtn">select payment method</a> </p>                   
                    </div>
                <?php endif;?>
            </div>
        </section>

        <!--footer section-->
        <?php include "footer.php";?>
    </body>
    </html>