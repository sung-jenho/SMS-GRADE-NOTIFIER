// Charts Manager - SMS delivery and grade distribution charts
class ChartsManager {
  constructor() {
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      if (window.Chart) {
        const smsCtx = document.getElementById('smsDeliveryChart');
        if (smsCtx) {
          this.loadSMSDeliveryChart(smsCtx);
        }
      }

      const gradeCtx = document.getElementById('gradeDistributionChart');
      if (gradeCtx) {
        this.loadGradeDistributionChart(gradeCtx);
      }
    });
  }

  // SMS Delivery Chart Functions
  loadSMSDeliveryChart(ctx) {
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
          this.updateSMSLegend(data.stats);
          
          const chartLabels = data.chart.labels;
          const deliveredRates = data.chart.delivered_rates;
          const pendingRates = data.chart.pending_rates;
          const failedRates = data.chart.failed_rates;
          
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
              show: false
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
          this.createDefaultEChartsSMSChart(smsChart);
        }
      })
      .catch(error => {
        console.warn('Failed to load SMS stats:', error);
        this.createDefaultEChartsSMSChart(smsChart);
      });
  }

  updateSMSLegend(stats) {
    const legendContainer = document.querySelector('#smsDeliveryChart').closest('.card-body').querySelector('.d-flex.justify-content-around');
    if (legendContainer) {
      legendContainer.innerHTML = `
        <div><span class="legend-dot legend-green" style="width: 6px; height: 6px;"></span>${stats.delivered.toLocaleString()} Delivered</div>
        <div><span class="legend-dot legend-amber" style="width: 6px; height: 6px;"></span>${stats.pending.toLocaleString()} Pending</div>
        <div><span class="legend-dot legend-red" style="width: 6px; height: 6px;"></span>${stats.failed.toLocaleString()} Failed</div>
      `;
    }
  }

  createDefaultEChartsSMSChart(chart) {
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
    
    const legendContainer = document.querySelector('#smsDeliveryChart').closest('.card-body').querySelector('.d-flex.justify-content-around');
    if (legendContainer) {
      legendContainer.innerHTML = `
        <div><span class="legend-dot legend-green" style="width: 6px; height: 6px;"></span>0 Delivered</div>
        <div><span class="legend-dot legend-amber" style="width: 6px; height: 6px;"></span>0 Pending</div>
        <div><span class="legend-dot legend-red" style="width: 6px; height: 6px;"></span>0 Failed</div>
      `;
    }
  }

  // Grade Distribution Chart Functions
  loadGradeDistributionChart(ctx) {
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
            this.createDefaultEChartsGradeChart(gradeChart);
          }
          
          this.updateGradeLegend(data.subjects);
        } else {
          console.log('Debug info - All subjects:', data.debug_all_subjects);
          console.log('Debug info - All grades:', data.debug_all_grades);
          console.log('API returned no distribution data');
          this.createDefaultEChartsGradeChart(gradeChart);
        }
      })
      .catch(error => {
        console.warn('Failed to load grade distribution:', error);
        this.createDefaultEChartsGradeChart(gradeChart);
      });
  }

  updateGradeLegend(subjects) {
    const legendContainer = document.querySelector('#grade-legend');
    if (legendContainer && subjects.length > 0) {
      let legendHTML = '';
      subjects.forEach(subject => {
        legendHTML += `<span><span class="legend-dot" style="background:${subject.color}; width: 6px; height: 6px;"></span>${subject.label}</span>`;
      });
      legendContainer.innerHTML = legendHTML;
    }
  }

  createDefaultEChartsGradeChart(chart) {
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
}

// Initialize charts manager
window.chartsManager = new ChartsManager();
