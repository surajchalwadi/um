<?php
require_once 'includes/functions.php';
sessionStart();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <!-- Include Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <a href="index.php" class="text-xl font-bold">User Management</a>
                <div>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="mr-4 hover:text-blue-200">Dashboard</a>
                        <a href="edit_profile.php" class="mr-4 hover:text-blue-200">Edit Profile</a>
                        <a href="logout.php" class="hover:text-blue-200">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="mr-4 hover:text-blue-200">Login</a>
                        <a href="register.php" class="hover:text-blue-200">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
        <main class="container mx-auto flex-grow p-4">
            <?php 
                $flash = getFlashMessage();
                if ($flash): 
                    $colorClass = $flash['type'] === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
            ?>
                <div class="<?php echo $colorClass; ?> border-l-4 p-4 mb-4 rounded">
                    <p><?php echo $flash['message']; ?></p>
                </div>
            <?php endif; ?>