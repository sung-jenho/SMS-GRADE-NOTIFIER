<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// DEBUG: Log all incoming data
file_put_contents(__DIR__ . '/upload_debug.log', 
    "=== " . date('Y-m-d H:i:s') . " ===\n" .
    "POST: " . print_r($_POST, true) . "\n" .
    "FILES: " . print_r($_FILES, true) . "\n\n", 
    FILE_APPEND
);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $mysqli = get_db_connection();
    $user_id = $_SESSION['user_id'];
    
    // Get current user data for validation
    $stmt = $mysqli->prepare('SELECT username, password_hash FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $current_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$current_user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Check if this is a password change or other sensitive changes
    $new_password = $_POST['new_password'] ?? '';
    $username_change = isset($_POST['username']) && $_POST['username'] !== $current_user['username'];
    $sensitive_change = !empty($new_password) || $username_change;
    
    // Validate current password only for sensitive changes
    $current_password = $_POST['current_password'] ?? '';
    if ($sensitive_change) {
        if (empty($current_password)) {
            echo json_encode(['success' => false, 'message' => 'Current password is required for password or username changes']);
            exit;
        }
        if (!password_verify($current_password, $current_user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }
    }
    
    // Validate new password confirmation
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if (!empty($new_password) && $new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match']);
        exit;
    }
    
    // Handle profile picture upload
    $profile_picture_filename = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK && $_FILES['profile_picture']['size'] > 0 && !empty($_FILES['profile_picture']['name'])) {
        $upload_dir = '../uploads/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }
        
        // Generate unique filename
        $profile_picture_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $profile_picture_filename;
        
        if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile picture']);
            exit;
        }
        
        // Delete old profile picture if exists
        $stmt = $mysqli->prepare('SELECT profile_picture FROM users WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $old_picture = $stmt->get_result()->fetch_assoc()['profile_picture'] ?? null;
        $stmt->close();
        
        if ($old_picture && file_exists($upload_dir . $old_picture)) {
            unlink($upload_dir . $old_picture);
        }
    }
    
    // Prepare update query
    $update_fields = [];
    $update_values = [];
    $update_types = '';
    
    // Full name
    if (isset($_POST['full_name'])) {
        $update_fields[] = 'full_name = ?';
        $update_values[] = $_POST['full_name'];
        $update_types .= 's';
    }
    
    // Username
    if (isset($_POST['username'])) {
        $update_fields[] = 'username = ?';
        $update_values[] = $_POST['username'];
        $update_types .= 's';
    }
    
    // Email
    if (isset($_POST['email'])) {
        $update_fields[] = 'email = ?';
        $update_values[] = $_POST['email'];
        $update_types .= 's';
    }
    
    // Profile picture
    if ($profile_picture_filename) {
        $update_fields[] = 'profile_picture = ?';
        $update_values[] = $profile_picture_filename;
        $update_types .= 's';
    }
    
    // Password
    if (!empty($new_password)) {
        $update_fields[] = 'password_hash = ?';
        $update_values[] = password_hash($new_password, PASSWORD_DEFAULT);
        $update_types .= 's';
    }
    
    // Allow profile picture only updates or other field updates
    if (empty($update_fields) && !$profile_picture_filename) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }
    
    // If only profile picture is being updated, we still need to update the database
    if ($profile_picture_filename && empty($update_fields)) {
        $update_fields[] = 'profile_picture = ?';
        $update_values[] = $profile_picture_filename;
        $update_types .= 's';
    }
    
    // Handle database update
    $update_success = true;
    if (!empty($update_fields)) {
        // Add user ID for WHERE clause
        $update_values[] = $user_id;
        $update_types .= 'i';
        
        $sql = 'UPDATE users SET ' . implode(', ', $update_fields) . ' WHERE id = ?';
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($update_types, ...$update_values);
            $update_success = $stmt->execute();
            if (!$update_success) {
                file_put_contents(__DIR__ . '/upload_debug.log', 
                    "SQL Error: " . $stmt->error . "\n" .
                    "SQL: " . $sql . "\n" .
                    "Values: " . print_r($update_values, true) . "\n\n", 
                    FILE_APPEND
                );
            }
            $stmt->close();
        } else {
            $update_success = false;
            file_put_contents(__DIR__ . '/upload_debug.log', 
                "Prepare Error: " . $mysqli->error . "\n\n", 
                FILE_APPEND
            );
        }
    }
    
    if ($update_success) {
        // Update session data with new values
        if (isset($_POST['full_name'])) {
            $_SESSION['full_name'] = $_POST['full_name'];
        }
        if (isset($_POST['username'])) {
            $_SESSION['username'] = $_POST['username'];
        }
        if (isset($_POST['email'])) {
            $_SESSION['email'] = $_POST['email'];
        }
        if ($profile_picture_filename) {
            $_SESSION['profile_picture'] = $profile_picture_filename;
        }
        
        $response = ['success' => true, 'message' => 'Profile updated successfully'];
        if ($profile_picture_filename) {
            $response['profile_picture_url'] = '../uploads/profiles/' . $profile_picture_filename;
        }
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
    
} catch (Exception $e) {
    error_log('Profile update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
