<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';

try {
    $mysqli = get_db_connection();
    
    echo "<h3>Fixing Database Schema</h3>";
    
    // Check if profile_picture column exists
    $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($result->num_rows == 0) {
        echo "<p>Adding profile_picture column to users table...</p>";
        $mysqli->query("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL AFTER email");
        echo "<p>✓ Profile picture column added</p>";
    } else {
        echo "<p>✓ Profile picture column already exists</p>";
    }
    
    // Check if settings table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'settings'");
    if ($result->num_rows == 0) {
        echo "<p>Creating settings table...</p>";
        $mysqli->query("CREATE TABLE settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Insert default settings
        $mysqli->query("INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES
        ('timezone', 'Asia/Manila', 'System timezone'),
        ('date_format', 'Y-m-d H:i:s', 'Default date format'),
        ('backup_retention_days', '30', 'Number of days to keep backup files'),
        ('auto_backup_enabled', '1', 'Enable automatic database backups')");
        
        echo "<p>✓ Settings table created with default values</p>";
    } else {
        echo "<p>✓ Settings table already exists</p>";
    }
    
    // Check if sms_templates table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'sms_templates'");
    if ($result->num_rows == 0) {
        echo "<p>Creating SMS templates table...</p>";
        $mysqli->query("CREATE TABLE sms_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            template_name VARCHAR(100) NOT NULL,
            grade_range VARCHAR(20) NOT NULL,
            message_template TEXT NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Insert default templates
        $mysqli->query("INSERT IGNORE INTO sms_templates (template_name, grade_range, message_template) VALUES
        ('Excellent Grade', '1.0-1.5', 'Hi! Your child {student_name} received an EXCELLENT grade of {grade} in {subject}. Keep up the great work! - CTUCC'),
        ('Good Grade', '1.6-2.0', 'Hi! Your child {student_name} received a GOOD grade of {grade} in {subject}. Well done! - CTUCC'),
        ('Satisfactory Grade', '2.1-2.5', 'Hi! Your child {student_name} received a SATISFACTORY grade of {grade} in {subject}. - CTUCC'),
        ('Needs Improvement', '2.6-3.0', 'Hi! Your child {student_name} received a grade of {grade} in {subject}. Please encourage more study time. - CTUCC'),
        ('Poor Grade', '3.1-5.0', 'Hi! Your child {student_name} received a grade of {grade} in {subject}. Please contact the teacher for support. - CTUCC')");
        
        echo "<p>✓ SMS templates table created with default templates</p>";
    } else {
        echo "<p>✓ SMS templates table already exists</p>";
    }
    
    echo "<p><strong>Database schema updated successfully!</strong></p>";
    echo "<p>You can now try logging in again with:</p>";
    echo "<p>Username: ctucc@edu.ph</p>";
    echo "<p>Password: ctuccadmin</p>";
    
} catch (Exception $e) {
    echo "<p>✗ Error: " . $e->getMessage() . "</p>";
}
?>
