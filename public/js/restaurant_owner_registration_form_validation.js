document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deliveryForm');
    const submitBtn = document.getElementById('submit-btn');

    // Form validation
    submitBtn.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission for validation
        // Clear previous error messages
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(function(errorElement) {
            errorElement.textContent = '';
        });

        let isValid = true;
        
        // Validate name (only letters and spaces)
        const nameInput = document.getElementById('fullname');
        const nameError = document.getElementById('name-error');
        if (!/^[a-zA-Z\s]+$/.test(nameInput.value.trim())) {
            nameError.textContent = 'Please enter a valid name (letters only)';
            nameInput.focus();
            isValid = false;
        } else {
            nameError.textContent = '';
        }
        
        // Validate email
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
            emailError.textContent = 'Please enter a valid email address';
            emailInput.focus();
            isValid = false;
        } else {
            emailError.textContent = '';
        }
        
        // Validate phone number (basic validation)
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phone-error');
        if (!/^(?:\+251\d{9}|0\d{9})$/.test(phoneInput.value.trim())) {
            phoneError.textContent = 'Please enter a valid phone number (10-15 digits)';
            phoneInput.focus();
            isValid = false;
        } else {
            phoneError.textContent = '';
        }
        
        // Validate date of birth (must be at least 18 years old)
        const dobInput = document.getElementById('dob');
        const dobError = document.getElementById('dob-error');
        if (dobInput.value) {
            const dob = new Date(dobInput.value);
            const today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            if (age < 18) {
                dobError.textContent = 'You must be at least 18 years old';
                isValid = false;
            } else {
                dobError.textContent = '';
            }
        }
        
        // Validate account owner name (only letters and spaces)
        const ownerNameInput = document.getElementById('owner_name');
        const ownerNameError = document.getElementById('owner-error');
        if (!/^[a-zA-Z\s]+$/.test(ownerNameInput.value.trim())) {
            ownerNameError.textContent = 'Please enter a valid name (letters only)';
            ownerNameInput.focus();
            isValid = false;
        } else {
            ownerNameError.textContent = '';
        }

        // Validate bank account number
        const accountInput = document.getElementById('account_number');
        const accountError = document.getElementById('account-error');
        if (!/^\d{9,18}$/.test(accountInput.value.trim())) {
            accountError.textContent = 'Please enter a valid account number (9-18 digits)';
            accountInput.focus();
            isValid = false;
        } else {
            accountError.textContent = '';
        }

        // Enhanced Password Validation
        function validatePassword(passwordInput, passwordError) {
            const password = passwordInput.value;
            const minLength = 8;
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            
            // Clear previous error
            passwordError.textContent = '';
            
            if (password.length < minLength) {
                passwordError.textContent = `Password must be at least ${minLength} characters long`;
                passwordInput.focus();
                return false;
            }
            
            if (!hasLetter) {
                passwordError.textContent = 'Password must contain at least one letter';
                passwordInput.focus();
                return false;
            }
            
            if (!hasNumber) {
                passwordError.textContent = 'Password must contain at least one number';
                passwordInput.focus();
                return false;
            }
            
            // All validations passed
            return true;
        }
        // Usage in your form validation:
        const passwordInput = document.getElementById('password');
        const passwordError = document.getElementById('password-error');
        if (!validatePassword(passwordInput, passwordError)) {
            isValid = false;
        } else{
            passwordError.textContent = '';
        }

        // Validate confirm password
        const confirmPasswordInput = document.getElementById('confirm_password');
        const confirmPasswordError = document.getElementById('confirm-password-error');
        if (confirmPasswordInput.value !== passwordInput.value) {
            confirmPasswordError.textContent = 'Passwords do not match';
            confirmPasswordInput.focus();
            isValid = false;
        } else {
            confirmPasswordError.textContent = '';
        }

        // Validate terms and conditions
        const termsCheckbox = document.getElementById('terms');
        const termsError = document.getElementById('terms-error');
        if (!termsCheckbox.checked) {
            termsError.textContent = 'You must accept the terms and conditions';
            termsCheckbox.focus();
            isValid = false;
        } else {
            termsError.textContent = '';
        }
        
        if (isValid) {
            form.submit();
        }
    });
});