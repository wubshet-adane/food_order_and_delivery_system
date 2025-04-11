document.addEventListener("DOMContentLoaded", function () {
    const titleH2 = document.getElementById("title_h2");
    const preCheckoutForm = document.getElementById("pre_checkout_form");
    const formInfoSection = document.getElementById("form_info_section");
    const paymentSection = document.getElementById("pament_section");
    const submitBtn = document.getElementById("submitBtn");
    const checkouBtn = document.getElementById("btn-checkout");
    const checkoutForm = document.getElementById("checkoutForm");

    // Initial setup
    formInfoSection.classList.add("form_info_section");
    paymentSection.classList.add("payment_section");
    checkouBtn.disabled = true;
    checkouBtn.style.cursor = "not-allowed";
    checkouBtn.style.backgroundColor = "#ccc";

    // Handle 'Next' click
    submitBtn.addEventListener("click", function (e) {
        e.preventDefault();

        // If formInfoSection is hidden, show it and hide preCheckoutForm
        if (formInfoSection.className.trim() === "form_info_section") {
            preCheckoutForm.style.display = "none";
            formInfoSection.classList.remove("form_info_section");
            formInfoSection.classList.add("visible_sections");
            this.textContent = "next";
            return;
        }

        const error = [];
        // Validate contact & delivery info
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
            document.getElementById("error_name").textContent = ""; // Clear error message
        }        
        const email = document.getElementById("email").value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            error[1] = "Please enter a valid email address.";
            document.getElementById("email").style.borderColor = "red";            
        } else {
            error[1] = null;
            document.getElementById("email").style.borderColor = "green";
            document.getElementById("error_email").textContent = ""; // Clear error message
        }
        const phone = document.getElementById("phone").value.trim();
        const phoneRegex = /^(?:\+251\s?)?\d{3}\s?\d{2}\s?\d{4}$|^\d{10}$/;
        if (!phoneRegex.test(phone)) {
            error[2] = "Please enter a valid phone number.";
            document.getElementById("phone").style.borderColor = "red";
        }
        else {
            error[2] = null;
            document.getElementById("phone").style.borderColor = "green";
            document.getElementById("error_phone").textContent = ""; // Clear error message
        }
        const address = document.getElementById("address").value.trim();
        const latitude = document.getElementById("latitude").value.trim();
        const nonAlphaRegex = /^[+-]?([0-9]*[.])?[0-9]+$/;
        if (!nonAlphaRegex.test(latitude)) {
            error[3] = "Please enter a valid latitude.";
            document.getElementById("latitude").style.borderColor = "red";
        } else {
            error[3] = null;
            document.getElementById("latitude").style.borderColor = "green";    
            document.getElementById("error_latitude").textContent = ""; // Clear error message
        }
        const longitude = document.getElementById("longitude").value.trim();
        const nonAlphaRegex2 = /^[+-]?([0-9]*[.])?[0-9]+$/;
        if (!nonAlphaRegex2.test(longitude)) {
            error[4] = "Please enter a valid longitude.";
            document.getElementById("longitude").style.borderColor = "red";
        } else {
            error[4] = null;
            document.getElementById("longitude").style.borderColor = "green";
            document.getElementById("error_longitude").textContent = ""; // Clear error message
        }
        const confirm = document.getElementById("confirm").checked;
        if (!confirm) {
            error[5] = "Please confirm that you have entered the correct information.";
        } else {
            error[5] = null;
            document.getElementById("confirm").style.borderColor = "green";
            document.getElementById("error_confirm").textContent = ""; // Clear error message
        }

        if (error.some(err => err !== null)) {
            // display error messages
            const errorMessages = [
                "error_name", 
                "error_email", 
                "error_phone", 
                "error_latitude", 
                "error_longitude", 
                "error_confirm"
            ];
            // Loop through the error array and display errors dynamically
            for (let i = 0; i < error.length; i++) {
                if (error[i]) {
                    const errorElement = document.getElementById(errorMessages[i]);
                    errorElement.textContent = error[i];
                }
            }
            return;
        }


        // If all validations pass, proceed to the next section
        formInfoSection.classList.remove("visible_sections");
        formInfoSection.classList.add("form_info_section");
        paymentSection.classList.remove("hidden_payment_section");
        titleH2.textContent = "Payment Information";
        this.textContent = "Save";
        this.style.color = "white";
        this.style.backgroundColor = "#4CAF50"; // Green
        this.id = "saveBtn";
    });
});
