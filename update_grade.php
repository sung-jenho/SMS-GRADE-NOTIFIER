<?php
// update_grade.php - Handles grade updates and sends SMS notification via Telesign

// CONFIG: Update these with your actual Telesign credentials
define('TELESIGN_CUSTOMER_ID', 'YOUR_CUSTOMER_ID');
define('TELESIGN_API_KEY', 'YOUR_API_KEY');
define('TELESIGN_SMS_URL', 'https://rest-api.telesign.com/v1/messaging');

$mysqli = new mysqli("localhost", "root", "", "sms_grades");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// Get POST data
$student_id = $_POST['student_id'];
$subject_id = $_POST['subject_id'];
$grade = $_POST['grade'];

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

// Fetch student and subject info
$student = $mysqli->query("SELECT name, phone_number FROM students WHERE id = $student_id")->fetch_assoc();
$subject = $mysqli->query("SELECT subject_code, subject_title FROM subjects WHERE id = $subject_id")->fetch_assoc();

// Compose SMS
$sms_message = "Hello {$student['name']}, your grade for {$subject['subject_code']} ({$subject['subject_title']}) is now: $grade.";
$phone = $student['phone_number'];

// Send SMS via Telesign
function send_sms_telesign($phone, $message) {
    $customer_id = TELESIGN_CUSTOMER_ID;
    $api_key = TELESIGN_API_KEY;
    $url = TELESIGN_SMS_URL . "/sms";

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

// Uncomment below to enable SMS sending (fill in your credentials above)
// send_sms_telesign($phone, $sms_message);

header('Location: index.php');
exit;
