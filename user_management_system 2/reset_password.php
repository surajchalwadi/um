<?php
// reset_password.php
session_start();
require_once 'config/database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die("Invalid token.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->bind_param("ss", $password, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p>Password reset successfully. <a href='login.php'>Login</a></p>";
    } else {
        echo "<p>Invalid or expired token.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-indigo-400 to-pink-500 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-lg shadow-md w-96">
    <h2 class="text-2xl font-bold mb-4 text-center">Reset Password</h2>
    <form method="POST">
      <input type="password" name="password" placeholder="New Password" required class="w-full p-2 mb-4 border rounded">
      <button type="submit" class="w-full bg-purple-500 hover:bg-purple-600 text-white p-2 rounded">Reset Password</button>
    </form>
  </div>
</body>
</html>
