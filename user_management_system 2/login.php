<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$formData = [
    'user_email' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData['user_email'] = cleanInput($_POST['user_email'] ?? '');
    $password = $_POST['user_password'] ?? '';
    
    // Validate inputs
    if (empty($formData['user_email'])) {
        $errors['user_email'] = 'Email is required';
    } elseif (!validateEmail($formData['user_email'])) {
        $errors['user_email'] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors['user_password'] = 'Password is required';
    }
    
    // If no validation errors, check credentials
    if (empty($errors)) {
        // Get user by email
        $user = getUserByEmail($formData['user_email']);
        
        if ($user && password_verify($password, $user['user_password'])) {
            if ($user['user_status'] === 'active') {
                // Start session and set user data
                sessionStart();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_email'] = $user['user_email'];
                
                // Update last login timestamp
                updateLastLogin($user['user_id']);
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $errors['general'] = 'Your account is inactive. Please contact support.';
            }
        } else {
            $errors['general'] = 'Invalid email or password';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#6366f1'
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '8px',
                        'md': '12px',
                        'lg': '16px',
                        'xl': '20px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
                        'button': '8px'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
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
        }
        .form-input:focus {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15),
            0 0 0 2px rgba(79, 70, 229, 0.1);
            border-color: #4f46e5;
        }
        .form-input:focus + .input-focus-effect {
            opacity: 1;
            transform: scaleX(1);
        }
        .input-container {
            position: relative;
        }
        .input-focus-effect {
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #4f46e5, #6366f1);
            opacity: 0;
            transform: scaleX(0);
            transition: all 0.3s ease;
            transform-origin: left;
        }
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            background: linear-gradient(45deg, #4f46e5, #6366f1);
            border: none;
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
    </style>
</head>
<body>
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-['Pacifico'] text-primary">YourLogo</h1>
                    <div class="hidden md:flex items-center ml-10 space-x-8">
                        <a href="index.php" class="text-gray-700 hover:text-primary">Home</a>
                        <a href="login.php" class="text-primary font-medium">Login</a>
                        <a href="register.php" class="text-gray-700 hover:text-primary">Register</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <button type="button" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none" id="mobile-menu-button">
                        <i class="ri-menu-line text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="index.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50">Home</a>
                <a href="login.php" class="block px-3 py-2 text-primary font-medium hover:bg-gray-50">Login</a>
                <a href="register.php" class="block px-3 py-2 text-gray-700 hover:text-primary hover:bg-gray-50">Register</a>
            </div>
        </div>
    </nav>

    <div class="pt-16">
        <div class="auth-container min-h-screen flex items-center justify-center px-4">
            <div class="auth-card rounded-lg w-full max-w-md">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-['Pacifico'] text-primary">YourLogo</h1>
                        <h2 class="text-2xl font-bold text-gray-800 mt-4">Welcome back</h2>
                        <p class="text-gray-600 mt-2">Sign in to your account</p>
                    </div>

                    <?php if (isset($errors['general'])): ?>
                    <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6">
                        <div class="flex">
                            <div class="w-6 h-6 flex items-center justify-center mr-2">
                                <i class="ri-error-warning-line"></i>
                            </div>
                            <p class="text-sm"><?php echo $errors['general']; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                        <div class="mb-4">
                            <label for="user_email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="ri-mail-line"></i>
                                </div>
                                <input type="email" id="user_email" name="user_email" 
                                       class="form-input pl-10 w-full border rounded-md" 
                                       placeholder="Enter your email"
                                       value="<?php echo $formData['user_email']; ?>" required>
                                <div class="input-focus-effect"></div>
                            </div>
                            <?php if (isset($errors['user_email'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $errors['user_email']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <label for="user_password" class="block text-gray-700 font-medium">Password</label>
                                <a href=forgot_password.php class="text-sm text-primary hover:text-primary/80">Forgot password?</a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="ri-lock-line"></i>
                                </div>
                                <input type="password" id="user_password" name="user_password" 
                                       class="form-input pl-10 w-full border rounded-md" 
                                       placeholder="Enter your password" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" id="toggle-password">
                                    <i class="ri-eye-line text-gray-400"></i>
                                </div>
                                <div class="input-focus-effect"></div>
                            </div>
                            <?php if (isset($errors['user_password'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $errors['user_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <span class="ml-2 text-gray-700 text-sm">Remember me</span>
                            </label>
                        </div>
                        
                        <button type="submit" 
                                class="btn-primary w-full rounded-md text-white font-medium py-2.5 px-4 focus:outline-none">
                            <span>Sign In</span>
                        </button>

                        <div class="text-center mt-6">
                            <p class="text-gray-600 text-sm">
                                Don't have an account?
                                <a href="register.php" class="text-primary hover:text-primary/80 font-medium">Register now</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('user_password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.className = 'ri-eye-off-line text-gray-400';
            } else {
                icon.className = 'ri-eye-line text-gray-400';
            }
        });

        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>