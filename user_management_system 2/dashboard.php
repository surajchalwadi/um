<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

// Check if user is logged in
requireLogin();

// Get user data
$user = getUserById($_SESSION['user_id']);

if (!$user) {
    // If user doesn't exist, redirect to login
    setFlashMessage('error', 'Session expired. Please login again.');
    header("Location: logout.php");
    exit();
}

include_once 'includes/header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-blue-600">Dashboard</h1>
        <div class="flex space-x-2">
            <a href="edit_profile.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                Edit Profile
            </a>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                Logout
            </a>
        </div>
    </div>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-md">
        <h2 class="text-xl font-bold text-blue-800 mb-2">Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</h2>
        <p class="text-blue-600">
            We're glad to see you here. This is your personal dashboard where you can manage your account.
        </p>
    </div>
    
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Account Information</h3>
            <ul class="space-y-3">
                <li class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($user['user_name']); ?></span>
                </li>
                <li class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($user['user_email']); ?></span>
                </li>
                <li class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium <?php echo $user['user_status'] === 'active' ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo ucfirst($user['user_status']); ?>
                    </span>
                </li>
                <li class="flex justify-between">
                    <span class="text-gray-600">Account Created:</span>
                    <span class="font-medium"><?php echo date('M d, Y', strtotime($user['user_created'])); ?></span>
                </li>
            </ul>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Login Information</h3>
            <div class="flex flex-col space-y-4">
                <div>
                    <p class="text-gray-600 mb-1">Last Login:</p>
                    <p class="font-medium">
                        <?php 
                        if ($user['user_last_login']) {
                            echo date('M d, Y H:i:s', strtotime($user['user_last_login']));
                        } else {
                            echo 'First time login';
                        }
                        ?>
                    </p>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-gray-600 mb-2">Account Security Tip:</p>
                    <p class="text-sm text-gray-500">
                        Remember to regularly update your password and never share your login credentials with others.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>