document.addEventListener("DOMContentLoaded", function () {
    const titleH2 = document.getElementById("title_h2");
    const backBtn = document.getElementById("back_btn");
    const preCheckoutForm = document.getElementById("pre_checkout_form");
    const formInfoSection = document.getElementById("form_info_section");
    const paymentSection = document.getElementById("pament_section");
    const submitBtn = document.getElementById("submitBtn");
    const checkoutBtn = document.getElementById("btn-checkout");
    const checkoutForm = document.getElementById("checkoutForm");
    
    // Initial setup
    backBtn.style.display = "none";
    formInfoSection.classList.add("form_info_section");
    paymentSection.classList.add("payment_section");
    checkoutBtn.disabled = true;
    checkoutBtn.style.cursor = "not-allowed";
    checkoutBtn.style.backgroundColor = "#ccc";

    // Handle Next/Save click
    submitBtn.addEventListener("click", function (e) {
        e.preventDefault();

        if (formInfoSection.className.trim() === "form_info_section") {
            preCheckoutForm.style.display = "none";
            formInfoSection.classList.remove("form_info_section");
            formInfoSection.classList.add("visible_sections");
            this.textContent = "next";
            return;
        }

        const error = [];

        const name = document.getElementById("full_name").value.trim();
        const regex = /^[A-Za-z]+ [A-Za-z]+$/;
        if (!regex.test(name)) {
            error[0] = "The name must consist of exactly two words.";
            document.getElementById("full_name").style.borderColor = "red";
        } else if (name.length < 5) {
            error[0] = "The name must be at least 5 characters long.";
            document.getElementById("full_name").style.borderColor = "red";
        } else {
            error[0] = null;
            document.getElementById("full_name").style.borderColor = "green";
            document.getElementById("error_name").textContent = "";
        }

        const email = document.getElementById("email").value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            error[1] = "Please enter a valid email address.";
            document.getElementById("email").style.borderColor = "red";
        } else {
            error[1] = null;
            document.getElementById("email").style.borderColor = "green";
            document.getElementById("error_email").textContent = "";
        }

        const phone = document.getElementById("phone").value.trim();
        const phoneRegex = /^(?:\+251\s?)?\d{3}\s?\d{2}\s?\d{4}$|^\d{10}$/;
        if (!phoneRegex.test(phone)) {
            error[2] = "Please enter a valid phone number.";
            document.getElementById("phone").style.borderColor = "red";
        } else {
            error[2] = null;
            document.getElementById("phone").style.borderColor = "green";
            document.getElementById("error_phone").textContent = "";
        }

        const latitude = document.getElementById("latitude").value.trim();
        const nonAlphaRegex = /^[+-]?([0-9]*[.])?[0-9]+$/;
        if (!nonAlphaRegex.test(latitude)) {
            error[3] = "Please enter a valid latitude.";
            document.getElementById("latitude").style.borderColor = "red";
        } else {
            error[3] = null;
            document.getElementById("latitude").style.borderColor = "green";
            document.getElementById("error_latitude").textContent = "";
        }

        const longitude = document.getElementById("longitude").value.trim();
        if (!nonAlphaRegex.test(longitude)) {
            error[4] = "Please enter a valid longitude.";
            document.getElementById("longitude").style.borderColor = "red";
        } else {
            error[4] = null;
            document.getElementById("longitude").style.borderColor = "green";
            document.getElementById("error_longitude").textContent = "";
        }

        const confirm = document.getElementById("confirm").checked;
        if (!confirm) {
            error[5] = "Please confirm that you have entered the correct information.";
            document.getElementById("error_confirm").textContent = error[5];
        } else {
            error[5] = null;
            document.getElementById("confirm").style.outline = "2px solid green";
            document.getElementById("error_confirm").textContent = "";
        }

        // Display error messages
        const errorMessages = [
            "error_name",
            "error_email",
            "error_phone",
            "error_latitude",
            "error_longitude",
            "error_confirm"
        ];
        for (let i = 0; i < error.length; i++) {
            if (error[i]) {
                document.getElementById(errorMessages[i]).textContent = error[i];
            }
        }

        if (error.some(err => err !== null)) return;

        // Proceed to payment section
        formInfoSection.classList.remove("visible_sections");
        formInfoSection.classList.add("form_info_section");
        paymentSection.classList.remove("hidden_payment_section");
        titleH2.textContent = "Payment Information";
        backBtn.style.display = "block";
        this.style.display = "none";
    });

    const screenshot_payment_method = document.getElementById('screenshot_payment_method');
    const telebirr_payment_method = document.getElementById('telebirr_payment_method');
    const screenshotchecked = document.getElementById('screenshotchecked');
    const telebirrchecked = document.getElementById('telebirrchecked');
    const saveBtn = document.getElementById('saveBtn');

    let payment_method = "";

    // Payment method selection
    screenshot_payment_method.addEventListener('click', function () {
        screenshot_payment_method.style.borderLeftWidth = "3px";
        screenshot_payment_method.style.borderLeftStyle = "solid";
        screenshot_payment_method.style.borderLeftColor = "green";
        screenshot_payment_method.style.backgroundColor = "#21883751";
        screenshotchecked.innerHTML = `<i class="fa-solid fa-circle-check"></i>`;
        telebirrchecked.innerHTML = `<i class="fa-regular fa-circle"></i>`;
        telebirr_payment_method.style.border = "none";
        telebirr_payment_method.style.backgroundColor = "#e7f1ff";
        payment_method = "screenshot";
        saveBtn.style.backgroundColor = "#10a";
        saveBtn.textContent = "save";
        saveBtn.disabled = false;
        saveBtn.style.cursor = "pointer";
    });

    telebirr_payment_method.addEventListener('click', function () {
        telebirr_payment_method.style.borderLeftWidth = "3px";
        telebirr_payment_method.style.borderLeftStyle = "solid";
        telebirr_payment_method.style.borderLeftColor = "green";
        telebirr_payment_method.style.backgroundColor = "#21883751";
        telebirrchecked.innerHTML = `<i class="fa-solid fa-circle-check"></i>`;
        screenshotchecked.innerHTML = `<i class="fa-regular fa-circle"></i>`;
        screenshot_payment_method.style.border = "none";
        screenshot_payment_method.style.backgroundColor = "#e7f1ff";
        payment_method = "telebirr";
        saveBtn.style.backgroundColor = "#10a";
        saveBtn.style.color = "#fff";
        saveBtn.textContent = "save";
        saveBtn.disabled = false;
        saveBtn.style.cursor = "pointer";
    });

    // Save button click: validate payment method
    saveBtn.addEventListener('click', function () {
        if (payment_method === "") {
            Swal.fire({
                title: 'Error!',
                text: 'please select payment method first',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        checkoutBtn.disabled = false;
        checkoutBtn.style.cursor = "pointer";
        checkoutBtn.style.backgroundColor = "#10a";
        this.style.backgroundColor = "#fff";
        this.style.color = "#10f343";
        this.textContent = "your information are correctly saved, let's click place order button ↗️↗️↗️";
        this.disabled = true;
        this.style.cursor = "auto";
    });

    //goto back and edit address information
    backBtn.addEventListener('click', function () {
        formInfoSection.classList.add("visible_sections");
        formInfoSection.classList.remove("form_info_section");
        paymentSection.classList.add("hidden_payment_section");
        submitBtn.style.display = "block";
        this.style.display = "none";
    });

    checkoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        // Gather data
        const fullName = document.getElementById("full_name").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const latitude = document.getElementById("latitude").value.trim();
        const longitude = document.getElementById("longitude").value.trim();
        const delivery_address = document.getElementById("address").value.trim(); // Optional
        const note = document.getElementById("note").value.trim(); // Optional
    
        // Construct the data object
        const data = {
            full_name: fullName,
            email: email,
            phone: phone,
            latitude: latitude,
            longitude: longitude,
            delivery_address: delivery_address,
            note: note,
        };
    
        // Save data to localStorage as JSON
        localStorage.setItem("checkoutData", JSON.stringify(data));
    
        // Redirect to place_order.php
        window.location.href = `/food_ordering_system/views/customers/place_order.php?payment_method=${payment_method}`;
    });

});
