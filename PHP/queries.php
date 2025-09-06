<?php
require_once __DIR__ . '/db.php';

function find_user_by_username(string $username): ?array {
    $mysqli = get_db_connection();
    $stmt = $mysqli->prepare('SELECT id, username, password_hash, full_name, email FROM users WHERE username = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    return $user ?: null;
}


function fetch_students(): array {
    $mysqli = get_db_connection();
    $result = $mysqli->query('SELECT * FROM students ORDER BY name ASC');
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function create_student(string $studentNumber, string $name, string $course, int $yearLevel, string $phoneNumber): bool {
    $mysqli = get_db_connection();
    
    // Check if student number already exists
    $check_stmt = $mysqli->prepare('SELECT id FROM students WHERE student_number = ?');
    $check_stmt->bind_param('s', $studentNumber);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $check_stmt->close();
        return false; // Student number already exists
    }
    $check_stmt->close();
    
    // Insert new student
    $stmt = $mysqli->prepare('INSERT INTO students (student_number, name, course, year_level, phone_number) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('sssis', $studentNumber, $name, $course, $yearLevel, $phoneNumber);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function update_student(int $studentId, string $studentNumber, string $name, string $course, int $yearLevel, string $phoneNumber): bool {
    $mysqli = get_db_connection();
    
    // Check if student number already exists for other students
    $check_stmt = $mysqli->prepare('SELECT id FROM students WHERE student_number = ? AND id != ?');
    $check_stmt->bind_param('si', $studentNumber, $studentId);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $check_stmt->close();
        return false; // Student number already exists for another student
    }
    $check_stmt->close();
    
    // Update student
    $stmt = $mysqli->prepare('UPDATE students SET student_number = ?, name = ?, course = ?, year_level = ?, phone_number = ? WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('sssisi', $studentNumber, $name, $course, $yearLevel, $phoneNumber, $studentId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function delete_student(int $studentId): bool {
    $mysqli = get_db_connection();
    
    // Check if student has grades or SMS logs
    $check_stmt = $mysqli->prepare('SELECT COUNT(*) as count FROM grades WHERE student_id = ?');
    $check_stmt->bind_param('i', $studentId);
    $check_stmt->execute();
    $grades_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($grades_result['count'] > 0) {
        return false; // Cannot delete student with existing grades
    }
    
    // Check SMS logs
    $check_stmt = $mysqli->prepare('SELECT COUNT(*) as count FROM sms_logs WHERE student_id = ?');
    $check_stmt->bind_param('i', $studentId);
    $check_stmt->execute();
    $sms_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($sms_result['count'] > 0) {
        return false; // Cannot delete student with existing SMS logs
    }
    
    // Delete student
    $stmt = $mysqli->prepare('DELETE FROM students WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $studentId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function fetch_subjects(): array {
    $mysqli = get_db_connection();
    $result = $mysqli->query('SELECT * FROM subjects');
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function fetch_grades(): array {
    $mysqli = get_db_connection();
    $sql = 'SELECT grades.id, students.name, students.student_number, subjects.subject_code, subjects.subject_title, grades.grade, grades.last_updated
            FROM grades
            JOIN students ON grades.student_id = students.id
            JOIN subjects ON grades.subject_id = subjects.id
            ORDER BY grades.last_updated DESC';
    $result = $mysqli->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function fetch_sms_logs(int $limit = 50): array {
  $mysqli = get_db_connection();
  $sql = 'SELECT l.id,
                 s.name AS student_name,
                 s.phone_number AS parent_phone,
                 sub.subject_title,
                 sub.subject_code,
                 l.grade_snapshot AS grade,
                 l.status,
                 l.created_at
          FROM sms_logs l
          JOIN students s ON l.student_id = s.id
          JOIN subjects sub ON l.subject_id = sub.id
          ORDER BY l.created_at DESC
          LIMIT ' . intval($limit);
  try {
    $result = $mysqli->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
  } catch (mysqli_sql_exception $e) {
    // Error 1146: table doesn't exist. Return empty list gracefully.
    if (strpos($e->getMessage(), '1146') !== false || stripos($e->getMessage(), 'doesn\'t exist') !== false) {
      return [];
    }
    throw $e;
  }
}

function delete_grade(int $grade_id): bool {
  $mysqli = get_db_connection();
  $stmt = $mysqli->prepare('DELETE FROM grades WHERE id = ?');
  if (!$stmt) {
    return false;
  }
  $stmt->bind_param('i', $grade_id);
  $ok = $stmt->execute();
  $stmt->close();
  return $ok;
}

function delete_sms_log(int $sms_log_id): bool {
  $mysqli = get_db_connection();
  $stmt = $mysqli->prepare('DELETE FROM sms_logs WHERE id = ?');
  if (!$stmt) {
    return false;
  }
  $stmt->bind_param('i', $sms_log_id);
  $ok = $stmt->execute();
  $stmt->close();
  return $ok;
}

function clear_test_sms_logs(): bool {
  $mysqli = get_db_connection();
  // Delete SMS logs that appear to be test data based on patterns:
  // 1. Future dates (after current date)
  // 2. Phone numbers with +1-555- pattern (test numbers)
  // 3. Grade snapshots that are just numbers (like "5")
  $stmt = $mysqli->prepare('DELETE FROM sms_logs WHERE 
    created_at > NOW() OR 
    parent_phone LIKE "+1-555-%" OR 
    grade_snapshot REGEXP "^[0-9]+$"');
  if (!$stmt) {
    return false;
  }
  $ok = $stmt->execute();
  $stmt->close();
  return $ok;
}

function clear_all_sms_logs(): bool {
  $mysqli = get_db_connection();
  $stmt = $mysqli->prepare('DELETE FROM sms_logs');
  if (!$stmt) {
    return false;
  }
  $ok = $stmt->execute();
  $stmt->close();
  return $ok;
}

// SMS Delivery Statistics Functions
function get_sms_delivery_stats(): array {
  $mysqli = get_db_connection();
  
  try {
    // Get overall counts
    $sql = "SELECT 
              COUNT(*) as total,
              SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as delivered,
              SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
              SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM sms_logs";
    
    $result = $mysqli->query($sql);
    $stats = $result ? $result->fetch_assoc() : [
      'total' => 0, 'delivered' => 0, 'pending' => 0, 'failed' => 0
    ];
    
    // Calculate delivery rate
    $stats['delivery_rate'] = $stats['total'] > 0 ? 
      round(($stats['delivered'] / $stats['total']) * 100, 1) : 0;
    
    return $stats;
  } catch (mysqli_sql_exception $e) {
    // Return default stats if table doesn't exist
    return [
      'total' => 0, 'delivered' => 0, 'pending' => 0, 'failed' => 0, 'delivery_rate' => 0
    ];
  }
}

function get_sms_delivery_chart_data(): array {
  $mysqli = get_db_connection();
  
  try {
    // Get last 7 days of SMS delivery rates
    $sql = "SELECT 
              DATE(created_at) as date,
              COUNT(*) as total,
              SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as delivered,
              ROUND((SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as rate
            FROM sms_logs 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC";
    
    $result = $mysqli->query($sql);
    $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    
    // Fill in missing days with 0% rate
    $chart_data = [];
    $labels = [];
    $rates = [];
    
    for ($i = 6; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("-$i days"));
      $day_name = date('D', strtotime("-$i days"));
      
      $labels[] = $day_name;
      
      // Find data for this date
      $found = false;
      foreach ($data as $row) {
        if ($row['date'] === $date) {
          $rates[] = (float)$row['rate'];
          $found = true;
          break;
        }
      }
      
      if (!$found) {
        $rates[] = 0; // No SMS sent on this day
      }
    }
    
    return [
      'labels' => $labels,
      'rates' => $rates
    ];
  } catch (mysqli_sql_exception $e) {
    // Return default chart data if table doesn't exist
    return [
      'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      'rates' => [0, 0, 0, 0, 0, 0, 0] // No data available
    ];
  }
}

// SMS Grade Tasks Metrics
function get_sms_tasks_count(): int {
  $mysqli = get_db_connection();
  
  try {
    $sql = "SELECT COUNT(*) as total FROM sms_logs";
    $result = $mysqli->query($sql);
    $data = $result ? $result->fetch_assoc() : ['total' => 0];
    return (int)$data['total'];
  } catch (mysqli_sql_exception $e) {
    return 0; // Return 0 if table doesn't exist
  }
}

function get_sms_tasks_change_percentage(): string {
  $mysqli = get_db_connection();
  
  try {
    // Get current month count
    $sql_current = "SELECT COUNT(*) as current_count 
                    FROM sms_logs 
                    WHERE YEAR(created_at) = YEAR(CURDATE()) 
                    AND MONTH(created_at) = MONTH(CURDATE())";
    
    // Get previous month count
    $sql_previous = "SELECT COUNT(*) as previous_count 
                     FROM sms_logs 
                     WHERE YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                     AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
    
    $current_result = $mysqli->query($sql_current);
    $previous_result = $mysqli->query($sql_previous);
    
    $current_count = $current_result ? $current_result->fetch_assoc()['current_count'] : 0;
    $previous_count = $previous_result ? $previous_result->fetch_assoc()['previous_count'] : 0;
    
    if ($previous_count == 0) {
      return $current_count > 0 ? '+100%' : '0%';
    }
    
    $percentage = round((($current_count - $previous_count) / $previous_count) * 100);
    return ($percentage >= 0 ? '+' : '') . $percentage . '%';
    
  } catch (mysqli_sql_exception $e) {
    return '0%'; // Return 0% if table doesn't exist
  }
}

// Grade Distribution Functions
function get_grade_distribution(): array {
  $mysqli = get_db_connection();
  
  try {
    $sql = "SELECT 
              CASE 
                WHEN grade >= 1.0 AND grade <= 1.5 THEN 'A'
                WHEN grade >= 1.6 AND grade <= 2.5 THEN 'B'
                WHEN grade >= 2.6 AND grade <= 3.0 THEN 'C'
                WHEN grade >= 3.1 AND grade <= 4.0 THEN 'D'
                WHEN grade >= 4.1 AND grade <= 5.0 THEN 'F'
                ELSE 'Other'
              END as grade_letter,
              COUNT(*) as count
            FROM grades 
            WHERE grade IS NOT NULL 
            GROUP BY grade_letter
            ORDER BY grade_letter";
    
    $result = $mysqli->query($sql);
    $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    
    // Initialize all grades with 0 count
    $distribution = [
      'A' => 0,
      'B' => 0, 
      'C' => 0,
      'D' => 0,
      'F' => 0
    ];
    
    // Fill in actual counts
    foreach ($data as $row) {
      if (isset($distribution[$row['grade_letter']])) {
        $distribution[$row['grade_letter']] = (int)$row['count'];
      }
    }
    
    return $distribution;
  } catch (mysqli_sql_exception $e) {
    // Return default distribution if table doesn't exist
    return ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
  }
}
