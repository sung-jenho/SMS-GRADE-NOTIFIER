<?php /** @var array $students */ ?>
<div class="row g-4 mb-4 metric-row">
    <div class="col-md-6">
        <a class="card-metric has-right-anim d-block text-decoration-none" href="?section=students" aria-label="Go to Students section">
            <div class="metric-text-group">
                <span class="metric-label">TOTAL STUDENTS</span>
                <span class="metric-subtitle">Total students added in this system.</span>
            </div>
            <span class="metric-change">+<?= number_format(count($students)) ?></span>
            <span id="studentsLottie" class="lottie-right" aria-hidden="true"></span>
        </a>
    </div>
    <div class="col-md-6">
        <a class="card-metric has-right-anim d-block text-decoration-none" href="?section=notifications" aria-label="Go to SMS Logs section">
            <div class="metric-text-group">
                <span class="metric-label">SMS GRADE TASKS</span>
                <span class="metric-subtitle">Actual SMS Logs computed.</span>
            </div>
            <span class="metric-change <?= strpos($sms_tasks_change, '+') === 0 ? 'green' : (strpos($sms_tasks_change, '-') === 0 ? 'red' : '') ?>">+<?= number_format($sms_tasks_count) ?></span>
            <span id="smsLottie" class="lottie-right" aria-hidden="true"></span>
        </a>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fw-semibold text-secondary">SMS DELIVERY RATE</div>
                        <div class="text-muted small">Total SMS calculated.</div>
                    </div>
                    <div class="badge rounded-pill bg-light text-secondary">Last 7 days</div>
                </div>
                <div class="chart-wrapper">
                    <div id="smsDeliveryChart" style="height: 160px;"></div>
                </div>
                <div class="d-flex justify-content-around mt-3 text-secondary" style="font-size: 11px;">
                    <div><span class="legend-dot legend-green" style="width: 6px; height: 6px;"></span>1,245 Delivered</div>
                    <div><span class="legend-dot legend-amber" style="width: 6px; height: 6px;"></span>67 Pending</div>
                    <div><span class="legend-dot legend-red" style="width: 6px; height: 6px;"></span>8 Failed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <a class="d-block text-decoration-none" href="?section=grades" aria-label="Go to Grades section">
        <div class="card chart-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fw-semibold text-secondary">GRADE DISTRIBUTION</div>
                        <div class="text-muted small chart-subtitle">Updated grades of students in a visual graph.</div>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <div id="gradeDistributionChart" style="height: 220px;"></div>
                </div>
                <div class="chart-legend mt-3" id="grade-legend" style="font-size: 11px;">
                    <span><span class="legend-dot" style="background:#22c55e; width: 6px; height: 6px;"></span>CM76</span>
                    <span><span class="legend-dot" style="background:#3b82f6; width: 6px; height: 6px;"></span>CM77</span>
                    <span><span class="legend-dot" style="background:#f59e0b; width: 6px; height: 6px;"></span>CM78</span>
                    <span><span class="legend-dot" style="background:#8b5cf6; width: 6px; height: 6px;"></span>CM79</span>
                    <span><span class="legend-dot" style="background:#ef4444; width: 6px; height: 6px;"></span>CM80</span>
                </div>
            </div>
        </div>
        </a>
    </div>
</div>
