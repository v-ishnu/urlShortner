<?php
session_start();
include 'db_config.php';

// If the user is logged in, clear their token in the database
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE users SET token = NULL WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

// Destroy the session
$_SESSION = [];
session_unset();
session_destroy();

// Remove the auth_token cookie
if (isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', '', time() - 3600, '/', '', false, true);
}

// Close DB connection
$conn->close();

// Redirect to login page
header("Location: login.php");
exit;
?>
