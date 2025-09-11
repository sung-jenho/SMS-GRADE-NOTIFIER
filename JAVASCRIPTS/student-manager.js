// Student Manager - Student CRUD operations functionality
class StudentManager {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.initializeStudentEventListeners();
      this.addLoadingAnimationStyles();
      this.initializeLoadingAnimation();
    });
  }

  initializeLoadingAnimation() {
    try {
      if (window.lottie) {
        // Preload the loading animation for students
        this.loadingAnimation = window.lottie.loadAnimation({
          container: document.getElementById('studentLoadingAnimation'),
          renderer: 'svg',
          loop: true,
          autoplay: false,
          path: '../assets/loading.json'
        });
      }
    } catch (e) {
      console.log('Lottie animation not available');
    }
  }

  addLoadingAnimationStyles() {
    if (!document.getElementById('loading-animation-style')) {
      const style = document.createElement('style');
      style.id = 'loading-animation-style';
      style.textContent = `
        @keyframes loading-shimmer {
          0% { left: -100%; }
          100% { left: 100%; }
        }
        @keyframes notification-bounce {
          0% { transform: scale(0.3) rotate(-10deg); opacity: 0; }
          50% { transform: scale(1.1) rotate(5deg); opacity: 0.8; }
          100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        @keyframes form-success-pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.02); }
          100% { transform: scale(1); }
        }
      `;
      document.head.appendChild(style);
    }
  }

  initializeStudentEventListeners() {
    // Add Student Form
    const addStudentForm = document.getElementById('addStudentForm');
    if (addStudentForm) {
      addStudentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.addStudent();
      });
    }

    // Edit Student Buttons
    document.addEventListener('click', (e) => {
      if (e.target.closest('.edit-student-btn')) {
        const btn = e.target.closest('.edit-student-btn');
        this.editStudent(btn);
      }
    });

    // Delete Student Buttons
    document.addEventListener('click', (e) => {
      if (e.target.closest('.delete-student-btn')) {
        const btn = e.target.closest('.delete-student-btn');
        const studentId = btn.dataset.studentId;
        const studentName = btn.dataset.studentName;
        
        // Populate modal
        document.getElementById('deleteStudentName').textContent = studentName;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('deleteStudentModal'));
        modal.show();
        
        // Set up confirmation handler
        document.getElementById('confirmDeleteStudentBtn').onclick = () => {
          this.deleteStudent(studentId);
          modal.hide();
        };
      }
    });

    // Edit Student Form
    const editStudentForm = document.getElementById('editStudentForm');
    if (editStudentForm) {
      editStudentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.updateStudent();
      });
    }
  }

  addStudent() {
    const form = document.getElementById('addStudentForm');
    const formData = new FormData(form);
    formData.append('action', 'create');
    
    // Show loading overlay immediately
    const loadingOverlay = document.getElementById('studentLoadingOverlay');
    loadingOverlay.classList.add('show');
    
    // Start loading animation
    if (this.loadingAnimation) {
      this.loadingAnimation.play();
    }
    
    // Update button state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding Student...';
    submitBtn.disabled = true;
    
    // Force exactly 3 seconds delay before processing
    setTimeout(() => {
      fetch('manage_student.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Get form data for detailed success message
          const studentName = formData.get('name');
          const studentNumber = formData.get('student_number');
          
          showNotification(
            'Student Added Successfully!', 
            'success', 
            `${studentName} (${studentNumber}) has been added to the system.`
          );
          
          // Add a subtle success animation to the form
          form.style.transition = 'all 0.4s ease';
          form.style.animation = 'form-success-pulse 0.6s ease-out';
          form.style.backgroundColor = 'rgba(34, 197, 94, 0.05)';
          form.style.borderRadius = '16px';
          form.style.boxShadow = '0 0 0 2px rgba(34, 197, 94, 0.2)';
          
          setTimeout(() => {
            form.style.animation = '';
            form.style.backgroundColor = '';
            form.style.boxShadow = '';
          }, 600);
          
          form.reset();
          
          // Reload the page to refresh the table
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          showNotification('Failed to add student', 'danger', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while adding student', 'danger');
      })
      .finally(() => {
        // Hide loading overlay
        loadingOverlay.classList.remove('show');
        if (this.loadingAnimation) {
          this.loadingAnimation.stop();
        }
        
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    }, 3000); // Exactly 3 seconds delay
  }

  editStudent(btn) {
    const studentId = btn.dataset.studentId;
    const studentNumber = btn.dataset.studentNumber;
    const name = btn.dataset.name;
    const course = btn.dataset.course;
    const yearLevel = btn.dataset.yearLevel;
    const phoneNumber = btn.dataset.phoneNumber;
    const photo = btn.dataset.photo;
    
    // Populate edit form
    document.getElementById('edit_student_id').value = studentId;
    document.getElementById('edit_student_number').value = studentNumber;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_course').value = course;
    document.getElementById('edit_year_level').value = yearLevel;
    document.getElementById('edit_phone_number').value = phoneNumber;
    
    // Handle current photo preview
    const currentPhotoPreview = document.getElementById('currentPhotoPreview');
    const currentPhotoImg = document.getElementById('currentPhotoImg');
    
    if (photo && photo.trim() !== '') {
      currentPhotoImg.src = `../uploads/students/${photo}`;
      currentPhotoPreview.style.display = 'block';
    } else {
      currentPhotoPreview.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
    modal.show();
  }

  updateStudent() {
    const form = document.getElementById('editStudentForm');
    const formData = new FormData(form);
    formData.append('action', 'update');
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch('manage_student.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showNotification('Student Updated Successfully!', 'success', 'The student information has been updated in the system.');
        // Close modal and reload page
        const modal = bootstrap.Modal.getInstance(document.getElementById('editStudentModal'));
        modal.hide();
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to Update Student', 'danger', data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Update Error', 'danger', 'An unexpected error occurred while updating the student');
    })
    .finally(() => {
      // Reset button state
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  }

  deleteStudent(studentId) {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('student_id', studentId);
    
    fetch('manage_student.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showNotification('Student Deleted Successfully!', 'success', 'The student has been permanently removed from the system.');
        // Reload the page to refresh the table
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to Delete Student', 'danger', data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Delete Error', 'danger', 'An unexpected error occurred while deleting the student');
    });
  }
}

// Initialize student manager
window.studentManager = new StudentManager();
