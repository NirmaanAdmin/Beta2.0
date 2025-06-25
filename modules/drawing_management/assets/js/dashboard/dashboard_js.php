<script>
  (function($) {
    "use strict";

    $(document).ready(function() {
      $('select[name="projects"]').on('change', function() {
        get_inventory_dashboard();
      });

      $('input[name="from_date"], input[name="to_date"]').on('change', function() {
        get_inventory_dashboard();
      });

      $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').selectpicker("val", "");
        get_inventory_dashboard();
      });

      get_inventory_dashboard();
    });

    var budgetedVsActualCategory;

    function get_inventory_dashboard() {
      "use strict";

      var data = {
        projects: $('select[name="projects"]').val(),
      };

      $.post(admin_url + 'drawing_management/dashboard/get_drawing_management_dashboard', data).done(function(response) {
        response = JSON.parse(response);
        // === Stacked Chart ===
        if (window.stackedLaborChartInstance) {
          window.stackedLaborChartInstance.destroy();
        }
        const stackedLaborCtx = document.getElementById('stackedChart').getContext('2d');
        const stackedDatasets = Object.keys(response.stacked_labor_values).map(function(label, i, arr) {
          var hue = (i * 360 / arr.length) % 360;
          var bg = 'hsl(' + hue + ', 70%, 60%)';
          return {
            label: label,
            data: response.stacked_labor_values[label],
            backgroundColor: bg,
            borderWidth: 1
          };
        });
        window.stackedLaborChartInstance = new Chart(stackedLaborCtx, {
          type: 'bar',
          data: {
            labels: response.stacked_labor_labels,
            datasets: stackedDatasets
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Stacked Status by Discipline',
              },
              tooltip: {
                mode: 'index',
                intersect: false
              }
            },
            scales: {
              x: {
                stacked: true
              },
              y: {
                stacked: true,
                beginAtZero: true
              }
            }
          }
        });

        // DOUGHNUT CHART - Approval Status
        // DOUGHNUT CHART - Approval Status Percentage
        var approvalStatusCtx = document.getElementById('doughnutChartapproval').getContext('2d');
        var approvalStatusLabels = ['Approved', 'Draft', 'Rejected'];
        var approvalStatusData = [
          response.approved_percent,
          response.draft_percent,
          response.rejected_percent
        ];

        if (window.approvalStatusChart) {
          approvalStatusChart.data.datasets[0].data = approvalStatusData;
          approvalStatusChart.update();
        } else {
          window.approvalStatusChart = new Chart(approvalStatusCtx, {
            type: 'doughnut',
            data: {
              labels: approvalStatusLabels,
              datasets: [{
                data: approvalStatusData,
                backgroundColor: ['#5cb85c', '#2563eb', '#F44336'], // Green, Yellow, Red
                borderColor: ['#388E3C', '#4373da', '#D32F2F'],
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: {
                  position: 'bottom'
                },
                title: {
                  display: true,
                  text: 'Approval Status (%)'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      var label = context.label || '';
                      var value = context.raw || 0;
                      return `${label}: ${value}%`;
                    }
                  }
                }
              }
            }
          });
        }
      });
      dicipline_status_charts();
    }

    var fnServerParams, fnServerParams2;

    function dicipline_status_charts() {
      "use strict";

      // Destroy existing instance if any
      if ($.fn.DataTable.isDataTable('.table-dicipline-status')) {
        $('.table-dicipline-status').DataTable().destroy();
      }

      // Define filter parameters if needed (example: by project, etc.)
      var fnServerParams = {
        // 'project_id': '[name="project_id"]',
        // Add filters here if needed
      };

      var table = initDataTable('.table-dicipline-status',
        admin_url + 'drawing_management/dashboard/dicipline_status_charts',
        false,
        false,
        fnServerParams
      );

      // Optional: Bind onchange reload logic if filters are added
      $.each(fnServerParams, function(key, selector) {
        $('select' + selector).on('change', function() {
          table.DataTable().ajax.reload();
        });
      });
    }



  })(jQuery);
</script>