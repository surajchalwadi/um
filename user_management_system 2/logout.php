<?php
require_once 'includes/functions.php';

// Start session if not already started
sessionStart();

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page with success message
setFlashMessage('success', 'You have been successfully logged out.');
header("Location: login.php");
exit();
?>