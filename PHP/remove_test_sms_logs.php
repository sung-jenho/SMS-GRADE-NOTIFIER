<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/queries.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    $mysqli = get_db_connection();
    
    if ($action === 'clear_test') {
        // Clear only test data based on patterns
        $success = clear_test_sms_logs();
        
        if ($success) {
            // Get count of remaining logs
            $count_result = $mysqli->query('SELECT COUNT(*) as count FROM sms_logs');
            $remaining_count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Test SMS logs removed successfully',
                'remaining_logs' => $remaining_count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove test SMS logs']);
        }
        
    } elseif ($action === 'clear_all') {
        // Clear all SMS logs
        $success = clear_all_sms_logs();
        
        if ($success) {
            echo json_encode([
                'success' => true, 
                'message' => 'All SMS logs removed successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove SMS logs']);
        }
        
    } elseif ($action === 'delete_single') {
        // Delete a single SMS log
        if (!isset($_POST['sms_log_id']) || !is_numeric($_POST['sms_log_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid SMS log ID']);
            exit;
        }
        
        $sms_log_id = (int)$_POST['sms_log_id'];
        $success = delete_sms_log($sms_log_id);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'SMS log removed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove SMS log']);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Database error removing SMS logs: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
