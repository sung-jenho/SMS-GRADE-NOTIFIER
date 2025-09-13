<?php
session_start();
require_once __DIR__ . '/db.php';

echo "<h3>Database Profile Check</h3>";

try {
    $mysqli = get_db_connection();
    $stmt = $mysqli->prepare('SELECT id, username, email, full_name, profile_picture FROM users WHERE id = 2');
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    echo "<p><strong>Current Database Data for User ID 2:</strong></p>";
    echo "<p>Username: " . htmlspecialchars($user['username']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
    echo "<p>Full Name: " . htmlspecialchars($user['full_name']) . "</p>";
    echo "<p>Profile Picture: " . htmlspecialchars($user['profile_picture'] ?? 'NULL') . "</p>";
    
    if ($user['profile_picture']) {
        $profile_path = '../uploads/profiles/' . $user['profile_picture'];
        echo "<p>Profile file exists: " . (file_exists($profile_path) ? "✅ Yes" : "❌ No") . "</p>";
        if (file_exists($profile_path)) {
            echo "<p>Current profile image:</p>";
            echo "<img src='$profile_path' style='width:100px;height:100px;border-radius:50%;'>";
        }
    }
    
    // Check all profile files
    echo "<hr><h4>All Profile Files:</h4>";
    $files = glob('../uploads/profiles/profile_2_*.jpg');
    foreach ($files as $file) {
        $filename = basename($file);
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "<p>$filename - $size bytes - Modified: $modified</p>";
        echo "<img src='../uploads/profiles/$filename' style='width:50px;height:50px;border-radius:50%;margin:5px;'>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3, h4 { color: #333; }
p { margin: 5px 0; }
</style>
