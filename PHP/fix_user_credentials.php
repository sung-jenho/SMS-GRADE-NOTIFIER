<?php
require_once __DIR__ . '/db.php';

try {
    $mysqli = get_db_connection();
    
    echo "<h3>Current User Data:</h3>";
    $result = $mysqli->query('SELECT id, username, email, full_name FROM users');
    while ($row = $result->fetch_assoc()) {
        echo "<p>ID: {$row['id']} | Username: {$row['username']} | Email: {$row['email']} | Name: {$row['full_name']}</p>";
    }
    
    echo "<hr><h3>Fixing User Credentials...</h3>";
    
    // Update the user to have a proper username (not email-like)
    // and set the email to what you want
    $stmt = $mysqli->prepare('UPDATE users SET username = ?, email = ? WHERE username = ?');
    $new_username = 'admin';  // Proper username for login
    $new_email = 'ctucc@gmail.com';  // Your desired email
    $old_username = 'ctucc@edu.ph';  // Current username that looks like email
    
    $stmt->bind_param('sss', $new_username, $new_email, $old_username);
    
    if ($stmt->execute()) {
        echo "<p>✅ User credentials updated successfully!</p>";
        echo "<p><strong>New Login Credentials:</strong></p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>ctuccadmin</strong> (unchanged)</p>";
        echo "<p>Email: <strong>ctucc@gmail.com</strong></p>";
        
        // Verify the update
        echo "<hr><h3>Updated User Data:</h3>";
        $result = $mysqli->query('SELECT id, username, email, full_name FROM users');
        while ($row = $result->fetch_assoc()) {
            echo "<p>ID: {$row['id']} | Username: {$row['username']} | Email: {$row['email']} | Name: {$row['full_name']}</p>";
        }
    } else {
        echo "<p>❌ Error updating user: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3 { color: #333; }
p { margin: 5px 0; }
hr { margin: 20px 0; }
</style>
