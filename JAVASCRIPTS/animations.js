// Animations Manager - Lottie animations initialization
class AnimationsManager {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.initializeStudentsAnimation();
      this.initializeSMSAnimation();
    });
  }

  initializeStudentsAnimation() {
    try {
      const studentsLottieEl = document.getElementById('studentsLottie');
      if (studentsLottieEl && window.lottie) {
        // Prevent duplicate in case of hot re-renders
        if (!studentsLottieEl.dataset.initialized) {
          window.lottie.loadAnimation({
            container: studentsLottieEl,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '../assets/students.json'
          });
          studentsLottieEl.dataset.initialized = '1';
        }
      }
    } catch (e) {
      console.warn('Students Lottie init failed:', e);
    }
  }

  initializeSMSAnimation() {
    try {
      const smsLottieEl = document.getElementById('smsLottie');
      if (smsLottieEl && window.lottie) {
        // Prevent duplicate in case of hot re-renders
        if (!smsLottieEl.dataset.initialized) {
          window.lottie.loadAnimation({
            container: smsLottieEl,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '../assets/sms.json'
          });
          smsLottieEl.dataset.initialized = '1';
        }
      }
    } catch (e) {
      console.warn('SMS Lottie init failed:', e);
    }
  }
}

// Initialize animations manager
window.animationsManager = new AnimationsManager();
