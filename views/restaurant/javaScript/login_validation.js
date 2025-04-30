document.addEventListener("DOMContentLoaded", function () {
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    const loginForm = document.getElementById("loginForm");
    const responseMessage = document.getElementById("responseMessage");
    const emailerr = document.getElementById('emailError');
    const passworderr = document.getElementById('passwordError');

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validatePassword(password) {
        return /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password);
    }

    emailInput.addEventListener("input", function () {
        if (validateEmail(emailInput.value)) {
            emailInput.classList.add("valid");
            emailInput.classList.remove("invalid");
            emailerr.style.display = "none";
        } else {
            emailInput.classList.add("invalid");
            emailInput.classList.remove("valid");
            emailerr.style.display = "block";
        }
    });

    passwordInput.addEventListener("input", function () {
        if (validatePassword(passwordInput.value)) {
            passwordInput.classList.add("valid");
            passwordInput.classList.remove("invalid");
            passworderr.style.display = "none";
        } else {
            passwordInput.classList.add("invalid");
            passwordInput.classList.remove("valid");
            passworderr.style.display = "block";
        }
    });

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!validateEmail(emailInput.value) && !validatePassword(passwordInput.value)) {
            responseMessage.textContent = "Invalid email and password!";
            responseMessage.style.color = "red";
            emailerr.style.display = "none";
            passworderr.style.display = "none";
            return;
        }else if(!validateEmail(emailInput.value) && validatePassword(passwordInput.value)){
            emailerr.textContent = "Invalid email ";
            emailerr.style.color = "red";
            emailerr.style.display = "block";
            responseMessage.style.display = "none";
            passworderr.style.display = "none";
        }else if(validateEmail(emailInput.value) && !validatePassword(passwordInput.value)){
            passworderr.textContent = "Invalid password ";
            passworderr.style.color = "red";
            passworderr.style.display = "block";
            responseMessage.style.display = "none";
            emailerr.style.display = "none";
        }

        let email = emailInput.value;
        let password = passwordInput.value;

        fetch("/food_ordering_system/controllers/restaurant_login_controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success === true && data.message === "Login successful") {
                window.location.href = data.redirect_url;
            } else {
                responseMessage.textContent = data.message;
                responseMessage.style.color = data.success ? "green" : "red";
                passwordInput.value = ""; // Clear password field on error
                emailInput.value = ""; // Clear email field on error
                emailInput.classList.remove("valid");
                passwordInput.classList.remove("valid");
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Show/Hide Password Functionality
    togglePassword.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePassword.classList.remove("fa-eye");
            togglePassword.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            togglePassword.classList.remove("fa-eye-slash");
            togglePassword.classList.add("fa-eye");
        }
    });
});
