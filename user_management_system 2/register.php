<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$formData = [
    'user_name' => '',
    'user_email' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData['user_name'] = cleanInput($_POST['user_name'] ?? '');
    $formData['user_email'] = cleanInput($_POST['user_email'] ?? '');
    $password = $_POST['user_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($formData['user_name'])) {
        $errors['user_name'] = 'Full name is required';
    }
    
    if (empty($formData['user_email'])) {
        $errors['user_email'] = 'Email is required';
    } elseif (!validateEmail($formData['user_email'])) {
        $errors['user_email'] = 'Please enter a valid email address';
    } else {
        // Check if email already exists
        $user = getUserByEmail($formData['user_email']);
        if ($user) {
            $errors['user_email'] = 'Email is already registered';
        }
    }
    
    if (empty($password)) {
        $errors['user_password'] = 'Password is required';
    } elseif (!validatePassword($password)) {
        $errors['user_password'] = 'Password must be at least 8 characters with at least 1 uppercase letter and 1 number';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // If no errors, insert user into database
    if (empty($errors)) {
        $conn = getConnection();
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO user_master (user_name, user_email, user_password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $formData['user_name'], $formData['user_email'], $hashed_password);
        
        if ($stmt->execute()) {
            // Success
            setFlashMessage('success', 'Registration successful! You can now log in.');
            header("Location: login.php");
            exit();
        } else {
            // Error
            $errors['general'] = 'Registration failed. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

$pageTitle = "Create an Account";
include_once 'includes/header.php';
?>

<!-- Modern CSS styles -->
<style>
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(-45deg, #4f46e5, #6366f1, #818cf8, #93c5fd);
    background-size: 400% 400%;
    animation: gradient 15s ease infinite;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    pointer-events: none;
}
.auth-container {
    animation: fadeIn 0.8s ease-out;
}
.auth-card {
    position: relative;
    backdrop-filter: blur(8px);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15),
    inset 0 0 0 1px rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.18);
    animation: float 6s ease-in-out infinite;
    border-radius: 1rem;
}
.auth-card::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #4f46e5, #6366f1, #818cf8, #93c5fd);
    z-index: -1;
    border-radius: inherit;
    opacity: 0.5;
    filter: blur(8px);
}
.auth-card::after {
    content: '';
    position: absolute;
    top: -1px;
    left: -1px;
    right: -1px;
    bottom: -1px;
    background: linear-gradient(45deg, #4f46e5, #6366f1, #818cf8, #93c5fd);
    z-index: -1;
    border-radius: inherit;
    opacity: 0.2;
}
.form-input {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    transition: all 0.3s ease;
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    outline: none;
}
.form-input:focus {
    background: rgba(255, 255, 255, 0.95);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15),
    0 0 0 2px rgba(79, 70, 229, 0.1);
    border-color: #4f46e5;
}
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}
.form-error {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
.btn-primary {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    background: linear-gradient(45deg, #4f46e5, #6366f1);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
}
.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #818cf8, #93c5fd);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.btn-primary:hover::before {
    opacity: 1;
}
.btn-primary span {
    position: relative;
    z-index: 1;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
}
.password-strength {
    display: flex;
    gap: 0.25rem;
    margin-top: 0.5rem;
}
.password-strength div {
    height: 0.25rem;
    flex-grow: 1;
    border-radius: 9999px;
    background-color: #e5e7eb;
}
</style>

<div class="auth-container flex items-center justify-center min-h-screen px-4 py-12">
    <div class="auth-card w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary" style="color: #4f46e5;">User Management</h1>
            <h2 class="text-2xl font-bold text-gray-800 mt-4">Create an account</h2>
            <p class="text-gray-600 mt-2">Fill in your details to get started</p>
        </div>
        
        <?php if (isset($errors['general'])): ?>
        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6">
            <div class="flex">
                <div class="w-6 h-6 flex items-center justify-center mr-2">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <p class="text-sm"><?php echo $errors['general']; ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
            <div class="mb-4">
                <label for="user_name" class="form-label">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-user"></i>
                    </div>
                    <input type="text" id="user_name" name="user_name" 
                           class="form-input pl-10" 
                           placeholder="Enter your full name"
                           value="<?php echo $formData['user_name']; ?>" required>
                </div>
                <?php if (isset($errors['user_name'])): ?>
                <div class="form-error"><?php echo $errors['user_name']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="user_email" class="form-label">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input type="email" id="user_email" name="user_email" 
                           class="form-input pl-10" 
                           placeholder="Enter your email address"
                           value="<?php echo $formData['user_email']; ?>" required>
                </div>
                <?php if (isset($errors['user_email'])): ?>
                <div class="form-error"><?php echo $errors['user_email']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="user_password" class="form-label">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" id="user_password" name="user_password" 
                           class="form-input pl-10" 
                           placeholder="Create a password" required>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" id="toggle-password">
                        <i class="fas fa-eye text-gray-400"></i>
                    </div>
                </div>
                
                <div class="password-strength mt-2">
                    <div id="strength-1"></div>
                    <div id="strength-2"></div>
                    <div id="strength-3"></div>
                    <div id="strength-4"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1" id="password-strength-text">Password strength: Too weak</p>
                
                <?php if (isset($errors['user_password'])): ?>
                <div class="form-error"><?php echo $errors['user_password']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-input pl-10" 
                           placeholder="Confirm your password" required>
                </div>
                <?php if (isset($errors['confirm_password'])): ?>
                <div class="form-error"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-primary w-full focus:outline-none">
                <span>Create Account</span>
            </button>
            
            <div class="text-center mt-6">
                <p class="text-gray-600 text-sm">
                    Already have an account?
                    <a href="login.php" class="text-primary hover:text-primary/80 font-medium" style="color: #4f46e5;">Sign in</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('user_password');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle icon
            const icon = togglePassword.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
    
    // Password strength indicator
    const passwordInput = document.getElementById('user_password');
    const strengthBars = [
        document.getElementById('strength-1'),
        document.getElementById('strength-2'),
        document.getElementById('strength-3'),
        document.getElementById('strength-4')
    ];
    const strengthText = document.getElementById('password-strength-text');
    
    if (passwordInput && strengthBars.every(bar => bar) && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Reset all bars
            strengthBars.forEach(bar => {
                bar.style.backgroundColor = '#e5e7eb';
            });
            
            if (password.length >= 8) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^A-Za-z0-9]/)) strength++;
            
            // Update bars based on strength
            for (let i = 0; i < strength; i++) {
                if (strength === 1) {
                    strengthBars[i].style.backgroundColor = '#ef4444'; // red
                } else if (strength === 2) {
                    strengthBars[i].style.backgroundColor = '#f97316'; // orange
                } else if (strength === 3) {
                    strengthBars[i].style.backgroundColor = '#eab308'; // yellow
                } else {
                    strengthBars[i].style.backgroundColor = '#22c55e'; // green
                }
            }
            
            // Update text
            if (strength === 0) {
                strengthText.textContent = 'Password strength: Too weak';
                strengthText.style.color = '#6b7280'; // gray
            } else if (strength === 1) {
                strengthText.textContent = 'Password strength: Weak';
                strengthText.style.color = '#ef4444'; // red
            } else if (strength === 2) {
                strengthText.textContent = 'Password strength: Fair';
                strengthText.style.color = '#f97316'; // orange
            } else if (strength === 3) {
                strengthText.textContent = 'Password strength: Good';
                strengthText.style.color = '#eab308'; // yellow
            } else {
                strengthText.textContent = 'Password strength: Strong';
                strengthText.style.color = '#22c55e'; // green
            }
        });
    }
});
</script>

<?php
include_once 'includes/footer.php';
?>