<?php
// Centralized MySQL connection with simple singleton accessor

function get_db_connection(): mysqli {
    static $mysqli = null;
    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'sms_grades';

    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_errno) {
        die('Failed to connect to MySQL: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}


