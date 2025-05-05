<?php
// forgot_password.php

session_start();
require_once 'config/database.php';
$conn = getConnection(); // ✅ This line gets the actual connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(50));
    $stmt = $conn->prepare("UPDATE user_master SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE user_email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
        // In production, send this link via email.
        echo "<p>Reset link: <a href='$reset_link'>$reset_link</a></p>";
    } else {
        echo "<p>Email not found.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-blue-400 to-purple-500 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-lg shadow-md w-96">
    <h2 class="text-2xl font-bold mb-4 text-center">Forgot Password</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required class="w-full p-2 mb-4 border rounded">
      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">Send Reset Link</button>
    </form>
  </div>
</body>
</html>
