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

if (!isset($_POST['grade_id']) || !is_numeric($_POST['grade_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid grade ID']);
    exit;
}

$grade_id = (int)$_POST['grade_id'];

// Debug logging
error_log("Attempting to delete grade ID: " . $grade_id);

try {
    // First, check if the grade exists
    $mysqli = get_db_connection();
    $check_stmt = $mysqli->prepare('SELECT id FROM grades WHERE id = ?');
    $check_stmt->bind_param('i', $grade_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Grade not found']);
        exit;
    }
    
    // Now delete the grade
    $success = delete_grade($grade_id);
    
    if ($success) {
        error_log("Grade deleted successfully: " . $grade_id);
        echo json_encode(['success' => true, 'message' => 'Grade removed successfully']);
    } else {
        error_log("Failed to delete grade: " . $grade_id);
        echo json_encode(['success' => false, 'message' => 'Failed to remove grade']);
    }
} catch (Exception $e) {
    error_log("Database error deleting grade: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
