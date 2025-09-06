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

    // First connect without database to check if we need to create it
    $mysqli = new mysqli($host, $user, $pass);
    if ($mysqli->connect_errno) {
        die('Failed to connect to MySQL: ' . $mysqli->connect_error);
    }
    
    // Check if database exists, if not create it
    $result = $mysqli->query("SHOW DATABASES LIKE '$db'");
    if ($result->num_rows == 0) {
        $mysqli->query("CREATE DATABASE $db");
        $mysqli->select_db($db);
        
        // Create tables from schema
        $schema = file_get_contents(__DIR__ . '/../SQL/sms_grade_schema.sql');
        $mysqli->multi_query($schema);
        
        // Wait for all queries to complete
        while ($mysqli->next_result()) {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        }
    } else {
        $mysqli->select_db($db);
    }
    
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}


