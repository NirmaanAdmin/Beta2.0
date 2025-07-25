<script>
  (function($) {
    "use strict";

    $(document).ready(function() {
      // Initialize the dashboard
      get_critical_tracker_dashboard();
    });

    var budgetedVsActualCategory;

    function get_critical_tracker_dashboard() {
      "use strict";

      var data = {};

      $.post(admin_url + 'meeting_management/dashboard/get_critical_tracker_dashboard', data).done(function(response) {
        response = JSON.parse(response);

        // DOUGHNUT CHART - Budget Utilization
        var budgetUtilizationCtx = document.getElementById('doughnutChartbudgetUtilization').getContext('2d');
        var budgetUtilizationLabels = ['Open', 'Closed'];
        var budgetUtilizationData = [
          response.open_ratio,
          response.closed_ratio
        ];
        if (window.budgetUtilizationChart) {
          budgetUtilizationChart.data.datasets[0].data = budgetUtilizationData;
          budgetUtilizationChart.update();
        } else {
          window.budgetUtilizationChart = new Chart(budgetUtilizationCtx, {
            type: 'doughnut',
            data: {
              labels: budgetUtilizationLabels,
              datasets: [{
                data: budgetUtilizationData,
                backgroundColor: ['#00008B', '#1E90FF'],
                borderColor: ['#00008B', '#1E90FF'],
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: {
                  position: 'bottom'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      var label = context.label || '';
                      var value = context.formattedValue;
                      return `${label}: ${value}%`;
                    }
                  }
                }
              }
            }
          });
        }


        // BAR CHART - Department-wise Issue Count
        const ctx = document.getElementById('barChartDeptwiseissuecount').getContext('2d');
        const deptwiseissuecountLabels = response.dept_labels || [];
        const deptwiseissuecountData = response.dept_data || [];

        if (window.barDeptwiseissuecountChart) {
          window.barDeptwiseissuecountChart.data.labels = deptwiseissuecountLabels;
          window.barDeptwiseissuecountChart.data.datasets[0].data = deptwiseissuecountData;
          window.barDeptwiseissuecountChart.update();
        } else {
          window.barDeptwiseissuecountChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: deptwiseissuecountLabels,
              datasets: [{
                label: 'Count',
                data: deptwiseissuecountData,
                backgroundColor: '#00008B',
                borderColor: '#00008B',
                borderWidth: 1
              }]
            },
            options: {
              indexAxis: 'y', // makes bars horizontal
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                }
              },
              scales: {
                x: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Number of Issues'
                  },
                  grid: {
                    display: false
                  }
                },
                y: {
                  title: {
                    display: true,
                    text: 'Departments'
                  },
                  ticks: {
                    autoSkip: false
                  },
                  grid: {
                    display: false
                  }
                }
              }
            }
          });
        }


        // BAR CHART - Overdue Tracker (by weeks)
        const overdueCtx = document.getElementById('barChartOverdueTracker').getContext('2d');
        const overdueLabels = Object.keys(response.overdue_tracker || {});
        const overdueData = Object.values(response.overdue_tracker || {});
        const overdueTotal = overdueData.reduce((sum, val) => sum + val, 0);

        if (window.overdueTrackerChart) {
          window.overdueTrackerChart.data.labels = overdueLabels;
          window.overdueTrackerChart.data.datasets[0].data = overdueData;
          window.overdueTrackerChart.options.plugins.title.text = `Total Overdue: ${overdueTotal}`;
          window.overdueTrackerChart.update();
        } else {
          window.overdueTrackerChart = new Chart(overdueCtx, {
            type: 'bar',
            data: {
              labels: overdueLabels,
              datasets: [{
                label: 'Overdue Items',
                data: overdueData,
                backgroundColor: [
                  '#00008B', // DarkBlue for <1 week
                  '#00008B', // Tomato for 1-2 weeks
                  '#00008B', // OrangeRed for 2-4 weeks
                  '#00008B' // Red for >4 weeks
                ],
                borderColor: [
                  '#00008B',
                  '#00008B',
                  '#00008B',
                  '#00008B'
                ],
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      const percentage = overdueTotal > 0 ?
                        Math.round((context.raw / overdueTotal) * 100) :
                        0;
                      return `${context.label}: ${context.raw} (${percentage}%)`;
                    }
                  }
                },
                title: {
                  display: true,
                  text: `Total Overdue: ${overdueTotal}`,
                  position: 'top',
                  font: {
                    size: 14,
                    weight: 'bold'
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Number of Overdue Items',
                    font: {
                      weight: 'bold'
                    }
                  },
                  grid: {
                    display: false
                  }
                },
                x: {
                  title: {
                    display: true,
                    text: 'Overdue Duration',
                    font: {
                      weight: 'bold'
                    }
                  },
                  grid: {
                    display: false
                  }
                }
              }
            }
          });
        }


        action_by_responsibility_tracker();
        upcoming_deadlines();
      });
    }
    var fnServerParams;
    fnServerParams = {

    }

    function action_by_responsibility_tracker() {
      "use strict";
      var table_rec_campaign = $('.table-action-by-responsibility-tracker');
      if ($.fn.DataTable.isDataTable('.table-action-by-responsibility-tracker')) {
        $('.table-action-by-responsibility-tracker').DataTable().destroy();
      }
      initDataTable('.table-action-by-responsibility-tracker', admin_url + 'meeting_management/dashboard/action_by_responsibility_tracker', false, false, fnServerParams, undefined, true);
    }

    function upcoming_deadlines() {
      "use strict";
      var table_upcoming_deadlines = $('.table-upcoming-deadlines');
      if ($.fn.DataTable.isDataTable('.table-upcoming-deadlines')) {
        $('.table-upcoming-deadlines').DataTable().destroy();
      }
      initDataTable('.table-upcoming-deadlines', admin_url + 'meeting_management/dashboard/upcoming_deadlines', false, false, fnServerParams, undefined, true);
    }
  })(jQuery);
</script>