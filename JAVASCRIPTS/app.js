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
    // Handle modern button toggle
    darkModeInput.onclick = function () {
      const next = !document.body.classList.contains('dark-mode');
      setDarkMode(next, true);
    };
  }

  if (window.Chart) {
    const smsCtx = document.getElementById('smsDeliveryChart');
    if (smsCtx) {
      // Load real SMS delivery data
      loadSMSDeliveryChart(smsCtx);
    }
  }

  // Initialize Lottie animations on metric cards (right side)
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

  // Function to load SMS delivery chart with ECharts area chart
  function loadSMSDeliveryChart(ctx) {
    // Initialize ECharts instance
    const smsChart = echarts.init(ctx);
    
    fetch('/VESTIL/PHP/sms_stats_api.php?v=' + Date.now())
      .then(response => {
        console.log('SMS API status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Update the legend with real stats
          updateSMSLegend(data.stats);
          
          // Use real API data for chart visualization
          const chartLabels = data.chart.labels;
          const deliveredRates = data.chart.delivered_rates;
          const pendingRates = data.chart.pending_rates;
          const failedRates = data.chart.failed_rates;
          
          // Create modern ECharts multi-line chart with real data
          const option = {
            tooltip: {
              trigger: 'axis',
              backgroundColor: 'rgba(255, 255, 255, 0.95)',
              borderColor: '#e5e7eb',
              borderWidth: 1,
              textStyle: {
                color: '#374151'
              },
              formatter: function(params) {
                let tooltip = `<strong>${params[0].name}</strong><br/>`;
                params.forEach(param => {
                  const color = param.color;
                  tooltip += `<span style="color: ${color}">${param.seriesName}: ${param.value}%</span><br/>`;
                });
                return tooltip;
              }
            },
            legend: {
              show: false // Hide legend since we have our own below
            },
            grid: {
              left: '3%',
              right: '4%',
              bottom: '3%',
              top: '10%',
              containLabel: true
            },
            xAxis: {
              type: 'category',
              data: chartLabels,
              axisTick: {
                show: false
              },
              axisLine: {
                show: false
              },
              axisLabel: {
                color: '#9ca3af',
                fontSize: 12
              }
            },
            yAxis: {
              type: 'value',
              min: 0,
              max: 100,
              axisLabel: {
                formatter: '{value}%',
                color: '#9ca3af',
                fontSize: 12
              },
              splitLine: {
                lineStyle: {
                  color: 'rgba(0,0,0,0.05)'
                }
              },
              axisLine: {
                show: false
              },
              axisTick: {
                show: false
              }
            },
            series: [{
              name: 'Delivered',
              type: 'line',
              data: deliveredRates,
              smooth: true,
              symbol: 'circle',
              symbolSize: 6,
              itemStyle: {
                color: '#22c55e',
                borderColor: '#fff',
                borderWidth: 2
              },
              lineStyle: {
                width: 3,
                color: '#22c55e'
              },
              areaStyle: {
                color: {
                  type: 'linear',
                  x: 0,
                  y: 0,
                  x2: 0,
                  y2: 1,
                  colorStops: [{
                    offset: 0, color: 'rgba(34, 197, 94, 0.2)'
                  }, {
                    offset: 1, color: 'rgba(34, 197, 94, 0.02)'
                  }]
                }
              },
              animationDuration: 2000,
              animationEasing: 'cubicOut'
            }, {
              name: 'Pending',
              type: 'line',
              data: pendingRates,
              smooth: true,
              symbol: 'circle',
              symbolSize: 6,
              itemStyle: {
                color: '#f59e0b',
                borderColor: '#fff',
                borderWidth: 2
              },
              lineStyle: {
                width: 3,
                color: '#f59e0b'
              },
              areaStyle: {
                color: {
                  type: 'linear',
                  x: 0,
                  y: 0,
                  x2: 0,
                  y2: 1,
                  colorStops: [{
                    offset: 0, color: 'rgba(245, 158, 11, 0.2)'
                  }, {
                    offset: 1, color: 'rgba(245, 158, 11, 0.02)'
                  }]
                }
              },
              animationDuration: 2000,
              animationEasing: 'cubicOut'
            }, {
              name: 'Failed',
              type: 'line',
              data: failedRates,
              smooth: true,
              symbol: 'circle',
              symbolSize: 6,
              itemStyle: {
                color: '#ef4444',
                borderColor: '#fff',
                borderWidth: 2
              },
              lineStyle: {
                width: 3,
                color: '#ef4444'
              },
              areaStyle: {
                color: {
                  type: 'linear',
                  x: 0,
                  y: 0,
                  x2: 0,
                  y2: 1,
                  colorStops: [{
                    offset: 0, color: 'rgba(239, 68, 68, 0.2)'
                  }, {
                    offset: 1, color: 'rgba(239, 68, 68, 0.02)'
                  }]
                }
              },
              animationDuration: 2000,
              animationEasing: 'cubicOut'
            }]
          };
          
          smsChart.setOption(option);
        } else {
          // Fallback to default chart if API fails
          createDefaultEChartsSMSChart(smsChart);
        }
      })
      .catch(error => {
        console.warn('Failed to load SMS stats:', error);
        createDefaultEChartsSMSChart(smsChart);
      });
  }

  // Function to update SMS legend with real data
  function updateSMSLegend(stats) {
    const legendContainer = document.querySelector('#smsDeliveryChart').closest('.card-body').querySelector('.d-flex.justify-content-around');
    if (legendContainer) {
      legendContainer.innerHTML = `
        <div><span class="legend-dot legend-green" style="width: 6px; height: 6px;"></span>${stats.delivered.toLocaleString()} Delivered</div>
        <div><span class="legend-dot legend-amber" style="width: 6px; height: 6px;"></span>${stats.pending.toLocaleString()} Pending</div>
        <div><span class="legend-dot legend-red" style="width: 6px; height: 6px;"></span>${stats.failed.toLocaleString()} Failed</div>
      `;
    }
  }

  // Fallback function for default ECharts SMS chart
  function createDefaultEChartsSMSChart(chart) {
    const option = {
      tooltip: {
        trigger: 'axis',
        formatter: 'No SMS data available'
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        top: '10%',
        containLabel: true
      },
      xAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        axisTick: { show: false },
        axisLine: { show: false },
        axisLabel: { color: '#9ca3af', fontSize: 12 }
      },
      yAxis: {
        type: 'value',
        min: 0,
        max: 100,
        axisLabel: {
          formatter: '{value}%',
          color: '#9ca3af',
          fontSize: 12
        },
        splitLine: {
          lineStyle: { color: 'rgba(0,0,0,0.05)' }
        },
        axisLine: { show: false },
        axisTick: { show: false }
      },
      series: [{
        name: 'Success Rate',
        type: 'line',
        data: [0, 0, 0, 0, 0, 0, 0],
        smooth: true,
        symbol: 'none',
        lineStyle: {
          width: 3,
          color: '#e5e7eb'
        },
        areaStyle: {
          color: 'rgba(229, 231, 235, 0.1)'
        }
      }],
      graphic: {
        type: 'text',
        left: 'center',
        top: 'middle',
        style: {
          text: 'No SMS Data',
          fontSize: 14,
          fontWeight: 500,
          fill: '#9ca3af'
        }
      }
    };
    
    chart.setOption(option);
    
    // Show "No data" message in legend
    const legendContainer = document.querySelector('#smsDeliveryChart').closest('.card-body').querySelector('.d-flex.justify-content-around');
    if (legendContainer) {
      legendContainer.innerHTML = `
        <div><span class="legend-dot legend-green" style="width: 6px; height: 6px;"></span>0 Delivered</div>
        <div><span class="legend-dot legend-amber" style="width: 6px; height: 6px;"></span>0 Pending</div>
        <div><span class="legend-dot legend-red" style="width: 6px; height: 6px;"></span>0 Failed</div>
      `;
    }
  }

  const gradeCtx = document.getElementById('gradeDistributionChart');
  if (gradeCtx) {
    // Load real grade distribution data with ECharts
    loadGradeDistributionChart(gradeCtx);
  }

  // Function to load grade distribution chart with ECharts
  function loadGradeDistributionChart(ctx) {
    // Initialize ECharts instance
    const gradeChart = echarts.init(ctx);
    
    fetch('/VESTIL/PHP/grade_distribution_api.php?v=' + Date.now())
      .then(response => {
        console.log('Grade API status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Grade API response:', data);
        if (data.success && data.subjects && data.subjects.length > 0) {
          const hasAnyGrades = data.subjects.some(subject => subject.count > 0);
          
          if (hasAnyGrades) {
            // Create modern ECharts pie chart with animations
            const option = {
              tooltip: {
                trigger: 'item',
                formatter: function(params) {
                  const subject = data.subjects[params.dataIndex];
                  return `<strong>${subject.full_name}</strong><br/>
                          ${subject.count} grades<br/>
                          Average: ${subject.avg_grade}<br/>
                          ${params.percent}% of total`;
                }
              },
              series: [{
                name: 'Grade Distribution',
                type: 'pie',
                radius: ['45%', '75%'],
                center: ['50%', '50%'],
                avoidLabelOverlap: false,
                itemStyle: {
                  borderRadius: 8,
                  borderColor: '#fff',
                  borderWidth: 2
                },
                label: {
                  show: false
                },
                emphasis: {
                  label: {
                    show: false
                  },
                  itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                  }
                },
                labelLine: {
                  show: false
                },
                data: data.subjects.map(subject => ({
                  value: subject.count,
                  name: subject.label,
                  itemStyle: {
                    color: subject.color
                  }
                })),
                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                  return Math.random() * 200;
                }
              }]
            };
            
            gradeChart.setOption(option);
          } else {
            createDefaultEChartsGradeChart(gradeChart);
          }
          
          updateGradeLegend(data.subjects);
        } else {
          console.log('Debug info - All subjects:', data.debug_all_subjects);
          console.log('Debug info - All grades:', data.debug_all_grades);
          console.log('API returned no distribution data');
          createDefaultEChartsGradeChart(gradeChart);
        }
      })
      .catch(error => {
        console.warn('Failed to load grade distribution:', error);
        createDefaultEChartsGradeChart(gradeChart);
      });
  }

  // Function to update grade legend with real subjects
  function updateGradeLegend(subjects) {
    const legendContainer = document.querySelector('#grade-legend');
    if (legendContainer && subjects.length > 0) {
      let legendHTML = '';
      subjects.forEach(subject => {
        legendHTML += `<span><span class="legend-dot" style="background:${subject.color}; width: 6px; height: 6px;"></span>${subject.label}</span>`;
      });
      legendContainer.innerHTML = legendHTML;
    }
  }

  // Fallback function for default ECharts grade chart
  function createDefaultEChartsGradeChart(chart) {
    const option = {
      tooltip: {
        trigger: 'item',
        formatter: 'No grade data available'
      },
      series: [{
        name: 'Grade Distribution',
        type: 'pie',
        radius: ['45%', '75%'],
        center: ['50%', '50%'],
        data: [{
          value: 1,
          name: 'No Data',
          itemStyle: {
            color: '#e5e7eb'
          }
        }],
        label: {
          show: false
        },
        emphasis: {
          label: {
            show: false
          }
        }
      }],
      graphic: {
        type: 'text',
        left: 'center',
        top: 'middle',
        style: {
          text: 'No Grades Yet',
          fontSize: 14,
          fontWeight: 500,
          fill: '#9ca3af'
        }
      }
    };
    
    chart.setOption(option);
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
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', function() {
      if (currentGradeId && currentButton) {
        removeGrade(currentGradeId, currentButton);
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        modal.hide();
      }
    });
  }

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

// --- SMS Send Functionality ---
document.addEventListener('click', function(e) {
  // Open SMS modal
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

const confirmSendSmsBtn = document.getElementById('confirmSendSmsBtn');
if (confirmSendSmsBtn) {
  confirmSendSmsBtn.addEventListener('click', function() {
  const logId = document.getElementById('smsLogId').value;
  const phone = document.getElementById('smsPhoneNumber').value;
  const message = document.getElementById('smsMessage').value;
  const btn = this;
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
});
}

// Smooth logout interaction
document.addEventListener('click', function(e) {
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

// Page transition effects
document.addEventListener('click', function(e) {
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
