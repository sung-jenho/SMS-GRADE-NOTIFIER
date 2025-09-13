<?php
session_start();
require_once __DIR__ . '/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $mysqli = get_db_connection();
    
    // Get database name from connection
    $db_name = $mysqli->query("SELECT DATABASE()")->fetch_row()[0];
    
    // Create backup directory if it doesn't exist
    $backup_dir = '../backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    // Generate backup filename
    $timestamp = date('Y-m-d_H-i-s');
    $backup_filename = "backup_{$db_name}_{$timestamp}.sql";
    $backup_path = $backup_dir . $backup_filename;
    
    // Get database connection info
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    // Create mysqldump command for Windows XAMPP
    $mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    if (!file_exists($mysqldump_path)) {
        // Fallback to system PATH
        $mysqldump_path = 'mysqldump';
    }
    
    $command = "\"{$mysqldump_path}\" --host={$host} --user={$username}";
    if (!empty($password)) {
        $command .= " --password={$password}";
    }
    $command .= " --single-transaction --routines --triggers {$db_name} > \"{$backup_path}\"";
    
    // Execute backup command
    $output = [];
    $return_code = 0;
    exec($command . ' 2>&1', $output, $return_code);
    
    // Log command and output for debugging
    error_log("Backup command: " . $command);
    error_log("Return code: " . $return_code);
    error_log("Output: " . implode("\n", $output));
    
    // Check if backup was created successfully
    if (file_exists($backup_path) && filesize($backup_path) > 0) {
        // Clean up old backups based on retention policy
        $retention_days = 30; // Default retention
        
        // Get retention setting from database
        $stmt = $mysqli->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $key = 'backup_retention_days';
        $stmt->bind_param('s', $key);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $retention_days = (int)$row['setting_value'];
        }
        $stmt->close();
        
        // Remove old backups
        $cutoff_time = time() - ($retention_days * 24 * 60 * 60);
        $backup_files = glob($backup_dir . 'backup_*.sql');
        
        foreach ($backup_files as $file) {
            if (filemtime($file) < $cutoff_time) {
                unlink($file);
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Database backup created successfully',
            'filename' => $backup_filename,
            'size' => formatBytes(filesize($backup_path))
        ]);
    } else {
        $error_message = 'Failed to create database backup';
        if (!empty($output)) {
            $error_message .= ': ' . implode(', ', $output);
        }
        echo json_encode(['success' => false, 'message' => $error_message]);
    }
    
} catch (Exception $e) {
    error_log('Backup creation error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while creating backup']);
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
