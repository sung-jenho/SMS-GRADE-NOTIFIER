<?php /** @var array $sms_logs */ ?>
<div class="sms-logs-header d-flex align-items-center justify-content-between">
  <div>
    <h2 class="me-3 mb-0">Recent SMS Logs</h2>
    <span class="text-secondary" style="font-size:0.9rem;">Latest grade notifications sent to parents</span>
  </div>
  <div class="me-3">
    <button type="button" class="btn btn-danger px-3 py-1 rounded-pill shadow-sm" id="clearAllDataBtn" title="Remove all SMS logs" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 0.3px; transition: all 0.3s ease;">
      Clear All
    </button>
  </div>
</div>
<div class="sms-logs-table-container">
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
            <th width="100">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($sms_logs)): ?>
            <?php foreach ($sms_logs as $log): ?>
              <?php
                $status = $log['status'];
                $pillClass = $status === 'Sent' ? 'badge-sent' : ($status === 'Failed' ? 'badge-failed' : 'badge-pending');
                // Determine grade pill color using same thresholds as Grades section
                $gradeVal = is_numeric($log['grade_snapshot']) ? (float)$log['grade_snapshot'] : null;
                if ($gradeVal !== null && $gradeVal >= 1.0 && $gradeVal <= 2.0) {
                  $gradeBadge = 'badge-grade-green';
                } elseif ($gradeVal !== null && $gradeVal > 2.0 && $gradeVal <= 2.9) {
                  $gradeBadge = 'badge-grade-amber';
                } else {
                  $gradeBadge = 'badge-grade-red';
                }
                // Short timestamp like 25/9/7 22:40
                $ts = strtotime($log['created_at']);
                $shortTs = $ts ? date('y/n/j H:i', $ts) : $log['created_at'];
              ?>
              <tr>
                <td><?= htmlspecialchars($log['student_name']) ?></td>
                <td class="text-secondary"><?= htmlspecialchars($log['parent_phone']) ?></td>
                <td><?= htmlspecialchars($log['subject_title']) ?></td>
                <td><span class="badge <?= $gradeBadge ?>"><?= htmlspecialchars($log['grade_snapshot']) ?></span></td>
                <td><span class="status-pill <?= $pillClass ?>"><?= htmlspecialchars($status) ?></span></td>
                <td class="text-secondary"><?= htmlspecialchars($shortTs) ?></td>
                <td>
                  <?php if ($status === 'Pending'): ?>
                    <i class="bi bi-send send-sms-btn me-2" 
                       data-sms-log-id="<?= htmlspecialchars($log['id']) ?>"
                       data-student-name="<?= htmlspecialchars($log['student_name']) ?>"
                       data-parent-phone="<?= htmlspecialchars($log['parent_phone']) ?>"
                       data-subject-title="<?= htmlspecialchars($log['subject_title']) ?>"
                       data-grade="<?= htmlspecialchars($log['grade_snapshot']) ?>"
                       title="Send SMS"
                       style="color: #28a745; cursor: pointer;"></i>
                  <?php endif; ?>
                  <i class="bi bi-dash-circle remove-sms-log-btn" 
                     data-sms-log-id="<?= htmlspecialchars($log['id']) ?>"
                     data-student-name="<?= htmlspecialchars($log['student_name']) ?>"
                     data-subject-title="<?= htmlspecialchars($log['subject_title']) ?>"
                     title="Remove SMS Log"></i>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="sms-logs-empty-state"><i class="bi bi-chat-dots"></i><h4>No SMS Logs Yet</h4><p>SMS notifications will appear here once grades are sent to parents.</p></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
</div>

<!-- Confirmation Modal for Single SMS Log Deletion -->
<div class="modal fade" id="confirmSmsDeleteModal" tabindex="-1" aria-labelledby="confirmSmsDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmSmsDeleteModalLabel" style="font-size: 0.95rem; font-weight: 600;">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>
          CONFIRM SMS LOG REMOVAL
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Are you sure you want to remove this SMS log?</p>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <div>
            <strong>Student:</strong> <span id="modalSmsStudentName"></span><br>
            <strong>Subject:</strong> <span id="modalSmsSubjectTitle"></span>
          </div>
        </div>
        <p class="text-muted small mb-0">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Cancel
        </button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmSmsDeleteBtn" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Remove SMS Log
        </button>
      </div>
    </div>
  </div>
</div>

<!-- SMS Sending Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendSmsModalLabel" style="font-size: 0.95rem; font-weight: 600;">
          <i class="bi bi-send text-success me-2"></i>
          SEND SMS NOTIFICATION
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="smsForm">
          <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <div>
              <strong>Student:</strong> <span id="smsStudentName"></span><br>
              <strong>Subject:</strong> <span id="smsSubjectTitle"></span><br>
              <strong>Grade:</strong> <span id="smsGrade"></span>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="smsPhoneNumber" class="form-label">Parent Phone Number</label>
            <input type="tel" class="form-control" id="smsPhoneNumber" readonly>
          </div>
          
          <div class="mb-3">
            <label for="smsMessage" class="form-label">SMS Message</label>
            <textarea class="form-control" id="smsMessage" rows="4" placeholder="Grade notification message will be generated automatically..."></textarea>
            <div class="form-text">You can customize the message before sending.</div>
          </div>
          
          <input type="hidden" id="smsLogId">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Cancel
        </button>
        <button type="button" class="btn btn-success btn-sm" id="confirmSendSmsBtn" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          <i class="bi bi-send me-1"></i>
          Send SMS
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal for Clearing All Data -->
<div class="modal fade" id="confirmClearAllModal" tabindex="-1" aria-labelledby="confirmClearAllModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmClearAllModalLabel" style="font-size: 0.95rem; font-weight: 600;">
          <i class="bi bi-exclamation-triangle text-danger me-2"></i>
          CLEAR ALL SMS LOGS
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">This will remove <strong>ALL</strong> SMS logs from the system.</p>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <div>
            <strong>Warning:</strong> This action will delete all SMS logs, including real data.
            Only use this if you want to start completely fresh.
          </div>
        </div>
        <p class="text-muted small mb-0">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Cancel
        </button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmClearAllBtn" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Clear All Data
        </button>
      </div>
    </div>
  </div>
</div>


