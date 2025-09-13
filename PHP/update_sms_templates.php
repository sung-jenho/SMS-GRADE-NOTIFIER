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
    
    if (!isset($input['templates']) || !is_array($input['templates'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid templates data']);
        exit;
    }
    
    $mysqli = get_db_connection();
    $success_count = 0;
    
    foreach ($input['templates'] as $template) {
        if (!isset($template['id'], $template['message_template'])) {
            continue;
        }
        
        $is_active = isset($template['is_active']) && $template['is_active'] ? 1 : 0;
        
        $stmt = $mysqli->prepare('UPDATE sms_templates SET message_template = ?, is_active = ? WHERE id = ?');
        $stmt->bind_param('sii', $template['message_template'], $is_active, $template['id']);
        
        if ($stmt->execute()) {
            $success_count++;
        }
        $stmt->close();
    }
    
    if ($success_count > 0) {
        echo json_encode(['success' => true, 'message' => 'SMS templates updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No templates were updated']);
    }
    
} catch (Exception $e) {
    error_log('SMS templates update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating templates']);
}
?>
