<?php /** @var array $students */ /** @var array $subjects */ /** @var array $grades */ ?>
<div class="form-section">
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
            <th width="50">Action</th>
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
            <td>
              <button type="button" class="btn btn-sm btn-outline-danger remove-grade-btn" 
                      data-grade-id="<?= htmlspecialchars($g['id']) ?>"
                      data-student-name="<?= htmlspecialchars($g['name']) ?>"
                      data-subject-title="<?= htmlspecialchars($g['subject_title']) ?>"
                      title="Remove Grade">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modern Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>
          Confirm Grade Removal
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
          <i class="bi bi-trash me-1"></i>Remove Grade
        </button>
      </div>
    </div>
  </div>
</div>


