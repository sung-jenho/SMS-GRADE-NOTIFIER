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
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['filename'])) {
        echo json_encode(['success' => false, 'message' => 'No filename specified']);
        exit;
    }
    
    $filename = $input['filename'];
    $backup_dir = '../backups/';
    $file_path = $backup_dir . $filename;
    
    // Validate filename to prevent directory traversal
    if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
        echo json_encode(['success' => false, 'message' => 'Invalid filename']);
        exit;
    }
    
    // Check if file exists and is a backup file
    if (!file_exists($file_path) || !preg_match('/^backup_.*\.sql$/', $filename)) {
        echo json_encode(['success' => false, 'message' => 'Backup file not found']);
        exit;
    }
    
    // Delete the file
    if (unlink($file_path)) {
        echo json_encode(['success' => true, 'message' => 'Backup file deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete backup file']);
    }
    
} catch (Exception $e) {
    error_log('Delete backup error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting backup']);
}
?>
