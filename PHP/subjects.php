<?php /** @var array $subjects */ ?>
<div class="dashboard-header d-flex align-items-center justify-content-between">
  <div>
    <h2 class="me-3 mb-0">Subjects</h2>
    <span class="text-secondary" style="font-size:0.9rem;">Manage subject information and course codes</span>
  </div>
</div>

<!-- Add Subject Form -->
<div class="subjects-form-container">
    <form id="addSubjectForm" class="row g-3">
      <div class="col-md-6">
        <label for="subject_code" class="form-label">Subject Code <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="subject_code" name="subject_code" required 
               placeholder="e.g., CM76" maxlength="10">
        <div class="form-text">Unique identifier for the subject</div>
      </div>
      <div class="col-md-6">
        <label for="subject_title" class="form-label">Subject Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="subject_title" name="subject_title" required 
               placeholder="e.g., Database Management Systems" maxlength="100">
      </div>
      <div class="col-md-2">
        <label for="units" class="form-label">Units</label>
        <input type="number" class="form-control" id="units" name="units" 
               placeholder="3" min="1" max="10" step="1">
      </div>
      <div class="col-md-6">
        <label for="schedule" class="form-label">Schedule</label>
        <div class="d-flex align-items-center gap-2 schedule-row">
          <input type="time" class="form-control schedule-time flex-shrink-0" style="max-width: 150px;" id="start_time" name="start_time">
          <span class="text-muted flex-shrink-0">to</span>
          <input type="time" class="form-control schedule-time flex-shrink-0" style="max-width: 150px;" id="end_time" name="end_time">
        </div>
        <input type="hidden" id="schedule" name="schedule">
      </div>
      <div class="col-md-2">
        <label for="days" class="form-label">Days</label>
        <input type="text" class="form-control" id="days" name="days" 
               placeholder="e.g., MWF" maxlength="20">
      </div>
      <div class="col-md-2">
        <label for="room" class="form-label">Room</label>
        <input type="text" class="form-control" id="room" name="room" 
               placeholder="e.g., 101" maxlength="20">
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-plus-circle me-2"></i>Add Subject
        </button>
        <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
      </div>
    </form>
</div>

<!-- Subjects List -->
<div class="subjects-table-container">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">All Subjects</h5>
    <span class="badge bg-primary"><?= count($subjects ?? []) ?> subjects</span>
  </div>
  <div class="card-body p-0">
    <?php if (!empty($subjects)): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Subject Code</th>
              <th>Subject Title</th>
              <th>Units</th>
              <th>Schedule</th>
              <th>Days</th>
              <th>Room</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($subjects as $subject): ?>
              <tr>
                <td>
                  <span class="badge badge-beige"><?= htmlspecialchars($subject['subject_code']) ?></span>
                </td>
                <td><?= htmlspecialchars($subject['subject_title']) ?></td>
                <td>
                  <?php if ($subject['units']): ?>
                    <span class="badge bg-info"><?= $subject['units'] ?> units</span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($subject['schedule']): ?>
                    <span class="badge badge-skyblue"><?= htmlspecialchars($subject['schedule']) ?></span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($subject['days']): ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($subject['days']) ?></span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($subject['room']): ?>
                    <span class="badge badge-maroon"><?= htmlspecialchars($subject['room']) ?></span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <i class="bi bi-pencil me-2" 
                     data-id="<?= $subject['id'] ?>"
                     title="Edit Subject"
                     style="cursor: pointer; font-size: 1.1rem; color: #0d6efd; transition: all 0.2s ease;"
                     onmouseover="this.style.color='#0a58ca'; this.style.transform='scale(1.1)'"
                     onmouseout="this.style.color='#0d6efd'; this.style.transform='scale(1)'" ></i>
                  <i class="bi bi-dash-circle" 
                     data-id="<?= $subject['id'] ?>"
                     title="Delete Subject"
                     style="cursor: pointer; font-size: 1.1rem; color: #dc3545; transition: all 0.2s ease;"
                     onmouseover="this.style.color='#b02a37'; this.style.transform='scale(1.1)'"
                     onmouseout="this.style.color='#dc3545'; this.style.transform='scale(1)'" ></i>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="subjects-empty-state">
        <i class="bi bi-book"></i>
        <h5>No subjects found</h5>
        <p>Add your first subject using the form above.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-check-circle me-2"></i><span id="successMessage"></span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
  <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-exclamation-circle me-2"></i><span id="errorMessage"></span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:650px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; padding: 0;">
      <!-- Blue Header -->
      <div class="modal-header border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 1.5rem; margin: 0; border-radius: 16px 16px 0 0;">
        <h5 class="modal-title text-white fw-bold mb-0" id="editSubjectModalLabel" style="font-size: 1rem; letter-spacing: 0.5px;">
          <i class="bi bi-pencil me-2"></i>Edit Subject
        </h5>
      </div>
      
      <div class="modal-body" style="padding: 1.5rem;">
        <form id="editSubjectForm" class="row g-3">
          <input type="hidden" id="editSubjectId" name="id">
          <div class="col-md-6">
            <label for="editSubjectCode" class="form-label">Subject Code <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editSubjectCode" name="subject_code" required maxlength="10">
          </div>
          <div class="col-md-6">
            <label for="editSubjectTitle" class="form-label">Subject Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editSubjectTitle" name="subject_title" required maxlength="100">
          </div>
          <div class="col-md-2">
            <label for="editUnits" class="form-label">Units</label>
            <input type="number" class="form-control" id="editUnits" name="units" min="1" max="10" step="1">
          </div>
          <div class="col-md-6">
            <label for="editSchedule" class="form-label">Schedule</label>
            <div class="d-flex align-items-center gap-2 schedule-row">
              <input type="time" class="form-control schedule-time flex-shrink-0" style="max-width: 150px;" id="editStartTime" name="start_time">
              <span class="text-muted flex-shrink-0">to</span>
              <input type="time" class="form-control schedule-time flex-shrink-0" style="max-width: 150px;" id="editEndTime" name="end_time">
            </div>
            <input type="hidden" id="editSchedule" name="schedule">
          </div>
          <div class="col-md-2">
            <label for="editDays" class="form-label">Days</label>
            <input type="text" class="form-control" id="editDays" name="days" maxlength="20">
          </div>
          <div class="col-md-2">
            <label for="editRoom" class="form-label">Room</label>
            <input type="text" class="form-control" id="editRoom" name="room" maxlength="20">
          </div>
        </form>
      </div>
      
      <div class="modal-footer border-0" style="padding: 1rem 1.5rem 1.5rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px;">
          Cancel
        </button>
        <button type="button" class="btn text-white fw-semibold" id="saveEditSubjectBtn" 
                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); 
                       font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px; border: none;">
          <i class="bi bi-check-circle me-1"></i>
          Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Loading Animation Overlay for Subjects -->
<div id="subjectLoadingOverlay" class="loading-overlay">
    <div id="subjectLoadingAnimation" class="loading-animation"></div>
    <div class="loading-text">Adding Subject<span class="loading-dots"></span></div>
    <div class="loading-subtext">Please wait while we process the subject information</div>
</div>

<!-- Delete Subject Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1" aria-labelledby="deleteSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; padding: 0;">
      <!-- Red Header -->
      <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 1.5rem; margin: 0; border-radius: 16px 16px 0 0;">
        <h5 class="modal-title text-white fw-bold mb-0" id="deleteSubjectModalLabel" style="font-size: 1rem; letter-spacing: 0.5px;">
          <i class="bi bi-exclamation-triangle me-2"></i>Confirm Subject Deletion
        </h5>
      </div>
      
      <div class="modal-body" style="padding: 1.5rem;">
        <p class="mb-3" style="color: #374151; font-size: 0.95rem;">Are you sure you want to delete this subject?</p>
        
        <!-- Subject Info Card with Red Border -->
        <div class="border rounded-3 p-3 mb-3" style="background-color: #fef2f2; border-color: #dc2626 !important; border-width: 2px; font-size: 0.9rem;">
          <div>
            <strong style="color: #dc2626;">Subject:</strong> 
            <span id="deleteSubjectName" style="color: #374151;"></span>
          </div>
        </div>
        
        <div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <div>
            <strong>Warning:</strong> This action cannot be undone. The subject will be permanently removed from the system.
          </div>
        </div>
        
        <p class="text-muted small mb-0" style="font-size: 0.85rem;">Note: Subjects with existing grades cannot be deleted.</p>
      </div>
      
      <div class="modal-footer border-0" style="padding: 1rem 1.5rem 1.5rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px;">
          Cancel
        </button>
        <button type="button" class="btn text-white fw-semibold" id="confirmDeleteSubjectBtn" 
                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); 
                       font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px; border: none;">
          <i class="bi bi-trash me-1"></i>
          Delete Subject
        </button>
      </div>
    </div>
  </div>
</div>

<!-- subject-manager.js handles all subject logic -->
<script src="../JAVASCRIPTS/subject-manager.js"></script>
