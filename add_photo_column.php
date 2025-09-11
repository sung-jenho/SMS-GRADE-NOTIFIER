<?php
// Simple script to add photo column to students table
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sms_grades";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Check if photo column exists
    $result = $conn->query("SHOW COLUMNS FROM students LIKE 'photo'");
    
    if ($result->num_rows == 0) {
        // Add photo column
        $sql = "ALTER TABLE students ADD COLUMN photo VARCHAR(255) DEFAULT NULL";
        if ($conn->query($sql) === TRUE) {
            echo "Photo column added successfully!";
        } else {
            echo "Error adding column: " . $conn->error;
        }
    } else {
        echo "Photo column already exists!";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Fix</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Database Column Fix</h2>
    <p>Run this script to add the missing 'photo' column to the students table.</p>
    <p><a href="index.php">← Back to Dashboard</a></p>
</body>
</html>
