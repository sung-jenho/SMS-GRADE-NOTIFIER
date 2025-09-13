// Notifications System - Global notification management
class NotificationSystem {
  constructor() {
    this.addNotificationStyles();
  }

  addNotificationStyles() {
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
  }

  show(message, type = 'info', details = null) {
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
          box-shadow: 0 0 8px ${shadowColor};
        "></div>
      </div>
    `;
    
    notificationContainer.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
      notification.querySelector('.notification-content').style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-close timer (5 seconds)
    let autoCloseTimer = setTimeout(() => {
      this.remove(notification);
    }, 5000);
    
    // Handle close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
      clearTimeout(autoCloseTimer); // Cancel auto-close if user clicks X
      this.remove(notification);
    });
    
    // Start progress bar animation
    setTimeout(() => {
      const progressBar = notification.querySelector('.notification-progress');
      if (progressBar) {
        progressBar.style.transform = 'scaleX(0)';
      }
    }, 100);
  }

  remove(notification) {
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
}

// Create global notification instance
window.notificationSystem = new NotificationSystem();

// Global function for backward compatibility
function showNotification(message, type = 'info', details = null) {
  window.notificationSystem.show(message, type, details);
}
