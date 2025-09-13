<?php
session_start();
require_once __DIR__ . '/db.php';

// Set a test user session if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2; // Assuming user ID 2 exists
    $_SESSION['username'] = 'test_admin';
}

echo "<h3>Profile Picture Upload Test</h3>";

// Check current database state
$mysqli = get_db_connection();
$stmt = $mysqli->prepare('SELECT id, username, profile_picture FROM users WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo "<p><strong>Current User Data:</strong></p>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
echo "<p>Username: " . htmlspecialchars($user['username']) . "</p>";
echo "<p>Current Profile Picture: " . htmlspecialchars($user['profile_picture'] ?? 'NULL') . "</p>";

if ($user['profile_picture']) {
    $profile_path = '../uploads/profiles/' . $user['profile_picture'];
    echo "<p>File exists: " . (file_exists($profile_path) ? "✅ Yes" : "❌ No") . "</p>";
    if (file_exists($profile_path)) {
        echo "<p>Current image:</p>";
        echo "<img src='$profile_path' style='width:100px;height:100px;border-radius:50%;'>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    echo "<hr><h4>Processing Upload...</h4>";
    
    $file = $_FILES['profile_picture'];
    echo "<p>File: " . htmlspecialchars($file['name']) . " (" . $file['size'] . " bytes)</p>";
    
    if ($file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
        $user_id = $_SESSION['user_id'];
        $upload_dir = '../uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            echo "<p>✅ Created upload directory</p>";
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<p>❌ Invalid file type. Only JPG, PNG, and GIF are allowed.</p>";
        } else {
            $profile_picture_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $profile_picture_filename;
            
            echo "<p>Target: $profile_picture_filename</p>";
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo "<p>✅ File uploaded successfully</p>";
                
                // Delete old profile picture
                if ($user['profile_picture'] && file_exists($upload_dir . $user['profile_picture'])) {
                    unlink($upload_dir . $user['profile_picture']);
                    echo "<p>✅ Old profile picture deleted</p>";
                }
                
                // Update database
                $stmt = $mysqli->prepare('UPDATE users SET profile_picture = ? WHERE id = ?');
                $stmt->bind_param('si', $profile_picture_filename, $user_id);
                
                if ($stmt->execute()) {
                    echo "<p>✅ Database updated successfully</p>";
                    
                    // Update session
                    $_SESSION['profile_picture'] = $profile_picture_filename;
                    
                    echo "<p><strong>Success! New profile picture:</strong></p>";
                    echo "<img src='../uploads/profiles/$profile_picture_filename' style='width:150px;height:150px;border-radius:50%;border:3px solid #28a745;'>";
                    
                    echo "<p><a href='?' style='color:#007bff;'>Refresh page to see persistence</a></p>";
                } else {
                    echo "<p>❌ Database update failed: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p>❌ File upload failed</p>";
            }
        }
    } else {
        echo "<p>❌ Upload error: " . $file['error'] . "</p>";
    }
}
?>

<hr>
<h4>Upload New Profile Picture:</h4>
<form method="POST" enctype="multipart/form-data" style="background:#f8f9fa;padding:20px;border-radius:8px;">
    <p>
        <label><strong>Choose Image:</strong></label><br>
        <input type="file" name="profile_picture" accept="image/*" required style="margin:10px 0;">
    </p>
    <p>
        <button type="submit" style="background:#007bff;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;">
            Upload Profile Picture
        </button>
    </p>
</form>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h3, h4 { color: #333; }
p { margin: 8px 0; }
</style>
