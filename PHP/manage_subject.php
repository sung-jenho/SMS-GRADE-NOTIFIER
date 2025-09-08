<?php
session_start();
require_once 'queries.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? 'add';

if ($action === 'add') {
    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_title = trim($_POST['subject_title'] ?? '');
    $units = !empty($_POST['units']) ? (int)$_POST['units'] : null;
    $schedule = trim($_POST['schedule'] ?? '') ?: null;
    $days = trim($_POST['days'] ?? '') ?: null;
    $room = trim($_POST['room'] ?? '') ?: null;
    
    if (empty($subject_code) || empty($subject_title)) {
        echo json_encode(['success' => false, 'message' => 'Subject code and title are required']);
        exit;
    }
    
    $result = add_subject($subject_code, $subject_title, $units, $schedule, $days, $room);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Subject added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add subject. Subject code may already exist.']);
    }
} elseif ($action === 'edit') {
    $subject_id = (int)($_POST['id'] ?? 0);
    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_title = trim($_POST['subject_title'] ?? '');
    $units = !empty($_POST['units']) ? (int)$_POST['units'] : null;
    $schedule = trim($_POST['schedule'] ?? '') ?: null;
    $days = trim($_POST['days'] ?? '') ?: null;
    $room = trim($_POST['room'] ?? '') ?: null;
    
    if ($subject_id <= 0 || empty($subject_code) || empty($subject_title)) {
        echo json_encode(['success' => false, 'message' => 'Subject ID, code and title are required']);
        exit;
    }
    
    $result = update_subject($subject_id, $subject_code, $subject_title, $units, $schedule, $days, $room);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Subject updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update subject. Subject code may already exist.']);
    }
} elseif ($action === 'delete') {
    $subject_id = (int)($_POST['id'] ?? 0);
    
    if ($subject_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid subject ID']);
        exit;
    }
    
    $result = delete_subject($subject_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Subject deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete subject. Subject may have existing grades.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
