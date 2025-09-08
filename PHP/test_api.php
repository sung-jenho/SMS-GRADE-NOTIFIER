<?php
// Simple test to debug API issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing API...\n";

// Test database connection
require_once 'db.php';
try {
    $mysqli = get_db_connection();
    echo "Database connection: OK\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test queries function
require_once 'queries.php';
try {
    $distribution = get_subject_grade_distribution();
    echo "Grade distribution function: OK\n";
    echo "Data count: " . count($distribution) . "\n";
    print_r($distribution);
} catch (Exception $e) {
    echo "Grade distribution function failed: " . $e->getMessage() . "\n";
}

// Test subjects query
$sql = "SELECT subject_code, subject_title FROM subjects ORDER BY subject_code";
$result = $mysqli->query($sql);
$subjects = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
echo "All subjects:\n";
print_r($subjects);

// Test grades query
$sql = "SELECT s.subject_code, COUNT(*) as count FROM grades g JOIN subjects s ON g.subject_id = s.id GROUP BY s.subject_code";
$result = $mysqli->query($sql);
$grades = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
echo "Grades by subject:\n";
print_r($grades);
?>
