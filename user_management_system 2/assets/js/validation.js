document.addEventListener('DOMContentLoaded', function() {
    // Get all required form elements
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"]');
    const nameInput = document.getElementById('user_name');
    const emailInput = document.getElementById('user_email');
    const passwordInput = document.getElementById('user_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    // Add event listeners if the form exists
    if (form) {
        // Check if fields exist before adding event listeners
        if (nameInput) {
            nameInput.addEventListener('input', validateName);
        }
        
        if (emailInput) {
            emailInput.addEventListener('input', validateEmail);
        }
        
        if (passwordInput) {
            passwordInput.addEventListener('input', validatePassword);
        }
        
        if (confirmPasswordInput && passwordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                validateConfirmPassword(passwordInput.value);
            });
        }
        
        // Validate the entire form on submit
        form.addEventListener('submit', validateForm);
    }
    
    // Validation functions
    function validateName() {
        const name = nameInput.value.trim();
        const feedback = document.getElementById('name-feedback');
        
        if (name.length < 2) {
            showError(nameInput, feedback, 'Name must be at least 2 characters');
            return false;
        } else {
            showSuccess(nameInput, feedback, 'Valid name');
            return true;
        }
    }
    
    function validateEmail() {
        const email = emailInput.value.trim();
        const feedback = document.getElementById('email-feedback');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            showError(emailInput, feedback, 'Please enter a valid email address');
            return false;
        } else {
            showSuccess(emailInput, feedback, 'Valid email format');
            return true;
        }
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        const feedback = document.getElementById('password-feedback');
        const strengthMeter = document.getElementById('password-strength');
        
        // Check password strength
        let strength = 0;
        let feedback_text = '';
        
        // Length check
        if (password.length < 8) {
            feedback_text = 'Password must be at least 8 characters';
        } else {
            strength += 1;
        }
        
        // Uppercase check
        if (!/[A-Z]/.test(password)) {
            feedback_text = feedback_text || 'Password must contain at least one uppercase letter';
        } else {
            strength += 1;
        }
        
        // Number check
        if (!/[0-9]/.test(password)) {
            feedback_text = feedback_text || 'Password must contain at least one number';
        } else {
            strength += 1;
        }
        
        // Update strength meter if it exists
        if (strengthMeter) {
            // Reset classes
            strengthMeter.className = 'h-2 mt-1 rounded';
            
            if (strength === 0) {
                strengthMeter.classList.add('bg-red-500', 'w-1/4');
            } else if (strength === 1) {
                strengthMeter.classList.add('bg-orange-500', 'w-2/4');
            } else if (strength === 2) {
                strengthMeter.classList.add('bg-yellow-500', 'w-3/4');
            } else {
                strengthMeter.classList.add('bg-green-500', 'w-full');
            }
        }
        
        if (strength < 3) {
            showError(passwordInput, feedback, feedback_text);
            return false;
        } else {
            showSuccess(passwordInput, feedback, 'Strong password');
            
            // If confirm password exists, validate it too
            if (confirmPasswordInput) {
                validateConfirmPassword(password);
            }
            return true;
        }
    }
    
    function validateConfirmPassword(password) {
        const confirmPassword = confirmPasswordInput.value;
        const feedback = document.getElementById('confirm-password-feedback');
        
        if (confirmPassword !== password) {
            showError(confirmPasswordInput, feedback, 'Passwords do not match');
            return false;
        } else {
            showSuccess(confirmPasswordInput, feedback, 'Passwords match');
            return true;
        }
    }
    
    function validateForm(e) {
        let isValid = true;
        
        // Validate each field if it exists
        if (nameInput) {
            isValid = validateName() && isValid;
        }
        
        if (emailInput) {
            isValid = validateEmail() && isValid;
        }
        
        if (passwordInput) {
            isValid = validatePassword() && isValid;
        }
        
        if (confirmPasswordInput && passwordInput) {
            isValid = validateConfirmPassword(passwordInput.value) && isValid;
        }
        
        // If not valid, prevent form submission
        if (!isValid) {
            e.preventDefault();
        }
    }
    
    // Helper functions for showing validation feedback
    function showError(input, feedbackElement, message) {
        if (feedbackElement) {
            feedbackElement.textContent = message;
            feedbackElement.className = 'text-red-500 text-sm mt-1';
        }
        input.className = 'border-red-500 focus:border-red-500 focus:ring-red-500 border rounded w-full p-2';
        updateSubmitButton();
    }
    
    function showSuccess(input, feedbackElement, message) {
        if (feedbackElement) {
            feedbackElement.textContent = message;
            feedbackElement.className = 'text-green-500 text-sm mt-1';
        }
        input.className = 'border-green-500 focus:border-green-500 focus:ring-green-500 border rounded w-full p-2';
        updateSubmitButton();
    }
    
    function updateSubmitButton() {
        if (!submitBtn) return;
        
        let isValid = true;
        
        // Check if all feedback elements show success
        document.querySelectorAll('[id$="-feedback"]').forEach(element => {
            if (element.className.includes('text-red-500') || element.textContent === '') {
                isValid = false;
            }
        });
        
        // Enable or disable submit button
        if (isValid) {
            submitBtn.disabled = false;
            submitBtn.className = 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'bg-gray-400 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline cursor-not-allowed';
        }
    }
});