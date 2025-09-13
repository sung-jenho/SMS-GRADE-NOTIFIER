// subject-manager.js - Modular Subject Management for SMS Grade Notifier
// Handles add, edit, delete, and modal logic for subjects

class SubjectManager {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.cacheElements();
      this.bindEvents();
    });
  }

  cacheElements() {
    this.addSubjectForm = document.getElementById('addSubjectForm');
    this.editSubjectForm = document.getElementById('editSubjectForm');
    this.saveEditSubjectBtn = document.getElementById('saveEditSubjectBtn');
    this.confirmDeleteSubjectBtn = document.getElementById('confirmDeleteSubjectBtn');
    this.subjectLoadingAnimation = null;
    this.subjectLoadingOverlay = document.getElementById('subjectLoadingOverlay');
    this.initializeLoadingAnimation();
  }

  initializeLoadingAnimation() {
    try {
      if (typeof lottie !== 'undefined' && document.getElementById('subjectLoadingAnimation')) {
        this.subjectLoadingAnimation = lottie.loadAnimation({
          container: document.getElementById('subjectLoadingAnimation'),
          renderer: 'svg',
          loop: true,
          autoplay: false,
          path: '../assets/loading.json'
        });
      }
    } catch (e) {
      console.warn('Could not initialize subject loading animation:', e);
    }
  }

  bindEvents() {
    if (this.addSubjectForm) {
      this.addSubjectForm.addEventListener('submit', (e) => this.handleAddSubject(e));
    }
    if (this.saveEditSubjectBtn) {
      this.saveEditSubjectBtn.addEventListener('click', () => this.handleSaveEditSubject());
    }
    if (this.confirmDeleteSubjectBtn) {
      this.confirmDeleteSubjectBtn.addEventListener('click', () => this.handleDeleteSubject());
    }
    // Pencil and trash icons use event delegation
    document.querySelector('.subjects-table-container').addEventListener('click', (e) => {
      if (e.target.classList.contains('bi-pencil')) {
        const id = e.target.getAttribute('data-id');
        this.editSubject(id);
      } else if (e.target.classList.contains('bi-dash-circle')) {
        const id = e.target.getAttribute('data-id');
        this.deleteSubject(id, e.target);
      }
    });
    // Time inputs for schedule
    ['start_time','end_time'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('change', () => this.combineSchedule());
    });
    ['editStartTime','editEndTime'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('change', () => this.combineEditSchedule());
    });
  }

  formatTime12Hour(time24) {
    if (!time24) return '';
    const [hours, minutes] = time24.split(':');
    const hour12 = hours % 12 || 12;
    const ampm = hours >= 12 ? 'PM' : 'AM';
    return `${hour12}:${minutes} ${ampm}`;
  }

  convertTo24Hour(time12) {
    if (!time12) return '';
    const match = time12.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
    if (!match) return time12;
    let [, hours, minutes, ampm] = match;
    hours = parseInt(hours);
    if (ampm.toUpperCase() === 'PM' && hours !== 12) hours += 12;
    if (ampm.toUpperCase() === 'AM' && hours === 12) hours = 0;
    return `${hours.toString().padStart(2, '0')}:${minutes}`;
  }

  combineSchedule() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    if (startTime && endTime) {
      const schedule = `${this.formatTime12Hour(startTime)}-${this.formatTime12Hour(endTime)}`;
      document.getElementById('schedule').value = schedule;
    }
  }

  combineEditSchedule() {
    const startTime = document.getElementById('editStartTime').value;
    const endTime = document.getElementById('editEndTime').value;
    if (startTime && endTime) {
      const schedule = `${this.formatTime12Hour(startTime)}-${this.formatTime12Hour(endTime)}`;
      document.getElementById('editSchedule').value = schedule;
    }
  }

  handleAddSubject(e) {
    e.preventDefault();
    this.combineSchedule();
    const formData = new FormData(this.addSubjectForm);
    const submitBtn = this.addSubjectForm.querySelector('button[type="submit"]');
    this.subjectLoadingOverlay.classList.add('show');
    if (this.subjectLoadingAnimation) {
      this.subjectLoadingAnimation.play();
    }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding Subject...';
    setTimeout(() => {
      fetch('../PHP/manage_subject.php', {
        method: 'POST', body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showNotification('Subject added successfully!', 'success', 'The new subject has been added to your course list');
          this.addSubjectForm.reset();
          setTimeout(() => location.reload(), 2500);
        } else {
          showNotification('Failed to add subject', 'error', data.message || 'Please check your input and try again');
        }
      })
      .catch(() => showNotification('Network error', 'error', 'An error occurred while adding the subject'))
      .finally(() => {
        this.subjectLoadingOverlay.classList.remove('show');
        if (this.subjectLoadingAnimation) {
          this.subjectLoadingAnimation.stop();
        }
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Subject';
      });
    }, 3000);
  }

  handleSaveEditSubject() {
    this.combineEditSchedule();
    const formData = new FormData(this.editSubjectForm);
    formData.append('action', 'edit');
    this.saveEditSubjectBtn.disabled = true;
    this.saveEditSubjectBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Saving...';
    fetch('../PHP/manage_subject.php', {
      method: 'POST', body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showNotification('Subject updated successfully!', 'success', 'All changes have been saved to your course list');
        bootstrap.Modal.getInstance(document.getElementById('editSubjectModal')).hide();
        setTimeout(() => location.reload(), 2500);
      } else {
        showNotification('Failed to update subject', 'error', data.message || 'Please check your input and try again');
      }
    })
    .catch(() => showNotification('Network error', 'error', 'An error occurred while updating the subject'))
    .finally(() => {
      this.saveEditSubjectBtn.disabled = false;
      this.saveEditSubjectBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Save Changes';
    });
  }

  handleDeleteSubject() {
    if (!window.currentDeleteId) return;
    this.confirmDeleteSubjectBtn.disabled = true;
    this.confirmDeleteSubjectBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
    fetch('../PHP/manage_subject.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'action=delete&id=' + window.currentDeleteId
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showNotification('Subject deleted successfully!', 'success', 'The subject has been removed from your course list');
        bootstrap.Modal.getInstance(document.getElementById('deleteSubjectModal')).hide();
        setTimeout(() => location.reload(), 2500);
      } else {
        showNotification('Failed to delete subject', 'error', data.message || 'Please try again');
      }
    })
    .catch(() => showNotification('Network error', 'error', 'An error occurred while deleting the subject'))
    .finally(() => {
      this.confirmDeleteSubjectBtn.disabled = false;
      this.confirmDeleteSubjectBtn.innerHTML = 'Delete Subject';
    });
  }

  editSubject(id) {
    fetch('../PHP/get_subject.php?id=' + id)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const subject = data.subject;
          document.getElementById('editSubjectId').value = id;
          document.getElementById('editSubjectCode').value = subject.subject_code || '';
          document.getElementById('editSubjectTitle').value = subject.subject_title || '';
          document.getElementById('editUnits').value = subject.units || '';
          document.getElementById('editDays').value = subject.days || '';
          document.getElementById('editRoom').value = subject.room || '';
          if (subject.schedule) {
            const scheduleMatch = subject.schedule.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/i);
            if (scheduleMatch) {
              const [, sH, sM, sA, eH, eM, eA] = scheduleMatch;
              document.getElementById('editStartTime').value = this.convertTo24Hour(`${sH}:${sM} ${sA || eA}`);
              document.getElementById('editEndTime').value = this.convertTo24Hour(`${eH}:${eM} ${eA}`);
            }
            document.getElementById('editSchedule').value = subject.schedule;
          } else {
            document.getElementById('editStartTime').value = '';
            document.getElementById('editEndTime').value = '';
            document.getElementById('editSchedule').value = '';
          }
          new bootstrap.Modal(document.getElementById('editSubjectModal')).show();
        } else {
          showNotification('Failed to load subject data', 'error', 'Please try again');
        }
      })
      .catch(() => showNotification('Network error', 'error', 'An error occurred while loading subject data'));
  }

  deleteSubject(id, iconEl) {
    window.currentDeleteId = id;
    const row = iconEl.closest('tr');
    const subjectCode = row.querySelector('td:first-child').textContent.trim();
    const subjectTitle = row.querySelector('td:nth-child(2)').textContent.trim();
    document.getElementById('deleteSubjectName').textContent = `${subjectCode} - ${subjectTitle}`;
    new bootstrap.Modal(document.getElementById('deleteSubjectModal')).show();
  }

  showToast(type, message) {
    const toastId = type === 'success' ? 'successToast' : 'errorToast';
    const messageId = type === 'success' ? 'successMessage' : 'errorMessage';
    document.getElementById(messageId).textContent = message;
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
  }
}

window.subjectManager = new SubjectManager();
