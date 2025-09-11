// UI Effects - Page transitions and UI effects
class UIEffects {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.initializePageTransitions();
      this.initializeLogoutEffect();
    });
  }

  initializePageTransitions() {
    // Page transition effects
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a');
      if (!link) return;
      
      const href = link.getAttribute('href') || '';
      // Only intercept internal section navigation
      if (href.includes('?section=')) {
        e.preventDefault();
        document.body.classList.add('page-transition-out');
        setTimeout(() => {
          window.location.href = link.href;
        }, 180);
      }
    });
  }

  initializeLogoutEffect() {
    // Smooth logout interaction
    document.addEventListener('click', (e) => {
      const logout = e.target.closest('#logoutLink');
      if (!logout) return;
      e.preventDefault();
      
      // Add quick pressed feedback on item
      logout.classList.add('pressed');
      
      // Fade out the page for a graceful exit
      document.body.classList.add('page-transition-out');
      setTimeout(() => {
        window.location.href = logout.href;
      }, 200);
    });
  }
}

// Initialize UI effects
window.uiEffects = new UIEffects();
