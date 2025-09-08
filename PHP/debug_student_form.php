<?php
// Debug student form submission
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Student Form Debug</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    // Test the actual manage_student.php logic
    session_start();
    $_SESSION['user_id'] = 1; // Mock authentication
    
    require_once 'db.php';
    require_once 'queries.php';
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        echo "<h3>Testing Student Creation:</h3>";
        
        // Validate required fields
        $required_fields = ['student_number', 'name', 'year_level', 'phone_number'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            echo "<p>❌ Missing required fields: " . implode(', ', $missing_fields) . "</p>";
        } else {
            echo "<p>✅ All required fields present</p>";
            
            $studentNumber = trim($_POST['student_number']);
            $name = trim($_POST['name']);
            $course = trim($_POST['course'] ?? '');
            $yearLevel = (int)$_POST['year_level'];
            $phoneNumber = trim($_POST['phone_number']);
            
            echo "<p>Processed data:</p>";
            echo "<ul>";
            echo "<li>Student Number: '$studentNumber'</li>";
            echo "<li>Name: '$name'</li>";
            echo "<li>Course: '$course'</li>";
            echo "<li>Year Level: $yearLevel</li>";
            echo "<li>Phone Number: '$phoneNumber'</li>";
            echo "</ul>";
            
            // Validate year level
            if ($yearLevel < 1 || $yearLevel > 10) {
                echo "<p>❌ Invalid year level: $yearLevel</p>";
            } else {
                echo "<p>✅ Year level valid</p>";
            }
            
            // Validate phone number format
            if (!preg_match('/^[\d\-\+\(\)\s]+$/', $phoneNumber)) {
                echo "<p>❌ Invalid phone number format: '$phoneNumber'</p>";
            } else {
                echo "<p>✅ Phone number format valid</p>";
            }
            
            // Try to create student
            try {
                $success = create_student($studentNumber, $name, $course, $yearLevel, $phoneNumber);
                
                if ($success) {
                    echo "<p>✅ Student created successfully!</p>";
                    
                    // Clean up test data
                    $mysqli = get_db_connection();
                    $deleteStmt = $mysqli->prepare('DELETE FROM students WHERE student_number = ?');
                    $deleteStmt->bind_param('s', $studentNumber);
                    $deleteStmt->execute();
                    echo "<p>🧹 Test data cleaned up</p>";
                } else {
                    echo "<p>❌ Failed to create student (possibly duplicate student number)</p>";
                }
            } catch (Exception $e) {
                echo "<p>❌ Exception during student creation: " . $e->getMessage() . "</p>";
            }
        }
    }
} else {
    // Show test form
    ?>
    <h3>Test Student Creation Form</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="create">
        
        <p>
            <label>Student Number:</label><br>
            <input type="text" name="student_number" value="TEST<?= rand(1000, 9999) ?>" required>
        </p>
        
        <p>
            <label>Name:</label><br>
            <input type="text" name="name" value="Test Student" required>
        </p>
        
        <p>
            <label>Course:</label><br>
            <input type="text" name="course" value="BSIT">
        </p>
        
        <p>
            <label>Year Level:</label><br>
            <select name="year_level" required>
                <option value="">Select Year Level</option>
                <option value="1" selected>1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
                <option value="5">5th Year</option>
            </select>
        </p>
        
        <p>
            <label>Phone Number:</label><br>
            <input type="tel" name="phone_number" value="+63 912 345 6789" required>
        </p>
        
        <p>
            <button type="submit">Test Create Student</button>
        </p>
    </form>
    <?php
}
?>
