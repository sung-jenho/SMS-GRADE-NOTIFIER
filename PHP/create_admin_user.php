<?php
require_once __DIR__ . '/db.php';

try {
    $mysqli = get_db_connection();
    
    // Check if user with email exists
    $stmt = $mysqli->prepare('SELECT id, username FROM users WHERE email = ?');
    $email = 'ctucc@edu.ph';
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "User with email ctucc@edu.ph already exists!\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Email: ctucc@edu.ph\n";
        echo "Password: ctuccadmin\n";
    } else {
        // Create user with your credentials
        $username = 'ctucc@edu.ph'; // Using email as username
        $password_hash = password_hash('ctuccadmin', PASSWORD_DEFAULT);
        $full_name = 'CTUCC Administrator';
        $email = 'ctucc@edu.ph';
        
        $stmt = $mysqli->prepare('INSERT INTO users (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $password_hash, $full_name, $email);
        
        if ($stmt->execute()) {
            echo "User created successfully!\n";
            echo "Username: ctucc@edu.ph\n";
            echo "Password: ctuccadmin\n";
            echo "Full Name: CTUCC Administrator\n";
            echo "Email: ctucc@edu.ph\n";
            echo "\nYou can now log in to the dashboard!\n";
        } else {
            echo "Error creating user: " . $stmt->error . "\n";
        }
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
