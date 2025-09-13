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
    if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No backup file uploaded']);
        exit;
    }
    
    $uploaded_file = $_FILES['backup_file'];
    
    // Validate file extension
    $file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
    if ($file_extension !== 'sql') {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only SQL files are allowed.']);
        exit;
    }
    
    // Move uploaded file to temporary location
    $temp_dir = '../temp/';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }
    
    $temp_file = $temp_dir . 'restore_' . uniqid() . '.sql';
    if (!move_uploaded_file($uploaded_file['tmp_name'], $temp_file)) {
        echo json_encode(['success' => false, 'message' => 'Failed to process backup file']);
        exit;
    }
    
    $mysqli = get_db_connection();
    $db_name = $mysqli->query("SELECT DATABASE()")->fetch_row()[0];
    
    // Get database connection info
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    // Create mysql restore command for Windows XAMPP
    $mysql_path = 'C:\\xampp\\mysql\\bin\\mysql.exe';
    if (!file_exists($mysql_path)) {
        // Fallback to system PATH
        $mysql_path = 'mysql';
    }
    
    $command = "\"{$mysql_path}\" --host={$host} --user={$username}";
    if (!empty($password)) {
        $command .= " --password={$password}";
    }
    $command .= " {$db_name} < \"{$temp_file}\"";
    
    // Execute restore command
    $output = [];
    $return_code = 0;
    exec($command, $output, $return_code);
    
    // Clean up temporary file
    unlink($temp_file);
    
    if ($return_code === 0) {
        echo json_encode(['success' => true, 'message' => 'Database restored successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to restore database']);
    }
    
} catch (Exception $e) {
    error_log('Backup restore error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while restoring backup']);
}
?>
