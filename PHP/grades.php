<?php /** @var array $students */ /** @var array $subjects */ /** @var array $grades */ ?>
<div class="grades-form-container">
  <form id="gradeForm" class="row g-3">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  let gradeLoadingAnimation = null;
  
  // Initialize Lottie loading animation for grades
  try {
    if (window.lottie) {
      gradeLoadingAnimation = window.lottie.loadAnimation({
        container: document.getElementById('gradeLoadingAnimation'),
        renderer: 'svg',
        loop: true,
        autoplay: false,
        path: '../assets/loading.json'
      });
    }
  } catch (e) {
    console.log('Lottie animation not available');
  }
  
  // Handle grade form submission
  const gradeForm = document.getElementById('gradeForm');
  if (gradeForm) {
    gradeForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const loadingOverlay = document.getElementById('gradeLoadingOverlay');
      
      // Show loading overlay immediately
      loadingOverlay.classList.add('show');
      
      // Start loading animation
      if (gradeLoadingAnimation) {
        gradeLoadingAnimation.play();
      }
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving Grade...';
      
      // Force exactly 3 seconds delay before processing
      setTimeout(() => {
        fetch('update_grade.php', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.text();
        })
        .then(data => {
          // Check if response indicates success (you may need to adjust this based on your update_grade.php response)
          if (data.includes('success') || data.includes('updated') || !data.includes('error')) {
            if (typeof showNotification === 'function') {
              showNotification('Grade Updated Successfully!', 'success', 'The grade has been saved to the system.');
            }
            this.reset();
            setTimeout(() => {
              window.location.reload();
            }, 1500);
          } else {
            if (typeof showNotification === 'function') {
              showNotification('Failed to Update Grade', 'error', 'An error occurred while saving the grade.');
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (typeof showNotification === 'function') {
            showNotification('Network Error', 'error', 'Unable to connect to server.');
          }
        })
        .finally(() => {
          // Hide loading overlay
          loadingOverlay.classList.remove('show');
          if (gradeLoadingAnimation) {
            gradeLoadingAnimation.stop();
          }
          
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Save';
        });
      }, 3000); // Exactly 3 seconds delay
    });
  }
});
</script>
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
              <i class="bi bi-pencil update-grade-btn me-2" 
                 data-grade-id="<?= htmlspecialchars($g['id']) ?>"
                 data-student-id="<?= htmlspecialchars($g['student_id']) ?>"
                 data-subject-id="<?= htmlspecialchars($g['subject_id']) ?>"
                 data-current-grade="<?= htmlspecialchars($g['grade']) ?>"
                 data-student-name="<?= htmlspecialchars($g['student_name']) ?>"
                 data-subject-title="<?= htmlspecialchars($g['subject_title']) ?>"
                 title="Update Grade"
                 style="color: #0d6efd; cursor: pointer;"></i>
              <i class="bi bi-dash-circle remove-grade-btn" 
                 data-grade-id="<?= htmlspecialchars($g['id']) ?>"
                 data-student-name="<?= htmlspecialchars($g['student_name']) ?>"
                 data-subject-title="<?= htmlspecialchars($g['subject_title']) ?>"
                 title="Remove Grade"
                 style="color: #dc3545; cursor: pointer;"></i>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>

<!-- Loading Animation Overlay for Grades -->
<div id="gradeLoadingOverlay" class="loading-overlay">
    <div id="gradeLoadingAnimation" class="loading-animation"></div>
    <div class="loading-text">Updating Grade<span class="loading-dots"></span></div>
    <div class="loading-subtext">Please wait while we process the grade information</div>
</div>

<!-- Update Grade Modal -->
<div class="modal fade" id="updateGradeModal" tabindex="-1" aria-labelledby="updateGradeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <!-- Blue Header -->
      <div class="modal-header border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 1.5rem;">
        <h5 class="modal-title text-white fw-bold mb-0" id="updateGradeModalLabel" style="font-size: 1rem; letter-spacing: 0.5px;">
          <i class="bi bi-pencil me-2"></i>
          UPDATE GRADE
        </h5>
      </div>
      
      <div class="modal-body" style="padding: 1.5rem;">
        <form id="updateGradeForm">
          <input type="hidden" id="updateGradeId" name="grade_id">
          <input type="hidden" id="updateStudentId" name="student_id">
          <input type="hidden" id="updateSubjectId" name="subject_id">
          
          <!-- Student Info Card with Blue Border -->
          <div class="border rounded-3 p-3 mb-3" style="background-color: #eff6ff; border-color: #3b82f6 !important; border-width: 2px; font-size: 0.9rem;">
            <div class="mb-2">
              <strong style="color: #3b82f6;">Student:</strong> 
              <span id="updateModalStudentName" style="color: #374151;"></span>
            </div>
            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              <strong style="color: #3b82f6;">Subject:</strong> 
              <span id="updateModalSubjectTitle" style="color: #374151;"></span>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="updateGradeValue" class="form-label">New Grade</label>
            <input type="number" class="form-control" id="updateGradeValue" name="grade" 
                   required min="0" max="5" step="0.01" placeholder="Enter grade (0.00 - 5.00)">
            <div class="form-text">Enter a grade between 0.00 and 5.00</div>
          </div>
        </form>
      </div>
      
      <div class="modal-footer border-0" style="padding: 1rem 1.5rem 1.5rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px;">
          Cancel
        </button>
        <button type="button" class="btn text-white fw-semibold" id="confirmUpdateBtn" 
                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); 
                       font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px; border: none;">
          <i class="bi bi-check-circle me-1"></i>
          Update Grade
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modern Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <!-- Red Header -->
      <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 1.5rem;">
        <h5 class="modal-title text-white fw-bold mb-0" id="confirmDeleteModalLabel" style="font-size: 1rem; letter-spacing: 0.5px;">
          <i class="bi bi-exclamation-triangle me-2"></i>
          CONFIRM GRADE REMOVAL
        </h5>
      </div>
      
      <div class="modal-body" style="padding: 1.5rem;">
        <p class="mb-3" style="color: #374151; font-size: 0.95rem;">Are you sure you want to remove this grade?</p>
        
        <!-- Student Info Card with Red Border -->
        <div class="border rounded-3 p-3 mb-3" style="background-color: #fef2f2; border-color: #dc2626 !important; border-width: 2px; font-size: 0.9rem;">
          <div class="mb-2">
            <strong style="color: #dc2626;">Student:</strong> 
            <span id="modalStudentName" style="color: #374151;"></span>
          </div>
          <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <strong style="color: #dc2626;">Subject:</strong> 
            <span id="modalSubjectTitle" style="color: #374151;"></span>
          </div>
        </div>
        
        <p class="text-muted small mb-0" style="font-size: 0.85rem;">This action cannot be undone.</p>
      </div>
      
      <div class="modal-footer border-0" style="padding: 1rem 1.5rem 1.5rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px;">
          Cancel
        </button>
        <button type="button" class="btn text-white fw-semibold" id="confirmDeleteBtn" 
                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); 
                       font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 8px; border: none;">
          <i class="bi bi-trash me-1"></i>
          Remove Grade
        </button>
      </div>
    </div>
  </div>
</div>


