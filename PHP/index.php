<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/queries.php';
$students = fetch_students();
$subjects = fetch_subjects();
$grades = fetch_grades();
$sms_logs = fetch_sms_logs(50);
$sms_tasks_count = get_sms_tasks_count();
$sms_tasks_change = get_sms_tasks_change_percentage();
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crozono</title>
    <link rel="icon" type="image/png" href="../assets/ctu-logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../CSS/vestil-dashboard.css?v=2">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="dashboard-main">
<?php
  $view = __DIR__ . '/' . $section . '.php';
  if (file_exists($view)) {
    include $view;
  }
?>
</div>
<script src="../JAVASCRIPTS/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
