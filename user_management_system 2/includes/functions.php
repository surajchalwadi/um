<?php
// Start session if not already started
function sessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    sessionStart();
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if already logged in
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit();
    }
}

// Clean and validate input data
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate password strength
function validatePassword($password) {
    // At least 8 characters, 1 uppercase, 1 number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// Get user data by ID
function getUserById($userId) {
    require_once 'config/database.php';
    $conn = getConnection();
    
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_status, user_created, user_last_login 
                           FROM user_master 
                           WHERE user_id = ? AND user_deleted = false");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get user data by email
function getUserByEmail($email) {
    require_once 'config/database.php';
    $conn = getConnection();
    
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password, user_status, user_created, user_last_login 
                           FROM user_master 
                           WHERE user_email = ? AND user_deleted = false");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Update last login timestamp
function updateLastLogin($userId) {
    require_once 'config/database.php';
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE user_master SET user_last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
}

// Set flash message
function setFlashMessage($type, $message) {
    sessionStart();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Get and clear flash message
function getFlashMessage() {
    sessionStart();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}