<?php
// Test student creation functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Student Creation</h2>";

require_once 'db.php';
require_once 'queries.php';

// Test database connection
try {
    $mysqli = get_db_connection();
    echo "<p>✅ Database connection: OK</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test create_student function
echo "<h3>Testing create_student function</h3>";
$testStudentNumber = "TEST" . rand(1000, 9999);
$testName = "Test Student";
$testCourse = "BSIT";
$testYearLevel = 1;
$testPhoneNumber = "+63 912 345 6789";

echo "<p>Attempting to create student:</p>";
echo "<ul>";
echo "<li>Student Number: $testStudentNumber</li>";
echo "<li>Name: $testName</li>";
echo "<li>Course: $testCourse</li>";
echo "<li>Year Level: $testYearLevel</li>";
echo "<li>Phone Number: $testPhoneNumber</li>";
echo "</ul>";

try {
    $result = create_student($testStudentNumber, $testName, $testCourse, $testYearLevel, $testPhoneNumber);
    
    if ($result) {
        echo "<p>✅ Student created successfully!</p>";
        
        // Verify by fetching students
        $students = fetch_students();
        $found = false;
        foreach ($students as $student) {
            if ($student['student_number'] === $testStudentNumber) {
                $found = true;
                echo "<p>✅ Student found in database:</p>";
                echo "<pre>" . print_r($student, true) . "</pre>";
                break;
            }
        }
        
        if (!$found) {
            echo "<p>❌ Student not found in database after creation</p>";
        }
        
        // Clean up - delete test student
        $deleteStmt = $mysqli->prepare('DELETE FROM students WHERE student_number = ?');
        $deleteStmt->bind_param('s', $testStudentNumber);
        $deleteStmt->execute();
        echo "<p>🧹 Test student cleaned up</p>";
        
    } else {
        echo "<p>❌ Failed to create student</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error creating student: " . $e->getMessage() . "</p>";
}

// Test fetch_students function
echo "<h3>Testing fetch_students function</h3>";
try {
    $students = fetch_students();
    echo "<p>✅ fetch_students() returned " . count($students) . " students</p>";
    
    if (count($students) > 0) {
        echo "<p>Sample student data:</p>";
        echo "<pre>" . print_r($students[0], true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error fetching students: " . $e->getMessage() . "</p>";
}

// Test table structure
echo "<h3>Checking students table structure</h3>";
try {
    $result = $mysqli->query("DESCRIBE students");
    if ($result) {
        echo "<p>✅ Students table structure:</p>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error checking table structure: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Complete</h3>";
?>
