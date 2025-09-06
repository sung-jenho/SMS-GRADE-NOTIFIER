<?php
require_once 'queries.php';

header('Content-Type: application/json');

try {
    // Get grade distribution data
    $distribution = get_grade_distribution();
    
    // Prepare data for Chart.js
    $response = [
        'success' => true,
        'labels' => ['A', 'B', 'C', 'D', 'F'],
        'data' => [
            $distribution['A'],
            $distribution['B'], 
            $distribution['C'],
            $distribution['D'],
            $distribution['F']
        ],
        'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444']
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    // Return error response with empty data
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'labels' => ['A', 'B', 'C', 'D', 'F'],
        'data' => [0, 0, 0, 0, 0],
        'colors' => ['#22c55e', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444']
    ]);
}
?>
