document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".form-login");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const confirm_password = document.getElementById('confirm_password');
    const showPasswordCheckbox = document.getElementById("showPassword");

    form.addEventListener("submit", function (event) {
        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value.trim();

        // Email Validation (Basic)
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(emailValue)) {
            alert("Please enter a valid email address!");
            event.preventDefault(); // Stop form submission
            return;
        }

        // Password Validation
        if (passwordValue.length < 6) {
            alert("Password must be at least 6 characters long!");
            event.preventDefault();
            return;
        }

        if (!/[A-Za-z]/.test(passwordValue) || !/[0-9]/.test(passwordValue)) {
            alert("Password must contain both letters and numbers!");
            event.preventDefault();
            return;
        }
    });

    // Show/Hide Password Functionality
    showPasswordCheckbox.addEventListener("change", function () {
        passwordInput.type = this.checked ? "text" : "password";
        confirm_password.type = this.checked ? "text" : "password";
    });
});
