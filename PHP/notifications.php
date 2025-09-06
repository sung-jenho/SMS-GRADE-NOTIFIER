<?php /** @var array $sms_logs */ ?>
<div class="dashboard-header d-flex align-items-center justify-content-between">
  <div>
    <h2 class="me-3 mb-0">Recent SMS Logs</h2>
    <span class="text-secondary" style="font-size:0.9rem;">Latest grade notifications sent to parents</span>
  </div>
  <div>
    <button type="button" class="btn btn-outline-warning btn-sm me-2" id="clearTestDataBtn" title="Remove test data only">
      <i class="bi bi-trash"></i> Clear Test Data
    </button>
    <button type="button" class="btn btn-outline-danger btn-sm" id="clearAllDataBtn" title="Remove all SMS logs">
      <i class="bi bi-trash-fill"></i> Clear All
    </button>
  </div>
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
            <th width="100">Actions</th>
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
                <td>
                  <button type="button" class="btn btn-sm btn-outline-danger remove-sms-log-btn" 
                          data-sms-log-id="<?= htmlspecialchars($log['id']) ?>"
                          data-student-name="<?= htmlspecialchars($log['student_name']) ?>"
                          data-subject-title="<?= htmlspecialchars($log['subject_title']) ?>"
                          title="Remove SMS Log">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-secondary">No SMS logs yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Confirmation Modal for Single SMS Log Deletion -->
<div class="modal fade" id="confirmSmsDeleteModal" tabindex="-1" aria-labelledby="confirmSmsDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmSmsDeleteModalLabel">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>
          Confirm SMS Log Removal
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-danger" id="confirmSmsDeleteBtn">
          <i class="bi bi-trash me-1"></i>Remove SMS Log
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal for Clearing Test Data -->
<div class="modal fade" id="confirmClearTestModal" tabindex="-1" aria-labelledby="confirmClearTestModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmClearTestModalLabel">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>
          Clear Test Data
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">This will remove all test SMS logs based on the following criteria:</p>
        <ul class="list-unstyled">
          <li><i class="bi bi-calendar-x text-danger me-2"></i>Future dates (after current date)</li>
          <li><i class="bi bi-telephone text-danger me-2"></i>Phone numbers with +1-555- pattern</li>
          <li><i class="bi bi-123 text-danger me-2"></i>Grade snapshots that are just numbers</li>
        </ul>
        <div class="alert alert-info d-flex align-items-center" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <div>Real SMS logs will be preserved.</div>
        </div>
        <p class="text-muted small mb-0">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-warning" id="confirmClearTestBtn">
          <i class="bi bi-trash me-1"></i>Clear Test Data
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
        <h5 class="modal-title" id="confirmClearAllModalLabel">
          <i class="bi bi-exclamation-triangle text-danger me-2"></i>
          Clear All SMS Logs
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-danger" id="confirmClearAllBtn">
          <i class="bi bi-trash-fill me-1"></i>Clear All Data
        </button>
      </div>
    </div>
  </div>
</div>


