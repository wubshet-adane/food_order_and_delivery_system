document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deliveryForm');
    const licenseGroup = document.getElementById('license-group');
    const plateGroup = document.getElementById('plate-group');
    const licenseUploadGroup = document.getElementById('license-upload-group');
    const submitBtn = document.getElementById('submit-btn');

    // Show/hide fields based on vehicle type
    vehicleType.addEventListener('change', function() {
        const selectedVehicle = this.value;
        
        if (selectedVehicle === 'motorcycle' || selectedVehicle === 'car') {
            licenseGroup.style.display = 'block';
            plateGroup.style.display = 'block';
            licenseUploadGroup.style.display = 'block';
            
            // Make license fields required
            document.getElementById('license_number').required = true;
            document.getElementById('plate_number').required = true;
        } else {
            licenseGroup.style.display = 'none';
            plateGroup.style.display = 'none';
            licenseUploadGroup.style.display = 'none';
            
            // Remove required attribute
            document.getElementById('license_number').required = false;
            document.getElementById('plate_number').required = false;
        }
    });

    // Form validation
    submitBtn.addEventListener('click', function(event) {
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
            const age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            
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
        
        // Validate license number if shown
        if (licenseGroup.style.display === 'block') {
            const licenseInput = document.getElementById('license_number');
            const licenseError = document.getElementById('license-error');
            if (!licenseInput.value.trim()) {
                licenseError.textContent = 'License number is required';
                licenseInput.focus();
                isValid = false;
            } else {
                licenseError.textContent = '';
            }
        }

        // Validate plate number if shown
        if (plateGroup.style.display === 'block') {
            const plateInput = document.getElementById('plate_number');
            const plateError = document.getElementById('plate-error');
            if (!plateInput.value.trim()) {
                plateError.textContent = 'Plate number is required';
                plateInput.focus();
                isValid = false;
            } else {
                plateError.textContent = '';
            }
        }

        // Validate license upload if shown
        if (licenseUploadGroup.style.display === 'block') {
            const licenseUploadInput = document.getElementById('license_upload');
            const licenseUploadError = document.getElementById('license-upload-error');
            if (!licenseUploadInput.files.length) {
                licenseUploadError.textContent = 'License upload is required';
                licenseUploadInput.focus();
                isValid = false;
            } else {
                licenseUploadError.textContent = '';
            }
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
            return;
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
            return;
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