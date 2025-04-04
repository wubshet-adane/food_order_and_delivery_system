
    document.getElementById("checkoutForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = this;
        const responseMsg = document.getElementById("responseMsg");

        // Collect fields
        const fullName = document.getElementById("full_name").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const address = document.getElementById("address").value.trim();
        const city = document.getElementById("city").value.trim();
        const payment = document.getElementById("payment_method").value;
        const confirm = document.getElementById("confirm").checked;

        let valid = true;

        // Reset previous errors
        document.querySelectorAll('.error').forEach(el => el.textContent = '');

        // Validation
        if (fullName.length < 3) {
            document.getElementById("error_name").textContent = "Full name must be at least 3 characters.";
            valid = false;
        }

        if (!email.match(/^[^ ]+@[^ ]+\.[a-z]{2,}$/)) {
            document.getElementById("error_email").textContent = "Invalid email address.";
            valid = false;
        }

        if (!phone.match(/^\d{10}$/)) {
            document.getElementById("error_phone").textContent = "Phone must be 10 digits.";
            valid = false;
        }

        if (address.length < 5) {
            document.getElementById("error_address").textContent = "Enter full address.";
            valid = false;
        }

        if (!confirm) {
            responseMsg.innerHTML = `<span style="color:red;">You must confirm your details.</span>`;
            valid = false;
        }

        if (!valid) return;

        // Prepare data
        const formData = new FormData(form);

        // Disable button
        document.getElementById("submitBtn").disabled = true;
        responseMsg.innerHTML = "Processing your order...";

        fetch("process_checkout.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(response => {
            responseMsg.innerHTML = `<span style="color:green;">${response}</span>`;
            form.reset();
            document.getElementById("submitBtn").disabled = false;
        })
        .catch(err => {
            responseMsg.innerHTML = `<span style="color:red;">Something went wrong. Please try again.</span>`;
            document.getElementById("submitBtn").disabled = false;
        });
    });
