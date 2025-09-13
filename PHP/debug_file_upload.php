<?php
session_start();
require_once __DIR__ . '/db.php';

echo "<h3>File Upload Debug</h3>";

// Set user ID for testing
$_SESSION['user_id'] = 2;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h4>POST Request Received</h4>";
    
    echo "<p><strong>POST Data:</strong></p>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    echo "<p><strong>FILES Data:</strong></p>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        echo "<h4>File Processing:</h4>";
        
        echo "<p>File name: " . htmlspecialchars($file['name']) . "</p>";
        echo "<p>File size: " . $file['size'] . " bytes</p>";
        echo "<p>File error: " . $file['error'] . "</p>";
        echo "<p>File type: " . htmlspecialchars($file['type']) . "</p>";
        echo "<p>Temp name: " . $file['tmp_name'] . "</p>";
        echo "<p>Temp file exists: " . (file_exists($file['tmp_name']) ? "✅ Yes" : "❌ No") . "</p>";
        
        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
            $upload_dir = '../uploads/profiles/';
            echo "<p>Upload directory: $upload_dir</p>";
            echo "<p>Directory exists: " . (is_dir($upload_dir) ? "✅ Yes" : "❌ No") . "</p>";
            echo "<p>Directory writable: " . (is_writable($upload_dir) ? "✅ Yes" : "❌ No") . "</p>";
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
                echo "<p>✅ Created upload directory</p>";
            }
            
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'profile_2_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            echo "<p>Target filename: $filename</p>";
            echo "<p>Full upload path: $upload_path</p>";
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo "<p>✅ File uploaded successfully!</p>";
                echo "<p>File exists: " . (file_exists($upload_path) ? "✅ Yes" : "❌ No") . "</p>";
                echo "<p>File size on disk: " . filesize($upload_path) . " bytes</p>";
                
                // Try to update database
                try {
                    $mysqli = get_db_connection();
                    $stmt = $mysqli->prepare('UPDATE users SET profile_picture = ? WHERE id = ?');
                    $stmt->bind_param('si', $filename, $_SESSION['user_id']);
                    
                    if ($stmt->execute()) {
                        echo "<p>✅ Database updated successfully!</p>";
                        echo "<p>New profile picture: $filename</p>";
                        echo "<img src='../uploads/profiles/$filename' style='width:100px;height:100px;border-radius:50%;'>";
                    } else {
                        echo "<p>❌ Database update failed: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>❌ File upload failed!</p>";
                echo "<p>Last error: " . error_get_last()['message'] . "</p>";
            }
        } else {
            echo "<p>❌ File upload error or empty file</p>";
            echo "<p>Error code: " . $file['error'] . "</p>";
        }
    } else {
        echo "<p>❌ No profile_picture file found in upload</p>";
    }
} else {
    echo "<p>No POST request received yet.</p>";
}
?>

<hr>
<h4>Test File Upload:</h4>
<form method="POST" enctype="multipart/form-data">
    <p>
        <label>Profile Picture:</label><br>
        <input type="file" name="profile_picture" accept="image/*" required>
    </p>
    <p>
        <button type="submit">Upload File</button>
    </p>
</form>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3, h4 { color: #333; }
p { margin: 5px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
form { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 20px; }
</style>
