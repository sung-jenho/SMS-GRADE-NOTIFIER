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

function create_user(string $username, string $plaintextPassword, string $fullName, string $email): bool {
    $mysqli = get_db_connection();
    $passwordHash = password_hash($plaintextPassword, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('INSERT INTO users (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ssss', $username, $passwordHash, $fullName, $email);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function fetch_students(): array {
    $mysqli = get_db_connection();
    $result = $mysqli->query('SELECT * FROM students');
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
