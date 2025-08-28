// Dark mode toggle and Chart.js initialization

function setDarkMode(on, persist = true) {
  document.body.classList.toggle('dark-mode', on);
  if (persist) localStorage.setItem('darkMode', on ? '1' : '0');
}

function updateIconClass(on) {
  const icon = document.getElementById('darkModeIcon');
  if (icon) icon.className = on ? 'bi bi-moon' : 'bi bi-sun';
}

document.addEventListener('DOMContentLoaded', function () {
  const darkModeBtn = document.getElementById('darkModeToggle');
  const stored = localStorage.getItem('darkMode');
  const media = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
  const isDark = stored === '1' ? true : stored === '0' ? false : (media ? media.matches : false);
  setDarkMode(isDark, false);
  updateIconClass(isDark);
  if (darkModeBtn) {
    darkModeBtn.onclick = function () {
      const next = !document.body.classList.contains('dark-mode');
      setDarkMode(next, true);
      updateIconClass(next);
    };
  }

  if (window.Chart) {
    const smsCtx = document.getElementById('smsDeliveryChart');
    if (smsCtx) {
      new Chart(smsCtx, {
        type: 'line',
        data: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          datasets: [{
            label: 'Success Rate %',
            data: [93, 95, 94, 96, 92, 95, 94],
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
            y: { min: 80, max: 100, ticks: { callback: v => v + '%' }, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
          }
        }
      });
    }

    const gradeCtx = document.getElementById('gradeDistributionChart');
    if (gradeCtx) {
      new Chart(gradeCtx, {
        type: 'doughnut',
        data: {
          labels: ['A', 'B', 'C', 'D', 'F'],
          datasets: [{
            data: [234, 456, 321, 89, 23],
            backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444'],
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
  }
});


