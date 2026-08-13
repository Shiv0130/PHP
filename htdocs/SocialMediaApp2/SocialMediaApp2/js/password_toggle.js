// js/password_toggle.js (Updated)

document.addEventListener('DOMContentLoaded', function() {
    const passwordToggleIcons = document.querySelectorAll('.password-toggle-icon');

  
    passwordToggleIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const inputWrapper = this.closest('.password-input-wrapper');
            const passwordInput = inputWrapper.querySelector('input[type="password"], input[type="text"]');

            if (passwordInput) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.textContent = '👁️'; // Open eye icon
                } else {
                    passwordInput.type = 'password';
                    this.textContent = '🚫👁️'; // Closed eye icon
                }
            }
        });
    });


    const registerForm = document.querySelector('form[action="register.php"]');
    const setPasswordForm = document.querySelector('form[action="set_new_password.php"]');
    const passwordStrengthMessage = document.getElementById('passwordStrengthMessage');

    // Function to validate password strength and match
    const validatePasswordFields = (passwordInput, confirmPasswordInput, messageElement) => {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        let errors = [];

        // Password strength check (example: minimum 8 characters, at least one letter, one number, one symbol)
        const minLength = 8;
        const hasLetter = /[a-zA-Z]/;
        const hasNumber = /[0-9]/;
        const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;

        if (password.length < minLength) {
            errors.push(`Password must be at least ${minLength} characters long.`);
        }
        if (!hasLetter.test(password)) {
            errors.push('Password must contain at least one letter.');
        }
        if (!hasNumber.test(password)) {
            errors.push('Password must contain at least one number.');
        }
        if (!hasSymbol.test(password)) {
            errors.push('Password must contain at least one symbol.');
        }

        // Password match check
        if (password !== confirmPassword) {
            errors.push('Passwords do not match.');
        }

        // Display messages
        if (errors.length > 0) {
            messageElement.innerHTML = errors.join('<br>');
            messageElement.style.display = 'block';
            return false; // Validation failed
        } else {
            messageElement.style.display = 'none'; // Hide message if valid
            return true; // Validation passed
        }
    };

    // Apply validation to registration form
    if (registerForm) {
        const passwordInput = registerForm.querySelector('input[name="password"]');
        const confirmPasswordInput = registerForm.querySelector('input[name="confirm_password"]');
        const msgElement = document.getElementById('passwordStrengthMessage'); // This ID needs to exist in register.php

        if (passwordInput && confirmPasswordInput && msgElement) {
            // Validate on input change
            passwordInput.addEventListener('input', () => {
                validatePasswordFields(passwordInput, confirmPasswordInput, msgElement);
            });
            confirmPasswordInput.addEventListener('input', () => {
                validatePasswordFields(passwordInput, confirmPasswordInput, msgElement);
            });

            // Prevent form submission if validation fails
            registerForm.addEventListener('submit', (event) => {
                if (!validatePasswordFields(passwordInput, confirmPasswordInput, msgElement)) {
                    event.preventDefault(); // Stop form submission
                }
            });
        }
    }

    // Apply validation to set new password form
    if (setPasswordForm) {
        const passwordInput = setPasswordForm.querySelector('input[name="new_password"]');
        const confirmPasswordInput = setPasswordForm.querySelector('input[name="confirm_new_password"]');

    }
});