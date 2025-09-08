<?php
// Prevent any output before JSON
ob_start();

require_once 'queries.php';

// Clear any previous output and set headers
ob_clean();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Get SMS delivery statistics
    $stats = get_sms_delivery_stats();
    $chart_data = get_sms_delivery_chart_data();
    
    // Combine the data
    $response = [
        'success' => true,
        'stats' => $stats,
        'chart' => $chart_data
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'stats' => [
            'total' => 0, 
            'delivered' => 0, 
            'pending' => 0, 
            'failed' => 0, 
            'delivery_rate' => 0
        ],
        'chart' => [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'rates' => [0, 0, 0, 0, 0, 0, 0]
        ]
    ]);
}
exit;
