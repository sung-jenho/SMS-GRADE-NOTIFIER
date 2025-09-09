<?php /** @var array $students */ /** @var array $subjects */ /** @var array $grades */ ?>
<div class="grades-form-container">
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
<div class="grades-table-container">
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
            <th width="50">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($grades as $g): ?>
          <tr>
            <td><?= htmlspecialchars($g['student_name']) ?></td>
            <td><span class="badge badge-purple"><?= htmlspecialchars($g['student_number']) ?></span></td>
            <td><span class="badge badge-beige"><?= htmlspecialchars($g['subject_code']) ?></span></td>
            <td><?= htmlspecialchars($g['subject_title']) ?></td>
            <?php
              $gradeVal = is_numeric($g['grade']) ? (float)$g['grade'] : null;
              if ($gradeVal !== null && $gradeVal >= 1.0 && $gradeVal <= 2.0) {
                $badgeClass = 'badge-grade-green';
              } elseif ($gradeVal !== null && $gradeVal > 2.0 && $gradeVal <= 2.9) {
                $badgeClass = 'badge-grade-amber';
              } else {
                // Covers 3.0–5.0 and any other unexpected values
                $badgeClass = 'badge-grade-red';
              }
            ?>
            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($g['grade']) ?></span></td>
            <td>
              <span class="badge badge-cream">
                <?php
                  $ts = strtotime($g['last_updated']);
                  echo $ts ? date('y/n/j H:i', $ts) : htmlspecialchars($g['last_updated']);
                ?>
              </span>
            </td>
            <td>
              <i class="bi bi-dash-circle remove-grade-btn" 
                 data-grade-id="<?= htmlspecialchars($g['id']) ?>"
                 data-student-name="<?= htmlspecialchars($g['student_name']) ?>"
                 data-subject-title="<?= htmlspecialchars($g['subject_title']) ?>"
                 title="Remove Grade"></i>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>

<!-- Modern Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel" style="font-size: 0.95rem; font-weight: 600;">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>
          CONFIRM GRADE REMOVAL
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Are you sure you want to remove this grade?</p>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <div>
            <strong>Student:</strong> <span id="modalStudentName"></span><br>
            <strong>Subject:</strong> <span id="modalSubjectTitle"></span>
          </div>
        </div>
        <p class="text-muted small mb-0">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Cancel
        </button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Remove Grade
        </button>
      </div>
    </div>
  </div>
</div>


