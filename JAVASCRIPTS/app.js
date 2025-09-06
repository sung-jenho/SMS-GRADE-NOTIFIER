// Dark mode toggle and Chart.js initialization

function setDarkMode(on, persist = true) {
  document.body.classList.toggle('dark-mode', on);
  if (persist) localStorage.setItem('darkMode', on ? '1' : '0');
}

// Add required CSS animations
if (!document.getElementById('notification-animations')) {
  const style = document.createElement('style');
  style.id = 'notification-animations';
  style.textContent = `
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
    @keyframes loading-shimmer {
      0% { left: -100%; }
      100% { left: 100%; }
    }
  `;
  document.head.appendChild(style);
}

// Global notification system
function showNotification(message, type = 'info', details = null) {
  // Remove any existing notifications of the same type
  const existingNotifications = document.querySelectorAll('.custom-notification');
  existingNotifications.forEach(notif => notif.remove());
  
  // Create notification container if it doesn't exist
  let notificationContainer = document.getElementById('notification-container');
  if (!notificationContainer) {
    notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    notificationContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 420px;';
    document.body.appendChild(notificationContainer);
  }
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = 'custom-notification';
  
  // Determine icon and colors based on type
  let icon, bgColor, borderColor, textColor, shadowColor;
  switch (type) {
    case 'success':
      icon = 'bi-check-circle-fill';
      bgColor = '#f0fdf4';
      borderColor = '#22c55e';
      textColor = '#15803d';
      shadowColor = 'rgba(34, 197, 94, 0.25)';
      break;
    case 'error':
    case 'danger':
      icon = 'bi-exclamation-triangle-fill';
      bgColor = '#fef2f2';
      borderColor = '#ef4444';
      textColor = '#dc2626';
      shadowColor = 'rgba(239, 68, 68, 0.25)';
      break;
    case 'warning':
      icon = 'bi-exclamation-triangle-fill';
      bgColor = '#fffbeb';
      borderColor = '#f59e0b';
      textColor = '#d97706';
      shadowColor = 'rgba(245, 158, 11, 0.25)';
      break;
    default:
      icon = 'bi-info-circle-fill';
      bgColor = '#eff6ff';
      borderColor = '#3b82f6';
      textColor = '#1d4ed8';
      shadowColor = 'rgba(59, 130, 246, 0.25)';
  }
  
  // Create notification HTML
  notification.innerHTML = `
    <div class="notification-content" style="
      background: ${bgColor};
      border: 2px solid ${borderColor};
      border-radius: 16px;
      padding: 20px 24px;
      margin-bottom: 16px;
      box-shadow: 0 8px 32px ${shadowColor}, 0 2px 8px rgba(0,0,0,0.1);
      transform: translateX(100%);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
    ">
      <div class="d-flex align-items-start">
        <div class="notification-icon me-3" style="
          color: ${textColor}; 
          font-size: 1.5rem; 
          margin-top: 2px;
          animation: notification-bounce 0.6s ease-out;
        ">
          <i class="bi ${icon}"></i>
        </div>
        <div class="notification-body flex-grow-1">
          <div class="notification-message fw-bold" style="
            color: ${textColor}; 
            margin-bottom: 6px;
            font-size: 1rem;
            line-height: 1.4;
          ">
            ${message}
          </div>
          ${details ? `<div class="notification-details" style="
            color: ${textColor}; 
            opacity: 0.85;
            font-size: 0.875rem;
            line-height: 1.3;
          ">${details}</div>` : ''}
        </div>
        <button type="button" class="btn-close notification-close" style="
          background: rgba(255,255,255,0.2);
          border: none;
          color: ${textColor};
          opacity: 0.8;
          font-size: 1.1rem;
          padding: 6px;
          margin-left: 16px;
          cursor: pointer;
          border-radius: 8px;
          width: 28px;
          height: 28px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        " onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.2)'">&times;</button>
      </div>
      <div class="notification-progress" style="
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, ${borderColor}, ${borderColor}aa);
        width: 100%;
        transform: scaleX(1);
        transform-origin: left;
        transition: transform 5s linear;
        border-radius: 0 0 16px 16px;
      "></div>
    </div>
  `;
  
  notificationContainer.appendChild(notification);
  
  // Animate in
  setTimeout(() => {
    notification.querySelector('.notification-content').style.transform = 'translateX(0)';
  }, 10);
  
  // Handle close button
  const closeBtn = notification.querySelector('.notification-close');
  closeBtn.addEventListener('click', () => {
    removeNotification(notification);
  });
  
  // Start progress bar animation
  setTimeout(() => {
    const progressBar = notification.querySelector('.notification-progress');
    if (progressBar) {
      progressBar.style.transform = 'scaleX(0)';
    }
  }, 100);
  
  // Auto remove after 6 seconds for success, 4 seconds for others
  const autoRemoveDelay = type === 'success' ? 6000 : 4000;
  setTimeout(() => {
    removeNotification(notification);
  }, autoRemoveDelay);
}

function removeNotification(notification) {
  if (notification && notification.parentNode) {
    const content = notification.querySelector('.notification-content');
    content.style.transform = 'translateX(100%)';
    content.style.opacity = '0';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove();
      }
    }, 300);
  }
}


document.addEventListener('DOMContentLoaded', function () {
  const darkModeInput = document.getElementById('darkModeToggle');
  const stored = localStorage.getItem('darkMode');
  const media = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
  const isDark = stored === '1' ? true : stored === '0' ? false : (media ? media.matches : false);
  setDarkMode(isDark, false);
  if (darkModeInput) {
    // If it's a checkbox switch, sync its checked state and listen to changes
    if (darkModeInput.type === 'checkbox') {
      darkModeInput.checked = isDark;
      darkModeInput.addEventListener('change', function () {
        const next = !!darkModeInput.checked;
        setDarkMode(next, true);
      });
    } else {
      // Fallback for old button behavior
      darkModeInput.onclick = function () {
        const next = !document.body.classList.contains('dark-mode');
        setDarkMode(next, true);
      };
    }
  }

  if (window.Chart) {
    const smsCtx = document.getElementById('smsDeliveryChart');
    if (smsCtx) {
      // Load real SMS delivery data
      loadSMSDeliveryChart(smsCtx);
    }
  }

  // Function to load SMS delivery chart with real data
  function loadSMSDeliveryChart(ctx) {
    fetch('PHP/sms_stats_api.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update the legend with real stats
          updateSMSLegend(data.stats);
          
          // Create chart with real data
          new Chart(ctx, {
            type: 'line',
            data: {
              labels: data.chart.labels,
              datasets: [{
                label: 'Success Rate %',
                data: data.chart.rates,
                borderColor: '#60a5fa',
                backgroundColor: 'rgba(96,165,250,0.25)',
                fill: true,
                tension: 0.35,
                borderWidth: 2,
                pointRadius: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: {
                y: { 
                  min: 0, 
                  max: 100, 
                  ticks: { callback: v => v + '%' }, 
                  grid: { color: 'rgba(0,0,0,0.05)' } 
                },
                x: { grid: { display: false } }
              }
            }
          });
        } else {
          // Fallback to default chart if API fails
          createDefaultSMSChart(ctx);
        }
      })
      .catch(error => {
        console.warn('Failed to load SMS stats:', error);
        createDefaultSMSChart(ctx);
      });
  }

  // Function to update SMS legend with real data
  function updateSMSLegend(stats) {
    const legendContainer = document.querySelector('#smsDeliveryChart').closest('.card-body').querySelector('.d-flex.justify-content-around');
    if (legendContainer) {
      legendContainer.innerHTML = `
        <div><span class="legend-dot legend-green"></span>${stats.delivered.toLocaleString()} Delivered</div>
        <div><span class="legend-dot legend-amber"></span>${stats.pending.toLocaleString()} Pending</div>
        <div><span class="legend-dot legend-red"></span>${stats.failed.toLocaleString()} Failed</div>
      `;
    }
  }

  // Fallback function for default chart
  function createDefaultSMSChart(ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Success Rate %',
          data: [0, 0, 0, 0, 0, 0, 0],
          borderColor: '#d1d5db',
          backgroundColor: 'rgba(209,213,219,0.25)',
          fill: true,
          tension: 0.35,
          borderWidth: 2,
          pointRadius: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { min: 0, max: 100, ticks: { callback: v => v + '%' }, grid: { color: 'rgba(0,0,0,0.05)' } },
          x: { grid: { display: false } }
        }
      }
    });
    
    // Show "No data" message in legend
    const legendContainer = document.querySelector('#smsDeliveryChart').closest('.card-body').querySelector('.d-flex.justify-content-around');
    if (legendContainer) {
      legendContainer.innerHTML = `
        <div><span class="legend-dot legend-green"></span>0 Delivered</div>
        <div><span class="legend-dot legend-amber"></span>0 Pending</div>
        <div><span class="legend-dot legend-red"></span>0 Failed</div>
      `;
    }
  }

  const gradeCtx = document.getElementById('gradeDistributionChart');
  if (gradeCtx) {
    // Load real grade distribution data
    loadGradeDistributionChart(gradeCtx);
  }

  // Function to load grade distribution chart with real data
  function loadGradeDistributionChart(ctx) {
    fetch('PHP/grade_distribution_api.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Create chart with real data
          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: data.labels,
              datasets: [{
                data: data.data,
                backgroundColor: data.colors,
                borderWidth: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              cutout: '70%',
              plugins: { legend: { display: false } }
            }
          });
        } else {
          // Fallback to default chart if API fails
          createDefaultGradeChart(ctx);
        }
      })
      .catch(error => {
        console.warn('Failed to load grade distribution:', error);
        createDefaultGradeChart(ctx);
      });
  }

  // Fallback function for default grade chart
  function createDefaultGradeChart(ctx) {
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['A', 'B', 'C', 'D', 'F'],
        datasets: [{
          data: [0, 0, 0, 0, 0],
          backgroundColor: ['#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: { legend: { display: false } }
      }
    });
  }

  // Handle grade removal
  let currentGradeId = null;
  let currentButton = null;

  document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-grade-btn')) {
      e.preventDefault();
      const button = e.target.closest('.remove-grade-btn');
      const gradeId = button.getAttribute('data-grade-id');
      const studentName = button.getAttribute('data-student-name');
      const subjectTitle = button.getAttribute('data-subject-title');
      
      // Store current values for modal
      currentGradeId = gradeId;
      currentButton = button;
      
      // Populate modal with data
      document.getElementById('modalStudentName').textContent = studentName;
      document.getElementById('modalSubjectTitle').textContent = subjectTitle;
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
      modal.show();
    }
  });

  // Handle modal confirmation
  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (currentGradeId && currentButton) {
      removeGrade(currentGradeId, currentButton);
      // Hide modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
      modal.hide();
    }
  });

  function removeGrade(gradeId, button) {
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

  function showNotification(message, type = 'info', details = null) {
    // Remove any existing notifications of the same type
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    // Create notification container if it doesn't exist
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
      notificationContainer = document.createElement('div');
      notificationContainer.id = 'notification-container';
      notificationContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 420px;';
      document.body.appendChild(notificationContainer);
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'custom-notification';
    
    // Determine icon and colors based on type
    let icon, bgColor, borderColor, textColor, shadowColor;
    switch (type) {
      case 'success':
        icon = 'bi-check-circle-fill';
        bgColor = '#f0fdf4';
        borderColor = '#22c55e';
        textColor = '#15803d';
        shadowColor = 'rgba(34, 197, 94, 0.25)';
        break;
      case 'error':
      case 'danger':
        icon = 'bi-exclamation-triangle-fill';
        bgColor = '#fef2f2';
        borderColor = '#ef4444';
        textColor = '#dc2626';
        shadowColor = 'rgba(239, 68, 68, 0.25)';
        break;
      case 'warning':
        icon = 'bi-exclamation-triangle-fill';
        bgColor = '#fffbeb';
        borderColor = '#f59e0b';
        textColor = '#d97706';
        shadowColor = 'rgba(245, 158, 11, 0.25)';
        break;
      default:
        icon = 'bi-info-circle-fill';
        bgColor = '#eff6ff';
        borderColor = '#3b82f6';
        textColor = '#1d4ed8';
        shadowColor = 'rgba(59, 130, 246, 0.25)';
    }
    
    // Create notification HTML
    notification.innerHTML = `
      <div class="notification-content" style="
        background: ${bgColor};
        border: 2px solid ${borderColor};
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 16px;
        box-shadow: 0 8px 32px ${shadowColor}, 0 2px 8px rgba(0,0,0,0.1);
        transform: translateX(100%);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
      ">
        <div class="d-flex align-items-start">
          <div class="notification-icon me-3" style="
            color: ${textColor}; 
            font-size: 1.5rem; 
            margin-top: 2px;
            animation: notification-bounce 0.6s ease-out;
          ">
            <i class="bi ${icon}"></i>
          </div>
          <div class="notification-body flex-grow-1">
            <div class="notification-message fw-bold" style="
              color: ${textColor}; 
              margin-bottom: 6px;
              font-size: 1rem;
              line-height: 1.4;
            ">
              ${message}
            </div>
            ${details ? `<div class="notification-details" style="
              color: ${textColor}; 
              opacity: 0.85;
              font-size: 0.875rem;
              line-height: 1.3;
            ">${details}</div>` : ''}
          </div>
          <button type="button" class="btn-close notification-close" style="
            background: rgba(255,255,255,0.2);
            border: none;
            color: ${textColor};
            opacity: 0.8;
            font-size: 1.1rem;
            padding: 6px;
            margin-left: 16px;
            cursor: pointer;
            border-radius: 8px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
          " onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.2)'">&times;</button>
        </div>
        <div class="notification-progress" style="
          position: absolute;
          bottom: 0;
          left: 0;
          height: 4px;
          background: linear-gradient(90deg, ${borderColor}, ${borderColor}aa);
          width: 100%;
          transform: scaleX(1);
          transform-origin: left;
          transition: transform 5s linear;
          border-radius: 0 0 16px 16px;
        "></div>
      </div>
    `;
    
    notificationContainer.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
      notification.querySelector('.notification-content').style.transform = 'translateX(0)';
    }, 10);
    
    // Handle close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
      removeNotification(notification);
    });
    
    // Start progress bar animation
    setTimeout(() => {
      const progressBar = notification.querySelector('.notification-progress');
      if (progressBar) {
        progressBar.style.transform = 'scaleX(0)';
      }
    }, 100);
    
    // Auto remove after 6 seconds for success, 4 seconds for others
    const autoRemoveDelay = type === 'success' ? 6000 : 4000;
    setTimeout(() => {
      removeNotification(notification);
    }, autoRemoveDelay);
  }
  
  function removeNotification(notification) {
    if (notification && notification.parentNode) {
      const content = notification.querySelector('.notification-content');
      content.style.transform = 'translateX(100%)';
      content.style.opacity = '0';
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 300);
    }
  }
});

// SMS Log Deletion Functionality
document.addEventListener('DOMContentLoaded', function() {
  // Single SMS log deletion
  document.addEventListener('click', function(e) {
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
      document.getElementById('confirmSmsDeleteBtn').onclick = function() {
        deleteSmsLog(smsLogId);
        modal.hide();
      };
    }
  });
  
  // Clear test data button
  document.getElementById('clearTestDataBtn')?.addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('confirmClearTestModal'));
    modal.show();
    
    document.getElementById('confirmClearTestBtn').onclick = function() {
      clearSmsLogs('clear_test');
      modal.hide();
    };
  });
  
  // Clear all data button
  document.getElementById('clearAllDataBtn')?.addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('confirmClearAllModal'));
    modal.show();
    
    document.getElementById('confirmClearAllBtn').onclick = function() {
      clearSmsLogs('clear_all');
      modal.hide();
    };
  });
});

function deleteSmsLog(smsLogId) {
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

function clearSmsLogs(action) {
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

// Student Management Functionality
document.addEventListener('DOMContentLoaded', function() {
  // Add Student Form
  const addStudentForm = document.getElementById('addStudentForm');
  if (addStudentForm) {
    addStudentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      addStudent();
    });
  }

  // Edit Student Buttons
  document.addEventListener('click', function(e) {
    if (e.target.closest('.edit-student-btn')) {
      const btn = e.target.closest('.edit-student-btn');
      editStudent(btn);
    }
  });

  // Delete Student Buttons
  document.addEventListener('click', function(e) {
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
      document.getElementById('confirmDeleteStudentBtn').onclick = function() {
        deleteStudent(studentId);
        modal.hide();
      };
    }
  });

  // Edit Student Form
  const editStudentForm = document.getElementById('editStudentForm');
  if (editStudentForm) {
    editStudentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      updateStudent();
    });
  }
});

function addStudent() {
  const form = document.getElementById('addStudentForm');
  const formData = new FormData(form);
  formData.append('action', 'create');
  
  // Show loading state
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding Student...';
  submitBtn.disabled = true;
  submitBtn.style.position = 'relative';
  submitBtn.style.overflow = 'hidden';
  
  // Add loading animation
  const loadingOverlay = document.createElement('div');
  loadingOverlay.style.cssText = `
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading-shimmer 1.5s infinite;
  `;
  submitBtn.appendChild(loadingOverlay);
  
  // Add CSS for loading and notification animations
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
  
  fetch('manage_student.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Get form data for detailed success message
      const formData = new FormData(form);
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
    // Reset button state
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    submitBtn.style.position = '';
    submitBtn.style.overflow = '';
    
    // Remove loading overlay
    const loadingOverlay = submitBtn.querySelector('div[style*="loading-shimmer"]');
    if (loadingOverlay) {
      loadingOverlay.remove();
    }
  });
}

function editStudent(btn) {
  const studentId = btn.dataset.studentId;
  const studentNumber = btn.dataset.studentNumber;
  const name = btn.dataset.name;
  const course = btn.dataset.course;
  const yearLevel = btn.dataset.yearLevel;
  const phoneNumber = btn.dataset.phoneNumber;
  
  // Populate edit form
  document.getElementById('edit_student_id').value = studentId;
  document.getElementById('edit_student_number').value = studentNumber;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_course').value = course;
  document.getElementById('edit_year_level').value = yearLevel;
  document.getElementById('edit_phone_number').value = phoneNumber;
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
  modal.show();
}

function updateStudent() {
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

function deleteStudent(studentId) {
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


