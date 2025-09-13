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
    $backup_dir = '../backups/';
    $backups = [];
    
    if (is_dir($backup_dir)) {
        $backup_files = glob($backup_dir . 'backup_*.sql');
        
        foreach ($backup_files as $file) {
            $filename = basename($file);
            $backups[] = [
                'filename' => $filename,
                'size' => formatBytes(filesize($file)),
                'created' => date('M j, Y g:i A', filemtime($file)),
                'timestamp' => filemtime($file)
            ];
        }
        
        // Sort by timestamp (newest first)
        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Remove timestamp from response
        foreach ($backups as &$backup) {
            unset($backup['timestamp']);
        }
    }
    
    echo json_encode(['success' => true, 'backups' => $backups]);
    
} catch (Exception $e) {
    error_log('List backups error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while listing backups']);
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
