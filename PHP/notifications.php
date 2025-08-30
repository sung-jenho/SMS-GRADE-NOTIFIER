<?php /** @var array $sms_logs */ ?>
<div class="dashboard-header d-flex align-items-center">
  <h2 class="me-3 mb-0">Recent SMS Logs</h2>
  <span class="text-secondary" style="font-size:0.9rem;">Latest grade notifications sent to parents</span>
</div>
<div class="card chart-card">
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
                <td><span class="badge rounded-pill bg-light text-secondary fw-semibold"><?= htmlspecialchars($log['grade']) ?></span></td>
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


