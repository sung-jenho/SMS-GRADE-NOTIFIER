<?php
// Root router: redirect to the correct PHP entry based on session
session_start();

$base = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // e.g., /VESTIL
$target = isset($_SESSION['user_id']) ? '/PHP/index.php' : '/PHP/login.php';

header('Location: ' . $base . $target);
exit;
