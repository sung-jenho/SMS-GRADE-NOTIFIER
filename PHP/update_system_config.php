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
    
    $settings_to_update = [
        'timezone' => $_POST['timezone'] ?? 'Asia/Manila',
        'date_format' => $_POST['date_format'] ?? 'Y-m-d H:i:s',
        'backup_retention_days' => $_POST['backup_retention_days'] ?? '30',
        'auto_backup_enabled' => isset($_POST['auto_backup_enabled']) ? '1' : '0'
    ];
    
    $success_count = 0;
    
    foreach ($settings_to_update as $key => $value) {
        $stmt = $mysqli->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $stmt->bind_param('ss', $key, $value);
        
        if ($stmt->execute()) {
            $success_count++;
        }
        $stmt->close();
    }
    
    if ($success_count === count($settings_to_update)) {
        echo json_encode(['success' => true, 'message' => 'System configuration updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Some settings failed to update']);
    }
    
} catch (Exception $e) {
    error_log('System config update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating configuration']);
}
?>
