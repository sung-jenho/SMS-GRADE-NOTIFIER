<?php
/**
 * Shared helper functions for the SMS Grade System
 */

/**
 * Get the appropriate avatar path for the current logged-in user
 * @return string The path to the avatar image
 */
function get_avatar_path() {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return '../assets/default-avatar.svg';
    }
    
    // Get user data from database
    require_once __DIR__ . '/db.php';
    try {
        $mysqli = get_db_connection();
        $stmt = $mysqli->prepare('SELECT profile_picture FROM users WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Return user's profile picture if exists, otherwise default
        if ($user && !empty($user['profile_picture'])) {
            $profile_path = '../uploads/profiles/' . $user['profile_picture'];
            // Check if file actually exists
            if (file_exists(__DIR__ . '/../uploads/profiles/' . $user['profile_picture'])) {
                return $profile_path;
            }
        }
    } catch (Exception $e) {
        error_log('Error getting avatar path: ' . $e->getMessage());
    }
    
    // Fallback to default avatar
    return '../assets/default-avatar.svg';
}
?>
