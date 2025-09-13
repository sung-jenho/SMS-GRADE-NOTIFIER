<?php
session_start();
require_once __DIR__ . '/db.php';

echo "<h3>Profile Update Test</h3>";

// Simulate being logged in as user ID 2
$_SESSION['user_id'] = 2;

try {
    $mysqli = get_db_connection();
    $user_id = $_SESSION['user_id'];
    
    echo "<p>✅ Database connection successful</p>";
    echo "<p>✅ User ID: $user_id</p>";
    
    // Get current user data
    $stmt = $mysqli->prepare('SELECT username, password_hash, full_name, email FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $current_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$current_user) {
        echo "<p>❌ User not found in database</p>";
        exit;
    }
    
    echo "<p>✅ Current user data found:</p>";
    echo "<ul>";
    echo "<li>Username: " . htmlspecialchars($current_user['username']) . "</li>";
    echo "<li>Full Name: " . htmlspecialchars($current_user['full_name']) . "</li>";
    echo "<li>Email: " . htmlspecialchars($current_user['email']) . "</li>";
    echo "</ul>";
    
    // Test simple update (just full name)
    $new_name = "CTUCC ADMIN TEST";
    $stmt = $mysqli->prepare('UPDATE users SET full_name = ? WHERE id = ?');
    $stmt->bind_param('si', $new_name, $user_id);
    
    if ($stmt->execute()) {
        echo "<p>✅ Database update successful</p>";
        
        // Verify the update
        $verify_stmt = $mysqli->prepare('SELECT full_name FROM users WHERE id = ?');
        $verify_stmt->bind_param('i', $user_id);
        $verify_stmt->execute();
        $result = $verify_stmt->get_result()->fetch_assoc();
        $verify_stmt->close();
        
        echo "<p>✅ Verification: New name is '" . htmlspecialchars($result['full_name']) . "'</p>";
        
        // Restore original name
        $restore_stmt = $mysqli->prepare('UPDATE users SET full_name = ? WHERE id = ?');
        $restore_stmt->bind_param('si', $current_user['full_name'], $user_id);
        $restore_stmt->execute();
        $restore_stmt->close();
        
        echo "<p>✅ Original name restored</p>";
    } else {
        echo "<p>❌ Database update failed: " . $stmt->error . "</p>";
    }
    $stmt->close();
    
} catch (Exception $e) {
    echo "<p>❌ Exception: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}

// Test the actual update_admin_profile.php endpoint
echo "<hr><h4>Testing update_admin_profile.php endpoint:</h4>";

// Simulate POST data
$_POST['full_name'] = 'CTUCC ADMIN TEST';
$_POST['username'] = 'ctucc@gmail.com';
$_POST['email'] = 'ctucc@gmail.com';

ob_start();
try {
    include 'update_admin_profile.php';
    $output = ob_get_contents();
} catch (Exception $e) {
    $output = "Exception: " . $e->getMessage();
}
ob_end_clean();

echo "<p><strong>Output from update_admin_profile.php:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Check if it's valid JSON
$json_data = json_decode($output, true);
if ($json_data !== null) {
    echo "<p>✅ Valid JSON response</p>";
    echo "<p>Success: " . ($json_data['success'] ? 'true' : 'false') . "</p>";
    echo "<p>Message: " . htmlspecialchars($json_data['message']) . "</p>";
} else {
    echo "<p>❌ Invalid JSON response</p>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3, h4 { color: #333; }
p { margin: 5px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>
