<?php
session_start();
require_once 'queries.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$subject_id = (int)($_GET['id'] ?? 0);

if ($subject_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid subject ID']);
    exit;
}

$subject = get_subject_by_id($subject_id);

if ($subject) {
    echo json_encode(['success' => true, 'subject' => $subject]);
} else {
    echo json_encode(['success' => false, 'message' => 'Subject not found']);
}
?>
