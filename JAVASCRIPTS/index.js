// SMS Grade Notifier - Main Application Entry Point
// This file coordinates all modular JavaScript components

// Application initialization
class SMSGradeNotifierApp {
  constructor() {
    this.modules = {};
    this.init();
  }

  init() {
    console.log('SMS Grade Notifier App initializing...');
    
    // All modules are loaded via separate script tags
    // and initialize themselves when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
      this.onDOMReady();
    });
  }

  onDOMReady() {
    console.log('SMS Grade Notifier App ready!');
    
    // Store references to global module instances
    this.modules = {
      theme: window.themeManager,
      notifications: window.notificationSystem,
      charts: window.chartsManager,
      animations: window.animationsManager,
      sms: window.smsManager,
      students: window.studentManager,
      grades: window.gradeManager,
      ui: window.uiEffects
    };
  }
}

// Initialize the main application
window.smsGradeNotifierApp = new SMSGradeNotifierApp();

// Backward compatibility function - delegates to notification system
function showNotification(message, type = 'info', details = null) {
  if (window.notificationSystem) {
    window.notificationSystem.show(message, type, details);
  }
}
