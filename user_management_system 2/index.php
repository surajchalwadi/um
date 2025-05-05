<?php
include_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto mt-10 text-center">
    <h1 class="text-4xl font-bold text-blue-600 mb-6">Welcome to User Management System</h1>
    <p class="text-xl mb-8">A secure and user-friendly platform to manage your account.</p>
    
    <div class="flex justify-center space-x-6">
        <?php if (!isLoggedIn()): ?>
            <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">Login</a>
            <a href="register.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">Register</a>
        <?php else: ?>
            <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">Go to Dashboard</a>
        <?php endif; ?>
    </div>
    
    <div class="mt-16 grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="text-4xl text-blue-600 mb-4">🔐</div>
            <h3 class="text-xl font-bold mb-2">Secure</h3>
            <p>Your data is protected with industry-standard encryption and security measures.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="text-4xl text-blue-600 mb-4">⚡</div>
            <h3 class="text-xl font-bold mb-2">Fast</h3>
            <p>Optimized performance for quick access to your account information.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="text-4xl text-blue-600 mb-4">🎨</div>
            <h3 class="text-xl font-bold mb-2">User-Friendly</h3>
            <p>Intuitive interface designed for the best user experience.</p>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>