<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

function checkDatabaseConnection() {
    try {
        // Use your existing database connection function
        $conn = get_db_connection();
        
        // Test with a simple query
        $result = $conn->query("SELECT 1");
        if (!$result) {
            return [
                'status' => 'disconnected',
                'message' => 'Query test failed: ' . $conn->error,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
        
        return [
            'status' => 'connected',
            'message' => 'Database connection successful',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
    } catch (Exception $e) {
        return [
            'status' => 'disconnected',
            'message' => 'Exception: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dbStatus = checkDatabaseConnection();
    echo json_encode($dbStatus);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
