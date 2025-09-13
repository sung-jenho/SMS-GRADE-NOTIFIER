<?php
session_start();
require_once __DIR__ . '/db.php';

echo "<h3>Debug Profile Upload</h3>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ User not logged in</p>";
    exit;
}

echo "<p>✅ User logged in: ID = " . $_SESSION['user_id'] . "</p>";

// Check uploads directory
$upload_dir = '../uploads/profiles/';
echo "<p>Upload directory: " . $upload_dir . "</p>";
echo "<p>Directory exists: " . (is_dir($upload_dir) ? "✅ Yes" : "❌ No") . "</p>";
echo "<p>Directory writable: " . (is_writable($upload_dir) ? "✅ Yes" : "❌ No") . "</p>";

// Check current user data
try {
    $mysqli = get_db_connection();
    $stmt = $mysqli->prepare('SELECT username, email, full_name, profile_picture FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    echo "<h4>Current User Data:</h4>";
    echo "<p>Username: " . htmlspecialchars($user['username']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
    echo "<p>Full Name: " . htmlspecialchars($user['full_name']) . "</p>";
    echo "<p>Profile Picture: " . htmlspecialchars($user['profile_picture'] ?? 'None') . "</p>";
    
    if ($user['profile_picture']) {
        $profile_path = $upload_dir . $user['profile_picture'];
        echo "<p>Profile file exists: " . (file_exists($profile_path) ? "✅ Yes" : "❌ No") . "</p>";
        if (file_exists($profile_path)) {
            echo "<p>File size: " . filesize($profile_path) . " bytes</p>";
            echo "<img src='../uploads/profiles/" . htmlspecialchars($user['profile_picture']) . "' style='width:100px;height:100px;border-radius:50%;'>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<hr><h4>Form Submission Test:</h4>";
    echo "<p>POST data received:</p>";
    foreach ($_POST as $key => $value) {
        echo "<p>$key: " . htmlspecialchars($value) . "</p>";
    }
    
    echo "<p>FILES data:</p>";
    if (isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];
        echo "<p>File name: " . htmlspecialchars($file['name']) . "</p>";
        echo "<p>File size: " . $file['size'] . " bytes</p>";
        echo "<p>File error: " . $file['error'] . "</p>";
        echo "<p>File type: " . htmlspecialchars($file['type']) . "</p>";
        
        // Test the actual upload process
        echo "<hr><h4>Testing Upload Process:</h4>";
        
        $user_id = $_SESSION['user_id'];
        $upload_dir = '../uploads/profiles/';
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<p>❌ Invalid file type: $file_extension</p>";
        } else {
            echo "<p>✅ Valid file type: $file_extension</p>";
            
            // Generate unique filename
            $profile_picture_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $profile_picture_filename;
            
            echo "<p>Target filename: $profile_picture_filename</p>";
            echo "<p>Upload path: $upload_path</p>";
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo "<p>✅ File uploaded successfully!</p>";
                
                // Update database
                try {
                    $mysqli = get_db_connection();
                    $stmt = $mysqli->prepare('UPDATE users SET profile_picture = ? WHERE id = ?');
                    $stmt->bind_param('si', $profile_picture_filename, $user_id);
                    
                    if ($stmt->execute()) {
                        echo "<p>✅ Database updated successfully!</p>";
                        echo "<p>New profile picture: $profile_picture_filename</p>";
                        echo "<img src='../uploads/profiles/$profile_picture_filename' style='width:100px;height:100px;border-radius:50%;'>";
                    } else {
                        echo "<p>❌ Database update failed: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>❌ File upload failed</p>";
                echo "<p>Upload error: " . error_get_last()['message'] . "</p>";
            }
        }
    } else {
        echo "<p>❌ No profile_picture file in upload</p>";
    }
}
?>

<hr>
<h4>Test Upload Form:</h4>
<form method="POST" enctype="multipart/form-data">
    <p>
        <label>Full Name:</label><br>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
    </p>
    <p>
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
    </p>
    <p>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
    </p>
    <p>
        <label>Profile Picture:</label><br>
        <input type="file" name="profile_picture" accept="image/*">
    </p>
    <p>
        <button type="submit">Test Update</button>
    </p>
</form>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3, h4 { color: #333; }
p { margin: 5px 0; }
form { background: #f5f5f5; padding: 15px; border-radius: 5px; }
input, button { padding: 5px; margin: 5px 0; }
</style>
