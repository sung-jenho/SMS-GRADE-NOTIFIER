<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/queries.php';
// Fetch data based on section
$students = fetch_students();
$subjects = get_subjects();
$all_subjects = get_all_subjects(); // For subjects management page
$grades = get_grades();
$sms_logs = get_sms_logs();

// Get SMS tasks count and change percentage for overview
$sms_tasks_count = get_sms_tasks_count();
$sms_tasks_change = get_sms_tasks_change_percentage();

// Set the section for page rendering
$section = $_GET['section'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buzzted</title>
    <link rel="icon" type="image/png" href="../assets/ctu-logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../CSS/index.css?v=2">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="dashboard-main">
<?php
  $view = __DIR__ . '/' . $section . '.php';
  if (file_exists($view)) {
    // Pass subjects data to subjects.php
    if ($section === 'subjects') {
      $subjects = $all_subjects;
    }
    include $view;
  }
?>
</div>
    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modular JavaScript Components -->
    <script src="../JAVASCRIPTS/notifications.js"></script>
    <script src="../JAVASCRIPTS/theme-manager.js"></script>
    <script src="../JAVASCRIPTS/charts.js"></script>
    <script src="../JAVASCRIPTS/animations.js"></script>
    <script src="../JAVASCRIPTS/sms-manager.js"></script>
    <script src="../JAVASCRIPTS/student-manager.js"></script>
    <script src="../JAVASCRIPTS/grade-manager.js"></script>
    <script src="../JAVASCRIPTS/ui-effects.js"></script>
    <script src="../JAVASCRIPTS/bee-header.js"></script>
    
    <!-- Main Application Coordinator -->
    <script src="../JAVASCRIPTS/index.js"></script>
</body>
</html>
