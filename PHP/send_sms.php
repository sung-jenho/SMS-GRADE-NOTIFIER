<?php
// send_sms.php
header('Content-Type: application/json');

try {
    require_once 'db.php'; // adjust path if needed
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit;
}

if (!isset($data['log_id'], $data['phone'], $data['message'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit;
}

$log_id = $data['log_id'];
$phone = $data['phone'];
$message = $data['message'];

// --- SMS Sending Logic (Replace with real gateway integration) ---
function send_sms($to, $msg) {
    // Simulate SMS sending
    // TODO: Replace with actual SMS gateway API call
    if (preg_match('/^[0-9]{10,15}$/', $to)) {
        // Simulate success
        return true;
    }
    return false;
}

try {
    $sent = send_sms($phone, $message);

    if ($sent) {
        // Update status in DB
        $stmt = $pdo->prepare('UPDATE sms_logs SET status = ? WHERE id = ?');
        $stmt->execute(['Sent', $log_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send SMS.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
