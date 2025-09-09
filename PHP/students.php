<?php /** @var array $students */ ?>
<div class="dashboard-header d-flex align-items-center justify-content-between">
  <div>
    <h2 class="me-3 mb-0">Students</h2>
    <span class="text-secondary" style="font-size:0.9rem;">Manage student information and records</span>
  </div>
</div>

<!-- Add Student Form -->
<div class="students-form-container">
    <form id="addStudentForm" class="row g-3">
      <div class="col-md-6">
        <label for="student_number" class="form-label">Student Number <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="student_number" name="student_number" required 
               placeholder="e.g., 1340789" maxlength="20">
        <div class="form-text">Unique identifier for the student</div>
      </div>
      <div class="col-md-6">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" required 
               placeholder="e.g., John Doe" maxlength="100">
      </div>
      <div class="col-md-4">
        <label for="course" class="form-label">Course</label>
        <input type="text" class="form-control" id="course" name="course" 
               placeholder="e.g., BSIT" maxlength="20">
      </div>
      <div class="col-md-4">
        <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
        <select class="form-select" id="year_level" name="year_level" required>
          <option value="">Select Year Level</option>
          <option value="1">1st Year</option>
          <option value="2">2nd Year</option>
          <option value="3">3rd Year</option>
          <option value="4">4th Year</option>
          <option value="5">5th Year</option>
        </select>
      </div>
      <div class="col-md-4">
        <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
        <input type="tel" class="form-control" id="phone_number" name="phone_number" required 
               placeholder="e.g., +63 912 345 6789" maxlength="20">
        <div class="form-text">Parent's or student's contact number</div>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-person-plus"></i> Add Student
        </button>
        <button type="reset" class="btn btn-outline-secondary ms-2">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </button>
      </div>
    </form>
</div>

<!-- Students Table -->
<div class="students-table-container">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Student List</h5>
    <span class="badge bg-primary"><?= count($students) ?> students</span>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Student #</th>
            <th>Course</th>
            <th>Year Level</th>
            <th>Phone Number</th>
            <th width="120">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($students)): ?>
            <?php foreach ($students as $s): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                    <?= strtoupper(substr($s['name'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($s['name']) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge badge-purple"><?= htmlspecialchars($s['student_number']) ?></span>
              </td>
              <td>
                <span class="badge badge-yellow"><?= htmlspecialchars($s['course'] ?? 'N/A') ?></span>
              </td>
              <td>
                <span class="badge bg-info"><?= htmlspecialchars($s['year_level'] ?? 'N/A') ?> Year</span>
              </td>
              <td>
                <span class="badge badge-red">
                  <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($s['phone_number']) ?>
                </span>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-pencil edit-student-btn" 
                     role="button"
                     data-student-id="<?= htmlspecialchars($s['id']) ?>"
                     data-student-number="<?= htmlspecialchars($s['student_number']) ?>"
                     data-name="<?= htmlspecialchars($s['name']) ?>"
                     data-course="<?= htmlspecialchars($s['course'] ?? '') ?>"
                     data-year-level="<?= htmlspecialchars($s['year_level'] ?? '') ?>"
                     data-phone-number="<?= htmlspecialchars($s['phone_number']) ?>"
                     title="Edit Student"></i>
                  <i class="bi bi-dash-circle delete-student-btn" 
                     role="button"
                     data-student-id="<?= htmlspecialchars($s['id']) ?>"
                     data-student-name="<?= htmlspecialchars($s['name']) ?>"
                     title="Delete Student"></i>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-person-x display-4 d-block mb-2"></i>
                No students found. Add your first student above.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editStudentModalLabel">
          <i class="bi bi-pencil-square me-2"></i>Edit Student
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editStudentForm">
        <div class="modal-body">
          <input type="hidden" id="edit_student_id" name="student_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="edit_student_number" class="form-label">Student Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit_student_number" name="student_number" required maxlength="20">
            </div>
            <div class="col-md-6">
              <label for="edit_name" class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit_name" name="name" required maxlength="100">
            </div>
            <div class="col-md-4">
              <label for="edit_course" class="form-label">Course</label>
              <input type="text" class="form-control" id="edit_course" name="course" maxlength="20">
            </div>
            <div class="col-md-4">
              <label for="edit_year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
              <select class="form-select" id="edit_year_level" name="year_level" required>
                <option value="">Select Year Level</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
                <option value="5">5th Year</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="edit_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
              <input type="tel" class="form-control" id="edit_phone_number" name="phone_number" required maxlength="20">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Update Student
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteStudentModalLabel">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Student Deletion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Are you sure you want to delete this student?</p>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <div>
            <strong>Student:</strong> <span id="deleteStudentName"></span>
          </div>
        </div>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <div>
            <strong>Warning:</strong> This action cannot be undone. The student will be permanently removed from the system.
          </div>
        </div>
        <p class="text-muted small mb-0">Note: Students with existing grades or SMS logs cannot be deleted.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteStudentBtn">
          <i class="bi bi-trash me-1"></i>Delete Student
        </button>
      </div>
    </div>
  </div>
</div>


