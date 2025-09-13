<?php
require_once __DIR__ . '/db.php';

try {
    $mysqli = get_db_connection();
    
    // Get all users in the database
    $result = $mysqli->query('SELECT id, username, email, full_name FROM users');
    
    echo "<h3>Current Users in Database:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if there are multiple users with different usernames but same email pattern
    $duplicate_check = $mysqli->query("SELECT username, email, COUNT(*) as count FROM users GROUP BY username, email HAVING count > 1");
    if ($duplicate_check->num_rows > 0) {
        echo "<h3>Duplicate Users Found:</h3>";
        while ($dup = $duplicate_check->fetch_assoc()) {
            echo "<p>Username: " . htmlspecialchars($dup['username']) . " | Email: " . htmlspecialchars($dup['email']) . " | Count: " . $dup['count'] . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
