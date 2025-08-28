<?php
require_once __DIR__ . '/includes/queries.php';
$students = fetch_students();
$subjects = fetch_subjects();
$grades = fetch_grades();
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
    <link rel="stylesheet" href="vestil-dashboard.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
<div class="dashboard-main">
<?php if ($section == 'overview'): ?>
    <div class="dashboard-header d-flex align-items-center">
        <h2 class="me-3 mb-0">Dashboard Overview</h2>
        <span class="status-badge">System Active</span>
    </div>
    <p style="color:#6c6e7e;margin-bottom:2.2rem;">Monitor your SMS grade notification system performance</p>
    <div class="row g-4 mb-4">
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
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
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
        </div>
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
    <div class="dashboard-header d-flex align-items-center">
        <h2 class="me-3 mb-0">Notifications</h2>
    </div>
    <div class="card">
        <div class="card-body text-secondary">
            <p>No notifications to display. (Placeholder)</p>
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
