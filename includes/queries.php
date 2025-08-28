<?php
require_once __DIR__ . '/db.php';

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


