// Grade Manager - Grade removal and update functionality
class GradeManager {
  constructor() {
    this.currentGradeId = null;
    this.currentButton = null;
    this.currentUpdateData = null;
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.initializeGradeEventListeners();
    });
  }

  initializeGradeEventListeners() {
    // Handle grade update
    document.addEventListener('click', (e) => {
      if (e.target.closest('.update-grade-btn')) {
        e.preventDefault();
        const button = e.target.closest('.update-grade-btn');
        const gradeId = button.getAttribute('data-grade-id');
        const studentId = button.getAttribute('data-student-id');
        const subjectId = button.getAttribute('data-subject-id');
        const currentGrade = button.getAttribute('data-current-grade');
        const studentName = button.getAttribute('data-student-name');
        const subjectTitle = button.getAttribute('data-subject-title');
        
        // Store current values for modal
        this.currentUpdateData = {
          gradeId: gradeId,
          studentId: studentId,
          subjectId: subjectId,
          currentGrade: currentGrade,
          button: button
        };
        
        // Populate modal with data
        document.getElementById('updateGradeId').value = gradeId;
        document.getElementById('updateStudentId').value = studentId;
        document.getElementById('updateSubjectId').value = subjectId;
        document.getElementById('updateGradeValue').value = currentGrade;
        document.getElementById('updateModalStudentName').textContent = studentName;
        document.getElementById('updateModalSubjectTitle').textContent = subjectTitle;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('updateGradeModal'));
        modal.show();
      }
    });

    // Handle grade removal
    document.addEventListener('click', (e) => {
      if (e.target.closest('.remove-grade-btn')) {
        e.preventDefault();
        const button = e.target.closest('.remove-grade-btn');
        const gradeId = button.getAttribute('data-grade-id');
        const studentName = button.getAttribute('data-student-name');
        const subjectTitle = button.getAttribute('data-subject-title');
        
        // Store current values for modal
        this.currentGradeId = gradeId;
        this.currentButton = button;
        
        // Populate modal with data
        document.getElementById('modalStudentName').textContent = studentName;
        document.getElementById('modalSubjectTitle').textContent = subjectTitle;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
      }
    });

    // Handle update modal confirmation
    const confirmUpdateBtn = document.getElementById('confirmUpdateBtn');
    if (confirmUpdateBtn) {
      confirmUpdateBtn.addEventListener('click', () => {
        if (this.currentUpdateData) {
          const newGrade = document.getElementById('updateGradeValue').value;
          if (newGrade && newGrade >= 0 && newGrade <= 5) {
            this.updateGrade(this.currentUpdateData, newGrade);
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('updateGradeModal'));
            modal.hide();
          } else {
            showNotification('Invalid Grade', 'error', 'Please enter a valid grade between 0.00 and 5.00');
          }
        }
      });
    }

    // Handle delete modal confirmation
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
      confirmDeleteBtn.addEventListener('click', () => {
        if (this.currentGradeId && this.currentButton) {
          this.removeGrade(this.currentGradeId, this.currentButton);
          // Hide modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
          modal.hide();
        }
      });
    }
  }

  updateGrade(updateData, newGrade) {
    const button = updateData.button;
    
    // Disable button and show loading state
    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    
    // Create form data
    const formData = new FormData();
    formData.append('grade_id', updateData.gradeId);
    formData.append('student_id', updateData.studentId);
    formData.append('subject_id', updateData.subjectId);
    formData.append('grade', newGrade);
    
    // Send request
    fetch('update_existing_grade.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      console.log('Update response status:', response.status);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('Update response data:', data);
      if (data.success) {
        // Update the grade display in the table
        const row = button.closest('tr');
        const gradeCell = row.querySelector('td:nth-child(5) .badge');
        if (gradeCell) {
          gradeCell.textContent = newGrade;
          
          // Update badge class based on new grade
          const gradeVal = parseFloat(newGrade);
          gradeCell.className = 'badge ';
          if (gradeVal >= 1.0 && gradeVal <= 2.0) {
            gradeCell.className += 'badge-grade-green';
          } else if (gradeVal > 2.0 && gradeVal <= 2.9) {
            gradeCell.className += 'badge-grade-amber';
          } else {
            gradeCell.className += 'badge-grade-red';
          }
        }
        
        // Update the data attributes for future updates
        button.setAttribute('data-current-grade', newGrade);
        
        // Update timestamp
        const timestampCell = row.querySelector('td:nth-child(6) .badge');
        if (timestampCell) {
          const now = new Date();
          const formattedTime = `${now.getFullYear().toString().substr(-2)}/${now.getMonth() + 1}/${now.getDate()} ${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;
          timestampCell.textContent = formattedTime;
        }
        
        // Show success message
        showNotification('Grade Updated Successfully!', 'success', `Grade has been updated to ${newGrade}`);
      } else {
        // Show error message
        showNotification('Failed to Update Grade', 'error', data.message || 'An unexpected error occurred');
      }
    })
    .catch(error => {
      console.error('Update error details:', error);
      showNotification('Network Error', 'error', `Unable to connect to server: ${error.message}`);
    })
    .finally(() => {
      // Restore button
      button.disabled = false;
      button.innerHTML = originalContent;
    });
  }

  removeGrade(gradeId, button) {
    // Disable button and show loading state
    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    
    // Create form data
    const formData = new FormData();
    formData.append('grade_id', gradeId);
    
    // Send request
    fetch('remove_grade.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        // Remove the table row
        const row = button.closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0';
        setTimeout(() => {
          row.remove();
        }, 300);
        
        // Show success message
        showNotification('Grade Removed Successfully', 'success', 'The grade has been permanently deleted from the system.');
      } else {
        // Show error message
        showNotification('Failed to Remove Grade', 'error', data.message || 'An unexpected error occurred');
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
      }
    })
    .catch(error => {
      console.error('Error details:', error);
      showNotification('Network Error', 'error', `Unable to connect to server: ${error.message}`);
      // Restore button
      button.disabled = false;
      button.innerHTML = originalContent;
    });
  }
}

// Initialize grade manager
window.gradeManager = new GradeManager();
