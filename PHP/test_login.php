<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Testing Login System</h3>";

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/queries.php';

try {
    echo "<p>1. Testing database connection...</p>";
    $mysqli = get_db_connection();
    echo "<p>✓ Database connected successfully</p>";
    
    echo "<p>2. Checking if users table exists...</p>";
    $result = $mysqli->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p>✓ Users table exists</p>";
    } else {
        echo "<p>✗ Users table does not exist</p>";
        exit;
    }
    
    echo "<p>3. Checking users in database...</p>";
    $result = $mysqli->query("SELECT id, username, email, full_name FROM users");
    if ($result->num_rows > 0) {
        echo "<p>✓ Found " . $result->num_rows . " user(s):</p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: {$row['id']}, Username: {$row['username']}, Email: {$row['email']}, Name: {$row['full_name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>✗ No users found in database</p>";
    }
    
    echo "<p>4. Testing login function with your credentials...</p>";
    $username = 'ctucc@edu.ph';
    $password = 'ctuccadmin';
    
    $user = find_user_by_username($username);
    if ($user) {
        echo "<p>✓ User found: " . print_r($user, true) . "</p>";
        
        if (password_verify($password, $user['password_hash'])) {
            echo "<p>✓ Password verification successful</p>";
            echo "<p><strong>Login should work with:</strong></p>";
            echo "<p>Username: ctucc@edu.ph</p>";
            echo "<p>Password: ctuccadmin</p>";
        } else {
            echo "<p>✗ Password verification failed</p>";
            echo "<p>Stored hash: " . $user['password_hash'] . "</p>";
            echo "<p>Testing password: ctuccadmin</p>";
        }
    } else {
        echo "<p>✗ User not found with username: $username</p>";
    }
    
} catch (Exception $e) {
    echo "<p>✗ Error: " . $e->getMessage() . "</p>";
}
?>
