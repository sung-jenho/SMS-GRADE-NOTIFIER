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
      <div class="col-md-4">
        <label for="units" class="form-label">Units</label>
        <input type="number" class="form-control" id="units" name="units" 
               placeholder="3" min="1" max="10" step="1">
      </div>
      <div class="col-md-4">
        <label for="schedule" class="form-label">Schedule</label>
        <input type="text" class="form-control" id="schedule" name="schedule" 
               placeholder="e.g., 8:00-9:00 AM" maxlength="50">
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
                    <?= htmlspecialchars($subject['schedule']) ?>
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
                    <?= htmlspecialchars($subject['room']) ?>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <i class="bi bi-pencil me-2" 
                     onclick="editSubject(<?= $subject['id'] ?>)" 
                     title="Edit Subject"
                     style="cursor: pointer; font-size: 1.1rem; color: #0d6efd; transition: all 0.2s ease;"
                     onmouseover="this.style.color='#0a58ca'; this.style.transform='scale(1.1)'"
                     onmouseout="this.style.color='#0d6efd'; this.style.transform='scale(1)'"></i>
                  <i class="bi bi-dash-circle" 
                     onclick="deleteSubject(<?= $subject['id'] ?>)" 
                     title="Delete Subject"
                     style="cursor: pointer; font-size: 1.1rem; color: #dc3545; transition: all 0.2s ease;"
                     onmouseover="this.style.color='#b02a37'; this.style.transform='scale(1.1)'"
                     onmouseout="this.style.color='#dc3545'; this.style.transform='scale(1)'"></i>
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
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSubjectModalLabel">
          <i class="bi bi-pencil me-2"></i>Edit Subject
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
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
          <div class="col-md-4">
            <label for="editUnits" class="form-label">Units</label>
            <input type="number" class="form-control" id="editUnits" name="units" min="1" max="10" step="1">
          </div>
          <div class="col-md-4">
            <label for="editSchedule" class="form-label">Schedule</label>
            <input type="text" class="form-control" id="editSchedule" name="schedule" maxlength="50">
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Cancel
        </button>
        <button type="button" class="btn btn-primary btn-sm" id="saveEditSubjectBtn" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Subject Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1" aria-labelledby="deleteSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSubjectModalLabel">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Subject Deletion
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Are you sure you want to delete this subject?</p>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <div>
            <strong>Subject:</strong> <span id="deleteSubjectName"></span>
          </div>
        </div>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <div>
            <strong>Warning:</strong> This action cannot be undone. The subject will be permanently removed from the system.
          </div>
        </div>
        <p class="text-muted small mb-0">Note: Subjects with existing grades cannot be deleted.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Cancel
        </button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteSubjectBtn" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
          Delete Subject
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const addSubjectForm = document.getElementById('addSubjectForm');
  const editSubjectForm = document.getElementById('editSubjectForm');
  const saveEditSubjectBtn = document.getElementById('saveEditSubjectBtn');
  const confirmDeleteSubjectBtn = document.getElementById('confirmDeleteSubjectBtn');
  
  window.currentDeleteId = null;
  
  // Toast functions
  function showToast(type, message) {
    const toastId = type === 'success' ? 'successToast' : 'errorToast';
    const messageId = type === 'success' ? 'successMessage' : 'errorMessage';
    
    document.getElementById(messageId).textContent = message;
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
  }
  
  // Add Subject Form
  if (addSubjectForm) {
    addSubjectForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding...';
      
      fetch('/VESTIL/PHP/manage_subject.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('success', 'Subject added successfully!');
          this.reset();
          setTimeout(() => location.reload(), 1500);
        } else {
          showToast('error', data.message || 'Failed to add subject');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred while adding the subject');
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Subject';
      });
    });
  }
  
  // Save Edit Subject
  if (saveEditSubjectBtn) {
    saveEditSubjectBtn.addEventListener('click', function() {
      const formData = new FormData(editSubjectForm);
      formData.append('action', 'edit');
      
      this.disabled = true;
      this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Saving...';
      
      fetch('/VESTIL/PHP/manage_subject.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('success', 'Subject updated successfully!');
          bootstrap.Modal.getInstance(document.getElementById('editSubjectModal')).hide();
          setTimeout(() => location.reload(), 1500);
        } else {
          showToast('error', data.message || 'Failed to update subject');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred while updating the subject');
      })
      .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle me-1"></i>Save Changes';
      });
    });
  }
  
  // Confirm Delete Subject
  if (confirmDeleteSubjectBtn) {
    confirmDeleteSubjectBtn.addEventListener('click', function() {
      if (!window.currentDeleteId) return;
      
      this.disabled = true;
      this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
      
      fetch('/VESTIL/PHP/manage_subject.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=delete&id=' + window.currentDeleteId
      })
      .then(response => {
        console.log('Delete response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Delete response data:', data);
        if (data.success) {
          showToast('success', 'Subject deleted successfully!');
          bootstrap.Modal.getInstance(document.getElementById('deleteSubjectModal')).hide();
          setTimeout(() => location.reload(), 1500);
        } else {
          showToast('error', data.message || 'Failed to delete subject');
        }
      })
      .catch(error => {
        console.error('Delete error:', error);
        showToast('error', 'An error occurred while deleting the subject');
      })
      .finally(() => {
        this.disabled = false;
        this.innerHTML = 'Delete Subject';
      });
    });
  }
});

function editSubject(id) {
  // Find subject data from the table
  const row = document.querySelector(`i[onclick="editSubject(${id})"]`).closest('tr');
  const cells = row.querySelectorAll('td');
  
  // Populate modal form
  document.getElementById('editSubjectId').value = id;
  document.getElementById('editSubjectCode').value = cells[0].textContent.trim();
  document.getElementById('editSubjectTitle').value = cells[1].textContent.trim();
  
  // Handle units (extract number from badge)
  const unitsText = cells[2].textContent.trim();
  if (unitsText !== '-') {
    const unitsMatch = unitsText.match(/(\d+)/);
    document.getElementById('editUnits').value = unitsMatch ? unitsMatch[1] : '';
  } else {
    document.getElementById('editUnits').value = '';
  }
  
  // Handle other fields
  document.getElementById('editSchedule').value = cells[3].textContent.trim() === '-' ? '' : cells[3].textContent.trim();
  document.getElementById('editDays').value = cells[4].textContent.trim() === '-' ? '' : cells[4].textContent.trim();
  document.getElementById('editRoom').value = cells[5].textContent.trim() === '-' ? '' : cells[5].textContent.trim();
  
  // Show modal
  new bootstrap.Modal(document.getElementById('editSubjectModal')).show();
}

function deleteSubject(id) {
  window.currentDeleteId = id;
  
  // Find subject data from the table
  const row = document.querySelector(`i[onclick="deleteSubject(${id})"]`).closest('tr');
  const subjectCode = row.querySelector('td:first-child').textContent.trim();
  const subjectTitle = row.querySelector('td:nth-child(2)').textContent.trim();
  
  // Populate modal
  document.getElementById('deleteSubjectName').textContent = `${subjectCode} - ${subjectTitle}`;
  
  // Show modal
  new bootstrap.Modal(document.getElementById('deleteSubjectModal')).show();
}
</script>
