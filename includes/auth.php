<?php
/**
 * Authentication middleware
 * Include this file at the top of any page that requires authentication
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Optional: Check if session has expired (24 hours)
$session_timeout = 24 * 60 * 60; // 24 hours in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_destroy();
    header('Location: ../login.php?error=session_expired');
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

// Helper function to check if user has specific permissions
function user_has_permission($permission) {
    // For now, all logged-in users have all permissions
    // In the future, you can implement role-based access control here
    return true;
}

// Helper function to get current user data (renamed to avoid PHP builtin name collision)
function get_current_user_info() {
    global $current_user;
    return $current_user;
}
?>
