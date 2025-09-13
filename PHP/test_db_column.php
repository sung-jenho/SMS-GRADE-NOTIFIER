<?php
// Simple test to check if profile_picture column exists and add it if needed
require_once __DIR__ . '/db.php';

try {
    $mysqli = get_db_connection();
    
    // Check if profile_picture column exists
    $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    
    if ($result->num_rows == 0) {
        // Column doesn't exist, add it
        $sql = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER email";
        
        if ($mysqli->query($sql)) {
            echo "✅ Successfully added profile_picture column to users table.\n";
        } else {
            echo "❌ Error adding column: " . $mysqli->error . "\n";
        }
    } else {
        echo "✅ profile_picture column already exists in users table.\n";
    }
    
    // Test the column by selecting from it
    $test_query = $mysqli->query("SELECT id, username, profile_picture FROM users LIMIT 1");
    if ($test_query) {
        echo "✅ Column is accessible and working.\n";
        $row = $test_query->fetch_assoc();
        if ($row) {
            echo "Sample data: User ID " . $row['id'] . " (" . $row['username'] . ") - Profile: " . ($row['profile_picture'] ?? 'NULL') . "\n";
        }
    } else {
        echo "❌ Error accessing column: " . $mysqli->error . "\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
