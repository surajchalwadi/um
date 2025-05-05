<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

// Check if user is logged in
requireLogin();

// Get the current user's data
$user = getUserById($_SESSION['user_id']);

if (!$user) {
    // If user doesn't exist, redirect to login
    setFlashMessage('error', 'Session expired. Please login again.');
    header("Location: logout.php");
    exit();
}

$errors = [];
$formData = [
    'user_name' => $user['user_name'],
    'user_email' => $user['user_email']
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData['user_name'] = cleanInput($_POST['user_name'] ?? '');
    $formData['user_email'] = cleanInput($_POST['user_email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($formData['user_name'])) {
        $errors['user_name'] = 'Full name is required';
    }
    
    if (empty($formData['user_email'])) {
        $errors['user_email'] = 'Email is required';
    } elseif (!validateEmail($formData['user_email'])) {
        $errors['user_email'] = 'Please enter a valid email address';
    } elseif ($formData['user_email'] !== $user['user_email']) {
        // Check if new email already exists for another user
        $existingUser = getUserByEmail($formData['user_email']);
        if ($existingUser && $existingUser['user_id'] !== $user['user_id']) {
            $errors['user_email'] = 'Email is already registered to another account';
        }
    }
    
    // Validate password only if the user is trying to change it
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        // Get full user data including password
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT user_password FROM user_master WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
        
        // Verify current password
        if (empty($current_password) || !password_verify($current_password, $userData['user_password'])) {
            $errors['current_password'] = 'Current password is incorrect';
        }
        
        if (empty($new_password)) {
            $errors['new_password'] = 'New password is required';
        } elseif (!validatePassword($new_password)) {
            $errors['new_password'] = 'Password must be at least 8 characters with at least 1 uppercase letter and 1 number';
        }
        
        if ($new_password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
    }
    
    // If no errors, update user data
    if (empty($errors)) {
        $conn = getConnection();
        
        // If password is being changed
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user_master SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $formData['user_name'], $formData['user_email'], $hashed_password, $_SESSION['user_id']);
        } else {
            $stmt = $conn->prepare("UPDATE user_master SET user_name = ?, user_email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $formData['user_name'], $formData['user_email'], $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['user_name'] = $formData['user_name'];
            $_SESSION['user_email'] = $formData['user_email'];
            
            // Set success message
            setFlashMessage('success', 'Profile updated successfully!');
            header("Location: dashboard.php");
            exit();
        } else {
            $errors['general'] = 'Failed to update profile. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

include_once 'includes/header.php';
?>

<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Edit Profile</h1>
    
    <?php if (isset($errors['general'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            <p><?php echo $errors['general']; ?></p>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
        <div class="mb-4">
            <label for="user_name" class="block text-gray-700 font-bold mb-2">Full Name</label>
            <input type="text" id="user_name" name="user_name" 
                   class="border rounded w-full p-2" 
                   value="<?php echo $formData['user_name']; ?>" required>
            <div id="name-feedback" class="<?php echo isset($errors['user_name']) ? 'text-red-500' : ''; ?> text-sm mt-1">
                <?php echo $errors['user_name'] ?? ''; ?>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="user_email" class="block text-gray-700 font-bold mb-2">Email Address</label>
            <input type="email" id="user_email" name="user_email" 
                   class="border rounded w-full p-2" 
                   value="<?php echo $formData['user_email']; ?>" required>
            <div id="email-feedback" class="<?php echo isset($errors['user_email']) ? 'text-red-500' : ''; ?> text-sm mt-1">
                <?php echo $errors['user_email'] ?? ''; ?>
            </div>
        </div>
        
        <div class="border-t border-gray-300 pt-4 mb-4">
            <h2 class="text-lg font-bold text-gray-700 mb-4">Change Password (Optional)</h2>
            
            <div class="mb-4">
                <label for="current_password" class="block text-gray-700 font-bold mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" 
                       class="border rounded w-full p-2">
                <div id="current-password-feedback" class="<?php echo isset($errors['current_password']) ? 'text-red-500' : ''; ?> text-sm mt-1">
                    <?php echo $errors['current_password'] ?? ''; ?>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 font-bold mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" 
                        class="border rounded w-full p-2">
                <div id="password-strength" class="h-2 mt-1 rounded"></div>
                <div id="password-feedback" class="<?php echo isset($errors['new_password']) ? 'text-red-500' : ''; ?> text-sm mt-1">
                    <?php echo $errors['new_password'] ?? ''; ?>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                        class="border rounded w-full p-2">
                <div id="confirm-password-feedback" class="<?php echo isset($errors['confirm_password']) ? 'text-red-500' : ''; ?> text-sm mt-1">
                    <?php echo $errors['confirm_password'] ?? ''; ?>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-between">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                Save Changes
            </button>
            <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // For new password validation
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            // Only validate if there's a value
            if (this.value.length > 0) {
                validatePassword(this);
            } else {
                // Reset validation if field is empty (since it's optional)
                resetValidation(this, document.getElementById('password-feedback'));
                resetStrengthMeter();
            }
        });
    }
    
    if (confirmPasswordInput && newPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            // Only validate if there's a value in new password
            if (newPasswordInput.value.length > 0) {
                validateConfirmPassword(this, newPasswordInput.value);
            } else {
                // Reset validation if new password is empty
                resetValidation(this, document.getElementById('confirm-password-feedback'));
            }
        });
    }
    
    function validatePassword(input) {
        const password = input.value;
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
        
        // Update strength meter
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
            showError(input, feedback, feedback_text);
            return false;
        } else {
            showSuccess(input, feedback, 'Strong password');
            return true;
        }
    }
    
    function validateConfirmPassword(input, password) {
        const confirmPassword = input.value;
        const feedback = document.getElementById('confirm-password-feedback');
        
        if (confirmPassword !== password) {
            showError(input, feedback, 'Passwords do not match');
            return false;
        } else {
            showSuccess(input, feedback, 'Passwords match');
            return true;
        }
    }
    
    function resetValidation(input, feedbackElement) {
        if (feedbackElement) {
            feedbackElement.textContent = '';
            feedbackElement.className = 'text-sm mt-1';
        }
        input.className = 'border rounded w-full p-2';
    }
    
    function resetStrengthMeter() {
        const strengthMeter = document.getElementById('password-strength');
        if (strengthMeter) {
            strengthMeter.className = 'h-2 mt-1 rounded';
            strengthMeter.classList.add('w-0');
        }
    }
    
    function showError(input, feedbackElement, message) {
        if (feedbackElement) {
            feedbackElement.textContent = message;
            feedbackElement.className = 'text-red-500 text-sm mt-1';
        }
        input.className = 'border-red-500 focus:border-red-500 focus:ring-red-500 border rounded w-full p-2';
    }
    
    function showSuccess(input, feedbackElement, message) {
        if (feedbackElement) {
            feedbackElement.textContent = message;
            feedbackElement.className = 'text-green-500 text-sm mt-1';
        }
        input.className = 'border-green-500 focus:border-green-500 focus:ring-green-500 border rounded w-full p-2';
    }
});
</script>

<?php
include_once 'includes/footer.php';
?>