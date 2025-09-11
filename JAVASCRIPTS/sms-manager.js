// SMS Manager - SMS sending and log management functionality
class SMSManager {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.initializeSMSEventListeners();
      this.initializeSMSLogDeletion();
    });
  }

  initializeSMSEventListeners() {
    // SMS Send Modal
    document.addEventListener('click', (e) => {
      const sendBtn = e.target.closest('.send-sms-btn');
      if (sendBtn) {
        const logId = sendBtn.getAttribute('data-sms-log-id');
        const studentName = sendBtn.getAttribute('data-student-name');
        const parentPhone = sendBtn.getAttribute('data-parent-phone');
        const subjectTitle = sendBtn.getAttribute('data-subject-title');
        const grade = sendBtn.getAttribute('data-grade');

        // Fill modal fields
        document.getElementById('smsStudentName').textContent = studentName;
        document.getElementById('smsSubjectTitle').textContent = subjectTitle;
        document.getElementById('smsGrade').textContent = grade;
        document.getElementById('smsPhoneNumber').value = parentPhone;
        document.getElementById('smsLogId').value = logId;
        
        // Default message
        document.getElementById('smsMessage').value = `Dear Parent,\n\nThis is to inform you that your child, ${studentName}, has received a grade of ${grade} in ${subjectTitle}.\n\nThank you.\n- School`;

        // Show modal
        const smsModal = new bootstrap.Modal(document.getElementById('sendSmsModal'));
        smsModal.show();
      }
    });

    // SMS Send Confirmation
    const confirmSendSmsBtn = document.getElementById('confirmSendSmsBtn');
    if (confirmSendSmsBtn) {
      confirmSendSmsBtn.addEventListener('click', () => {
        this.sendSMS();
      });
    }
  }

  sendSMS() {
    const logId = document.getElementById('smsLogId').value;
    const phone = document.getElementById('smsPhoneNumber').value;
    const message = document.getElementById('smsMessage').value;
    const btn = document.getElementById('confirmSendSmsBtn');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';

    fetch('/VESTIL/PHP/send_sms.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ log_id: logId, phone: phone, message: message })
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response headers:', response.headers.get('content-type'));
      return response.text();
    })
    .then(text => {
      console.log('Raw response:', text);
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error('JSON parse error:', e);
        throw new Error('Server returned invalid JSON: ' + text.substring(0, 100));
      }
    })
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send me-1"></i>Send SMS';
      
      if (data.success) {
        // Hide modal
        const smsModal = bootstrap.Modal.getInstance(document.getElementById('sendSmsModal'));
        smsModal.hide();
        
        // Update status in table
        const row = document.querySelector(`.send-sms-btn[data-sms-log-id="${logId}"]`).closest('tr');
        const statusPill = row.querySelector('.status-pill');
        statusPill.textContent = 'Sent';
        statusPill.classList.remove('badge-pending');
        statusPill.classList.add('badge-sent');
        
        // Remove send button
        row.querySelector('.send-sms-btn').remove();
        
        showNotification('SMS sent successfully!', 'success');
      } else {
        showNotification('Failed to send SMS', 'error', data.message || 'An error occurred.');
      }
    })
    .catch(error => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send me-1"></i>Send SMS';
      showNotification('Network error', 'error', error.message);
    });
  }

  initializeSMSLogDeletion() {
    // Single SMS log deletion
    document.addEventListener('click', (e) => {
      if (e.target.closest('.remove-sms-log-btn')) {
        const btn = e.target.closest('.remove-sms-log-btn');
        const smsLogId = btn.dataset.smsLogId;
        const studentName = btn.dataset.studentName;
        const subjectTitle = btn.dataset.subjectTitle;
        
        // Populate modal with data
        document.getElementById('modalSmsStudentName').textContent = studentName;
        document.getElementById('modalSmsSubjectTitle').textContent = subjectTitle;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('confirmSmsDeleteModal'));
        modal.show();
        
        // Set up confirmation handler
        document.getElementById('confirmSmsDeleteBtn').onclick = () => {
          this.deleteSmsLog(smsLogId);
          modal.hide();
        };
      }
    });
    
    // Clear all data button
    document.getElementById('clearAllDataBtn')?.addEventListener('click', () => {
      const modal = new bootstrap.Modal(document.getElementById('confirmClearAllModal'));
      modal.show();
      
      document.getElementById('confirmClearAllBtn').onclick = () => {
        this.clearSmsLogs('clear_all');
        modal.hide();
      };
    });
  }

  deleteSmsLog(smsLogId) {
    const formData = new FormData();
    formData.append('action', 'delete_single');
    formData.append('sms_log_id', smsLogId);
    
    fetch('remove_test_sms_logs.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showNotification('SMS log removed successfully', 'success');
        // Reload the page to refresh the table
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to remove SMS log: ' + data.message, 'danger');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('An error occurred while removing SMS log', 'danger');
    });
  }

  clearSmsLogs(action) {
    const formData = new FormData();
    formData.append('action', action);
    
    fetch('remove_test_sms_logs.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const message = action === 'clear_test' 
          ? `Test data cleared successfully. ${data.remaining_logs || 0} real logs remaining.`
          : 'All SMS logs cleared successfully.';
        showNotification(message, 'success');
        // Reload the page to refresh the table
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to clear SMS logs: ' + data.message, 'danger');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('An error occurred while clearing SMS logs', 'danger');
    });
  }
}

// Initialize SMS manager
window.smsManager = new SMSManager();
