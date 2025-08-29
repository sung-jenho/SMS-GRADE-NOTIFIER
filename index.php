<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/includes/queries.php';
$students = fetch_students();
$subjects = fetch_subjects();
$grades = fetch_grades();
// recent SMS logs for Notifications section
if (!function_exists('fetch_sms_logs')) {
    require_once __DIR__ . '/includes/queries.php';
}
$sms_logs = fetch_sms_logs(50);
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMS Grade Notifier</title>
    <link rel="icon" type="image/png" href="assets/ctu-logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="vestil-dashboard.css?v=2">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
<div class="dashboard-main">
<?php if ($section == 'overview'): ?>
    <div class="row g-4 mb-4 metric-row">
        <div class="col-md-6">
            <div class="card-metric">
                <span class="icon"><span class="bi bi-people"></span></span>
                <span class="metric-value"><?= number_format(count($students)) ?></span>
                <span class="metric-label">Total Students<br><span style="font-size:0.95rem;color:#a1a1aa;font-weight:400;">this month</span></span>
                <span class="metric-change">+12</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-metric">
                <span class="icon green"><span class="bi bi-chat-dots"></span></span>
                <span class="metric-value">348</span>
                <span class="metric-label">SMS Grade Tasks</span>
                <span class="metric-change green">+23%</span>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-semibold text-secondary">SMS Delivery Rate</div>
                        <div class="badge rounded-pill bg-light text-secondary">Last 7 days</div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="smsDeliveryChart" height="160"></canvas>
                    </div>
                    <div class="d-flex justify-content-around mt-3 small text-secondary">
                        <div><span class="legend-dot legend-green"></span>1,245 Delivered</div>
                        <div><span class="legend-dot legend-amber"></span>67 Pending</div>
                        <div><span class="legend-dot legend-red"></span>8 Failed</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="fw-semibold text-secondary mb-3">Grade Distribution</div>
                    <div class="chart-wrapper">
                        <canvas id="gradeDistributionChart" height="160"></canvas>
                    </div>
                    <div class="chart-legend mt-3 small">
                        <span><span class="legend-dot" style="background:#22c55e"></span>Grade A</span>
                        <span><span class="legend-dot" style="background:#3b82f6"></span>Grade B</span>
                        <span><span class="legend-dot" style="background:#f59e0b"></span>Grade C</span>
                        <span><span class="legend-dot" style="background:#8b5cf6"></span>Grade D</span>
                        <span><span class="legend-dot" style="background:#ef4444"></span>Grade F</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($section == 'students'): ?>
    <div class="dashboard-header d-flex align-items-center">
        <h2 class="me-3 mb-0">Students</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Student #</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['student_number']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif ($section == 'grades'): ?>
    <div class="dashboard-header d-flex align-items-center">
        <h2 class="me-3 mb-0">Grade Updates</h2>
    </div>
    <div class="form-section">
        <h2 class="mb-3">Add/Update Grade</h2>
        <form action="update_grade.php" method="post" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Student</label>
                <select name="student_id" class="form-select" required>
                    <option value="">Select...</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['student_number']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" required>
                    <option value="">Select...</option>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['subject_code']) ?> - <?= htmlspecialchars($sub['subject_title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Grade</label>
                <input type="number" name="grade" class="form-control" required min="0" max="5" step="0.01">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Student #</th>
                            <th>Subject Code</th>
                            <th>Subject Title</th>
                            <th>Grade</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $g): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['name']) ?></td>
                            <td><?= htmlspecialchars($g['student_number']) ?></td>
                            <td><?= htmlspecialchars($g['subject_code']) ?></td>
                            <td><?= htmlspecialchars($g['subject_title']) ?></td>
                            <td><?= htmlspecialchars($g['grade']) ?></td>
                            <td><?= htmlspecialchars($g['last_updated']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif ($section == 'notifications'): ?>
    <div class="dashboard-header d-flex align-items-center" style="margin-bottom: 0.5rem;">
        <h2 class="me-3 mb-0">Recent SMS Logs</h2>
        <span class="text-secondary" style="font-size:0.9rem;">Latest grade notifications sent to parents</span>
    </div>
    <div class="card chart-card" style="border-radius:16px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Parent Phone</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sms_logs)): ?>
                            <?php foreach ($sms_logs as $log): ?>
                                <?php
                                  $status = $log['status'];
                                  $pillClass = $status === 'Sent' ? 'badge-sent' : ($status === 'Failed' ? 'badge-failed' : 'badge-pending');
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['student_name']) ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($log['parent_phone']) ?></td>
                                    <td><?= htmlspecialchars($log['subject_title']) ?></td>
                                    <td><span class="badge rounded-pill bg-light text-secondary" style="font-weight:600;"><?= htmlspecialchars($log['grade']) ?></span></td>
                                    <td><span class="status-pill <?= $pillClass ?>"><?= htmlspecialchars($status) ?></span></td>
                                    <td class="text-secondary"><?= htmlspecialchars($log['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-secondary">No SMS logs yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif ($section == 'settings'): ?>
    <div class="dashboard-header" style="margin-bottom: 0.5rem;">
        <h2 class="me-3 mb-0">Settings</h2>
    </div>
    <div class="settings-card">
        Settings panel coming soon. (Placeholder)
    </div>
<?php endif; ?>
</div>
<script src="assets/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
