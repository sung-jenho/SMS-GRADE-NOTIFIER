<?php /** @var array $students */ ?>
<div class="row g-4 mb-4 metric-row">
    <div class="col-md-6">
        <div class="card-metric">
            <span class="icon"><span class="bi bi-people"></span></span>
            <span class="metric-value"><?= number_format(count($students)) ?></span>
            <span class="metric-label">Total Students<br><span class="metric-sub">this month</span></span>
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


