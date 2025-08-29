<?php
// update_grade.php - Handles grade updates and sends SMS notification via Telesign
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// CONFIG: Use environment variables for Telesign
define('TELESIGN_CUSTOMER_ID', getenv('TELESIGN_CUSTOMER_ID') ?: '');
define('TELESIGN_API_KEY', getenv('TELESIGN_API_KEY') ?: '');
define('TELESIGN_SMS_URL', 'https://rest-api.telesign.com/v1/messaging');

$mysqli = get_db_connection();

// Get and validate POST data
$student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
$subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
$grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_FLOAT);
if ($student_id === false || $subject_id === false || $grade === false || $grade < 0 || $grade > 5) {
    http_response_code(400);
    exit('Invalid input');
}

// Check if grade exists for this student+subject
$stmt = $mysqli->prepare("SELECT id FROM grades WHERE student_id=? AND subject_id=?");
$stmt->bind_param("ii", $student_id, $subject_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    // Update existing
    $stmt_u = $mysqli->prepare("UPDATE grades SET grade=?, last_updated=NOW() WHERE student_id=? AND subject_id=?");
    $stmt_u->bind_param("sii", $grade, $student_id, $subject_id);
    $stmt_u->execute();
    $stmt_u->close();
} else {
    // Insert new
    $stmt_i = $mysqli->prepare("INSERT INTO grades (student_id, subject_id, grade, last_updated) VALUES (?, ?, ?, NOW())");
    $stmt_i->bind_param("iis", $student_id, $subject_id, $grade);
    $stmt_i->execute();
    $stmt_i->close();
}
$stmt->close();

// Fetch student and subject info securely
$stmt = $mysqli->prepare('SELECT name, phone_number FROM students WHERE id = ?');
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $mysqli->prepare('SELECT subject_code, subject_title FROM subjects WHERE id = ?');
$stmt->bind_param('i', $subject_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Compose SMS
$sms_message = "Hello {$student['name']}, your grade for {$subject['subject_code']} ({$subject['subject_title']}) is now: $grade.";
$phone = $student['phone_number'];

// Send SMS via Telesign
function send_sms_telesign($phone, $message) {
    $customer_id = TELESIGN_CUSTOMER_ID;
    $api_key = TELESIGN_API_KEY;
    $url = TELESIGN_SMS_URL . "/sms";
    if (!$customer_id || !$api_key) { return false; }

    $data = array(
        'phone_number' => $phone,
        'message' => $message,
        'message_type' => 'ARN'
    );
    $json = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$customer_id:$api_key");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpcode == 200;
}

// Attempt to send SMS if credentials exist; otherwise treat as Pending
$status = 'Pending';
if (TELESIGN_CUSTOMER_ID && TELESIGN_API_KEY) {
    $ok = send_sms_telesign($phone, $sms_message);
    $status = $ok ? 'Sent' : 'Failed';
}

// Log to sms_logs table
$stmt_log = $mysqli->prepare('INSERT INTO sms_logs (student_id, subject_id, grade_snapshot, parent_phone, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
$grade_str = (string)$grade;
$stmt_log->bind_param('iisss', $student_id, $subject_id, $grade_str, $phone, $status);
$stmt_log->execute();
$stmt_log->close();

header('Location: index.php');
exit;
