<?php
// db_connect.php - Reusable MySQL connection
$mysqli = new mysqli("localhost", "root", "", "sms_grades");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}
?>
