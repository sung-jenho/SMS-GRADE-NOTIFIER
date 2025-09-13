<?php
session_start();
require_once __DIR__ . '/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit;
}

if (!isset($_GET['file'])) {
    http_response_code(400);
    echo 'No file specified';
    exit;
}

$filename = $_GET['file'];
$backup_dir = '../backups/';
$file_path = $backup_dir . $filename;

// Validate filename to prevent directory traversal
if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
    http_response_code(400);
    echo 'Invalid filename';
    exit;
}

// Check if file exists and is a backup file
if (!file_exists($file_path) || !preg_match('/^backup_.*\.sql$/', $filename)) {
    http_response_code(404);
    echo 'Backup file not found';
    exit;
}

// Set headers for file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Output file contents
readfile($file_path);
exit;
?>
