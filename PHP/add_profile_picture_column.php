<?php
// Add profile_picture column to users table
require_once __DIR__ . '/db.php';

try {
    $mysqli = get_db_connection();
    
    // Check if profile_picture column already exists
    $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    
    if ($result->num_rows == 0) {
        // Column doesn't exist, add it
        $sql = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER email";
        
        if ($mysqli->query($sql)) {
            echo "Successfully added profile_picture column to users table.\n";
        } else {
            echo "Error adding column: " . $mysqli->error . "\n";
        }
    } else {
        echo "profile_picture column already exists in users table.\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
