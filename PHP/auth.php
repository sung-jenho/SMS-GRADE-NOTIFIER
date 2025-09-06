<?php
/**
 * Authentication middleware
 * Include this file at the top of any page that requires authentication
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Optional: Check if session has expired (24 hours)
$session_timeout = 24 * 60 * 60; // 24 hours in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_destroy();
    header('Location: login.php?error=session_expired');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Get current user info
$current_user = [
    'id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null,
    'full_name' => $_SESSION['full_name'] ?? null,
    'email' => $_SESSION['email'] ?? null
];

?>
