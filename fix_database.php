<?php
// Fix database by adding photo column to students table
require_once 'PHP/db.php';

try {
    $mysqli = get_db_connection();
    
    // Check if photo column already exists
    $result = $mysqli->query("SHOW COLUMNS FROM students LIKE 'photo'");
    
    if ($result->num_rows == 0) {
        // Add photo column
        $mysqli->query("ALTER TABLE students ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
        echo "✅ Successfully added 'photo' column to students table.\n";
    } else {
        echo "ℹ️ Photo column already exists in students table.\n";
    }
    
    // Verify the column was added
    $result = $mysqli->query("SHOW COLUMNS FROM students");
    echo "\n📋 Current students table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
