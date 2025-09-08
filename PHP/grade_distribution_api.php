<?php
// Prevent any output before JSON
ob_start();

require_once 'queries.php';

// Clear any previous output and set headers
ob_clean();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Get subject-based grade distribution data
    $distribution = get_subject_grade_distribution();
    
    // Debug: Also get all subjects to see what's available
    $mysqli = get_db_connection();
    $all_subjects_sql = "SELECT subject_code, subject_title FROM subjects ORDER BY subject_code";
    $all_subjects_result = $mysqli->query($all_subjects_sql);
    $all_subjects = $all_subjects_result ? $all_subjects_result->fetch_all(MYSQLI_ASSOC) : [];
    
    // Debug: Get all grades to see what exists
    $all_grades_sql = "SELECT s.subject_code, COUNT(*) as count FROM grades g JOIN subjects s ON g.subject_id = s.id GROUP BY s.subject_code";
    $all_grades_result = $mysqli->query($all_grades_sql);
    $all_grades = $all_grades_result ? $all_grades_result->fetch_all(MYSQLI_ASSOC) : [];
    
    if (empty($distribution)) {
        // No data available
        echo json_encode([
            'success' => true,
            'labels' => [],
            'data' => [],
            'colors' => [],
            'hasData' => false,
            'debug_all_subjects' => $all_subjects,
            'debug_all_grades' => $all_grades,
            'debug_message' => 'No CM subjects found with grades'
        ]);
    } else {
        // Prepare data for Chart.js
        $labels = [];
        $data = [];
        $colors = [];
        
        foreach ($distribution as $subject) {
            $labels[] = $subject['label'];
            $data[] = $subject['count'];
            $colors[] = $subject['color'];
        }
        
        $response = [
            'success' => true,
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
            'hasData' => true,
            'subjects' => $distribution // Include full subject info for tooltips
        ];
        
        echo json_encode($response);
    }
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'labels' => [],
        'data' => [],
        'colors' => [],
        'hasData' => false
    ]);
}
exit;
