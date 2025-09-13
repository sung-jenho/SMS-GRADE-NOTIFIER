<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/queries.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$report_type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';

if (empty($report_type)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Report type is required']);
    exit;
}

try {
    $mysqli = get_db_connection();
    $data = [];
    $filename = '';
    
    switch ($report_type) {
        case 'students':
            $data = fetch_students();
            $filename = 'students_report_' . date('Y-m-d');
            break;
            
        case 'subjects':
            $data = get_all_subjects();
            $filename = 'subjects_report_' . date('Y-m-d');
            break;
            
        case 'grades':
            $data = get_grades();
            $filename = 'grades_report_' . date('Y-m-d');
            break;
            
        case 'sms_logs':
            $data = get_sms_logs();
            $filename = 'sms_logs_report_' . date('Y-m-d');
            break;
            
        case 'complete':
            // Get all data for complete report
            $students = fetch_students();
            $subjects = get_all_subjects();
            $grades = get_grades();
            $sms_logs = get_sms_logs();
            
            $data = [
                'students' => $students,
                'subjects' => $subjects,
                'grades' => $grades,
                'sms_logs' => $sms_logs
            ];
            $filename = 'complete_report_' . date('Y-m-d');
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid report type']);
            exit;
    }
    
    if ($format === 'csv') {
        exportToCSV($data, $filename, $report_type);
    } elseif ($format === 'pdf') {
        exportToPDF($data, $filename, $report_type);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid format']);
    }
    
} catch (Exception $e) {
    error_log('Export error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred during export']);
}

function exportToCSV($data, $filename, $report_type) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    if ($report_type === 'complete') {
        // Handle complete report with multiple sections
        foreach ($data as $section => $section_data) {
            fputcsv($output, [strtoupper($section) . ' REPORT']);
            fputcsv($output, []); // Empty line
            
            if (!empty($section_data)) {
                // Write headers
                fputcsv($output, array_keys($section_data[0]));
                
                // Write data
                foreach ($section_data as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fputcsv($output, []); // Empty line between sections
            fputcsv($output, []); // Another empty line
        }
    } else {
        // Handle single report type
        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
    }
    
    fclose($output);
}

function exportToPDF($data, $filename, $report_type) {
    // Simple HTML to PDF conversion
    $html = generateHTMLReport($data, $filename, $report_type);
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    
    // For now, we'll use a simple HTML response that can be printed to PDF
    // In a production environment, you might want to use a library like TCPDF or mPDF
    echo $html;
}

function generateHTMLReport($data, $filename, $report_type) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>' . ucfirst($report_type) . ' Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
            h2 { color: #666; margin-top: 30px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .report-info { margin-bottom: 30px; }
            .report-info p { margin: 5px 0; }
        </style>
    </head>
    <body>
        <div class="report-info">
            <h1>SMS Grade Notifier - ' . ucfirst(str_replace('_', ' ', $report_type)) . ' Report</h1>
            <p><strong>Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>
            <p><strong>Report Type:</strong> ' . ucfirst(str_replace('_', ' ', $report_type)) . '</p>
        </div>';
    
    if ($report_type === 'complete') {
        foreach ($data as $section => $section_data) {
            $html .= '<h2>' . ucfirst(str_replace('_', ' ', $section)) . '</h2>';
            $html .= generateTableHTML($section_data);
        }
    } else {
        $html .= generateTableHTML($data);
    }
    
    $html .= '</body></html>';
    
    return $html;
}

function generateTableHTML($data) {
    if (empty($data)) {
        return '<p>No data available.</p>';
    }
    
    $html = '<table>';
    
    // Headers
    $html .= '<thead><tr>';
    foreach (array_keys($data[0]) as $header) {
        $html .= '<th>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $header))) . '</th>';
    }
    $html .= '</tr></thead>';
    
    // Data rows
    $html .= '<tbody>';
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    
    $html .= '</table>';
    
    return $html;
}
?>
