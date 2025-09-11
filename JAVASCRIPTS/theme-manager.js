// Theme Manager - Dark mode toggle functionality
class ThemeManager {
  constructor() {
    this.init();
  }

  init() {
    const darkModeInput = document.getElementById('darkModeToggle');
    const stored = localStorage.getItem('darkMode');
    const media = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
    const isDark = stored === '1' ? true : stored === '0' ? false : (media ? media.matches : false);
    
    this.setDarkMode(isDark, false);
    
    if (darkModeInput) {
      darkModeInput.onclick = () => {
        const next = !document.body.classList.contains('dark-mode');
        this.setDarkMode(next, true);
      };
    }
  }

  setDarkMode(on, persist = true) {
    document.body.classList.toggle('dark-mode', on);
    if (persist) localStorage.setItem('darkMode', on ? '1' : '0');
  }
}

// Initialize theme manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  window.themeManager = new ThemeManager();
});
