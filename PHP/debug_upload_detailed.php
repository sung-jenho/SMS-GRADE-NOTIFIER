<?php
session_start();
require_once __DIR__ . '/db.php';

echo "<h3>Detailed Upload Debug</h3>";

// Check current user data
$mysqli = get_db_connection();
$stmt = $mysqli->prepare('SELECT id, username, profile_picture FROM users WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo "<p><strong>Before Upload:</strong></p>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
echo "<p>Current DB Profile Picture: " . htmlspecialchars($user['profile_picture'] ?? 'NULL') . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<hr><h4>Processing Upload...</h4>";
    
    // Debug file upload
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        echo "<p>File received: " . htmlspecialchars($file['name']) . "</p>";
        echo "<p>File size: " . $file['size'] . " bytes</p>";
        echo "<p>File error: " . $file['error'] . "</p>";
        echo "<p>Temp name: " . $file['tmp_name'] . "</p>";
        
        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
            $user_id = $_SESSION['user_id'];
            $upload_dir = '../uploads/profiles/';
            
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $profile_picture_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $profile_picture_filename;
            
            echo "<p>Target filename: $profile_picture_filename</p>";
            echo "<p>Upload path: $upload_path</p>";
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo "<p>✅ File moved successfully</p>";
                
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
                    echo "<p>✅ Session updated</p>";
                    
                    // Verify database update
                    $verify_stmt = $mysqli->prepare('SELECT profile_picture FROM users WHERE id = ?');
                    $verify_stmt->bind_param('i', $user_id);
                    $verify_stmt->execute();
                    $verify_result = $verify_stmt->get_result()->fetch_assoc();
                    $verify_stmt->close();
                    
                    echo "<p><strong>Verification:</strong></p>";
                    echo "<p>New DB Profile Picture: " . htmlspecialchars($verify_result['profile_picture']) . "</p>";
                    echo "<p>Session Profile Picture: " . htmlspecialchars($_SESSION['profile_picture'] ?? 'NULL') . "</p>";
                    
                    echo "<p><strong>New Profile Image:</strong></p>";
                    echo "<img src='../uploads/profiles/$profile_picture_filename' style='width:100px;height:100px;border-radius:50%;'>";
                    
                } else {
                    echo "<p>❌ Database update failed: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p>❌ File move failed</p>";
            }
        } else {
            echo "<p>❌ File upload error or empty file</p>";
        }
    } else {
        echo "<p>❌ No file uploaded</p>";
    }
}
?>

<hr>
<h4>Test Upload Form:</h4>
<form method="POST" enctype="multipart/form-data">
    <p>
        <label>Profile Picture:</label><br>
        <input type="file" name="profile_picture" accept="image/*" required>
    </p>
    <p>
        <button type="submit">Upload Test</button>
    </p>
</form>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3, h4 { color: #333; }
p { margin: 5px 0; }
form { background: #f5f5f5; padding: 15px; border-radius: 5px; }
</style>
