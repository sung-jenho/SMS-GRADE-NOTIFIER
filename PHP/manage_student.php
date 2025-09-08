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
    if ($action === 'create') {
        // Validate required fields
        $required_fields = ['student_number', 'name', 'year_level', 'phone_number'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                exit;
            }
        }
        
        // Course is optional, so handle it separately
        if (!isset($_POST['course'])) {
            $_POST['course'] = '';
        }
        
        $studentNumber = trim($_POST['student_number']);
        $name = trim($_POST['name']);
        $course = trim($_POST['course']);
        $yearLevel = (int)$_POST['year_level'];
        $phoneNumber = trim($_POST['phone_number']);
        
        // Validate year level
        if ($yearLevel < 1 || $yearLevel > 10) {
            echo json_encode(['success' => false, 'message' => 'Year level must be between 1 and 10']);
            exit;
        }
        
        // Validate phone number format (basic validation)
        if (!preg_match('/^[\d\-\+\(\)\s]+$/', $phoneNumber)) {
            echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
            exit;
        }
        $success = create_student($studentNumber, $name, $course, $yearLevel, $phoneNumber);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Student added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Student number already exists or failed to add student']);
        }
        
    } elseif ($action === 'update') {
        // Validate required fields
        if (!isset($_POST['student_id']) || !is_numeric($_POST['student_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
            exit;
        }
        
        $required_fields = ['student_number', 'name', 'year_level', 'phone_number'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                exit;
            }
        }
        
        // Course is optional, so handle it separately
        if (!isset($_POST['course'])) {
            $_POST['course'] = '';
        }
        
        $studentId = (int)$_POST['student_id'];
        $studentNumber = trim($_POST['student_number']);
        $name = trim($_POST['name']);
        $course = trim($_POST['course']);
        $yearLevel = (int)$_POST['year_level'];
        $phoneNumber = trim($_POST['phone_number']);
        
        // Validate year level
        if ($yearLevel < 1 || $yearLevel > 10) {
            echo json_encode(['success' => false, 'message' => 'Year level must be between 1 and 10']);
            exit;
        }
        
        // Validate phone number format
        if (!preg_match('/^[\d\-\+\(\)\s]+$/', $phoneNumber)) {
            echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
            exit;
        }
        
        $success = update_student($studentId, $studentNumber, $name, $course, $yearLevel, $phoneNumber);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Student number already exists or failed to update student']);
        }
        
    } elseif ($action === 'delete') {
        if (!isset($_POST['student_id']) || !is_numeric($_POST['student_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
            exit;
        }
        
        $studentId = (int)$_POST['student_id'];
        $success = delete_student($studentId);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cannot delete student with existing grades or SMS logs']);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Database error managing student: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
