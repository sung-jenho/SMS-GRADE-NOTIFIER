<?php
// update_existing_grade.php - Handles updating existing grades by grade ID with SMS logging
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// CONFIG: Use environment variables for Telesign
define('TELESIGN_CUSTOMER_ID', getenv('TELESIGN_CUSTOMER_ID') ?: '');
define('TELESIGN_API_KEY', getenv('TELESIGN_API_KEY') ?: '');
define('TELESIGN_SMS_URL', 'https://rest-api.telesign.com/v1/messaging');

header('Content-Type: application/json');

try {
    $mysqli = get_db_connection();

    // Get and validate POST data
    $grade_id = filter_input(INPUT_POST, 'grade_id', FILTER_VALIDATE_INT);
    $new_grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_FLOAT);
    
    if ($grade_id === false || $new_grade === false || $new_grade < 0 || $new_grade > 5) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    // Get grade details with student and subject info
    $stmt = $mysqli->prepare("
        SELECT g.student_id, g.subject_id, s.name as student_name, s.phone_number, 
               sub.subject_code, sub.subject_title 
        FROM grades g 
        JOIN students s ON g.student_id = s.id 
        JOIN subjects sub ON g.subject_id = sub.id 
        WHERE g.id = ?
    ");
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Grade not found']);
        exit;
    }
    
    $grade_data = $result->fetch_assoc();
    $stmt->close();

    // Update the grade
    $stmt = $mysqli->prepare("UPDATE grades SET grade = ?, last_updated = NOW() WHERE id = ?");
    $stmt->bind_param("di", $new_grade, $grade_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        
        // Send SMS notification
        $sms_message = "Hello {$grade_data['student_name']}, your grade for {$grade_data['subject_code']} ({$grade_data['subject_title']}) has been updated to: $new_grade.";
        $phone = $grade_data['phone_number'];
        
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
        $grade_str = (string)$new_grade;
        $stmt_log->bind_param('iisss', $grade_data['student_id'], $grade_data['subject_id'], $grade_str, $phone, $status);
        $stmt_log->execute();
        $stmt_log->close();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Grade updated successfully and SMS notification logged',
            'new_grade' => $new_grade,
            'sms_status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or database error']);
    }
    
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
