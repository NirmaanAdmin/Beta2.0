<script>
  (function($) {
    "use strict";

    $(document).ready(function() {
      $('select[name="vendors"], select[name="projects"], select[name="group_pur"], select[name="kind"]').on('change', function() {
        get_purchase_order_dashboard();
      });

      $('input[name="from_date"], input[name="to_date"]').on('change', function() {
        get_purchase_order_dashboard();
      });

      $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="projects"]').selectpicker("val", "");
        get_purchase_order_dashboard();
      });

      get_purchase_order_dashboard();
    });

    var budgetedVsActualCategory;

    function get_purchase_order_dashboard() {
      "use strict";

      var data = {
        vendors: $('select[name="vendors"]').val(),
        projects: $('select[name="projects"]').val(),
        group_pur: $('select[name="group_pur"]').val(),
        kind: $('select[name="kind"]').val(),
        from_date: $('input[name="from_date"]').val(),
        to_date: $('input[name="to_date"]').val()
      };

      $.post(admin_url + 'purchase/dashboard/get_purchase_order_dashboard', data).done(function(response) {
        response = JSON.parse(response);

        // Update value summaries
        $('.cost_to_complete').text(response.cost_to_complete);
        $('.rev_contract_value').text(response.rev_contract_value);
        $('.percentage_utilized').text(response.percentage_utilized + '%');
        $('.budgeted_procurement_net_value').text(response.budgeted_procurement_net_value);
        $('.procurement_table_data').html(response.procurement_table_data);
        $('.on_time_deliveries_percentage').text(response.on_time_deliveries_percentage + '%');
        $('.delivery_table_data').html(response.delivery_table_data);
        $('.average_delay').text(response.average_delay + ' Days');

        $('.total_procurement_items').text(response.total_procurement_items);
        $('.late_deliveries').text(response.late_deliveries);
        $('.shop_drawing_approved').text(response.shop_drawing_approved);
        $('.shop_drawing_pending_approval').text(response.shop_drawing_pending_approval)
        $('.procurement_table_data_secound').html(response.procurement_table_data_secound);
        // DOUGHNUT CHART - Budget Utilization
        var budgetUtilizationCtx = document.getElementById('doughnutChartbudgetUtilization').getContext('2d');
        var budgetUtilizationLabels = ['Budgeted', 'Actual'];
        var budgetUtilizationData = [
          response.cost_to_complete_ratio,
          response.rev_contract_value_ratio
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

        // COLUMN CHART - Budgeted vs Actual Procurement by Category
        var barCtx = document.getElementById('budgetedVsActualCategory').getContext('2d');
        var barData = {
          labels: response.budgeted_actual_category_labels,
          datasets: [{
              label: 'Budgeted',
              data: response.budgeted_category_value,
              backgroundColor: '#00008B',
              borderColor: '#00008B',
              borderWidth: 1
            },
            {
              label: 'Actual',
              data: response.actual_category_value,
              backgroundColor: '#1E90FF',
              borderColor: '#1E90FF',
              borderWidth: 1
            }
          ]
        };

        if (budgetedVsActualCategory) {
          budgetedVsActualCategory.data.labels = barData.labels;
          budgetedVsActualCategory.data.datasets[0].data = barData.datasets[0].data;
          budgetedVsActualCategory.data.datasets[1].data = barData.datasets[1].data;
          budgetedVsActualCategory.update();
        } else {
          budgetedVsActualCategory = new Chart(barCtx, {
            type: 'bar',
            data: barData,
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom'
                }
              },
              scales: {
                x: {
                  title: {
                    display: false,
                    text: 'Order Date'
                  }
                },
                y: {
                  beginAtZero: true,
                  title: {
                    display: false,
                    text: 'Amount'
                  }
                }
              }
            }
          });
        }

        // BAR CHART
        var deliveryDelayBarCtx = document.getElementById('barChartDeliveryDelay').getContext('2d');
        var deliveryDelayLabels = response.delivery_delay_po;
        var deliveryDelayData = response.delivery_delay_days;

        if (window.barDeliveryDelayChart) {
          barDeliveryDelayChart.data.labels = deliveryDelayLabels;
          barDeliveryDelayChart.data.datasets[0].data = deliveryDelayData;
          barDeliveryDelayChart.update();
        } else {
          window.barDeliveryDelayChart = new Chart(deliveryDelayBarCtx, {
            type: 'bar',
            data: {
              labels: deliveryDelayLabels,
              datasets: [{
                label: 'PO',
                data: deliveryDelayData,
                backgroundColor: '#00008B',
                borderColor: '#00008B',
                borderWidth: 1
              }]
            },
            options: {
              indexAxis: 'y',
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
                    text: 'Delivery Delays'
                  }
                },
                y: {
                  ticks: {
                    autoSkip: false
                  },
                  title: {
                    display: true,
                    text: 'PO'
                  }
                }
              }
            }
          });
        }

        // PIE CHART
        var deliveryPerformancePieCtx = document.getElementById('pieChartDeliveryPerformance').getContext('2d');
        var deliveryPerformancePieLabels = response.delivery_performance_labels;
        var deliveryPerformancePieData = response.delivery_performance_values;

        if (window.deliveryPerformancePieChart) {
          deliveryPerformancePieChart.data.labels = deliveryPerformancePieLabels;
          deliveryPerformancePieChart.data.datasets[0].data = deliveryPerformancePieData;
          deliveryPerformancePieChart.update();
        } else {
          window.deliveryPerformancePieChart = new Chart(deliveryPerformancePieCtx, {
            type: 'pie',
            data: {
              labels: deliveryPerformancePieLabels,
              datasets: [{
                data: deliveryPerformancePieData,
                backgroundColor: ['#00008B', '#1E90FF'],
                borderColor: '#fff',
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

        // BAR CHART
        // after you’ve fetched “response” via $.post or fetch…
        const ctx = document.getElementById('barChartProcurementStatus').getContext('2d');

        // 1) Define labels and data directly
        const procurementStatusLabels = [
          'Shop Drawings Submited',
          'Production Status Approved',
          'RFQ Sent',
          'POI Sent',
          'PIR Sent'
        ];
        const procurementStatusData = [
          response.shop_drawing_pending_approval,
          response.production_status_approved,
          response.rfq_sent,
          response.poi_sent,
          response.pir_sent
        ];

        if (window.barprocurementStatusChart) {
          window.barprocurementStatusChart.data.labels = procurementStatusLabels;
          window.barprocurementStatusChart.data.datasets[0].data = procurementStatusData;
          window.barprocurementStatusChart.update();
        } else {
          window.barprocurementStatusChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: procurementStatusLabels,
              datasets: [{
                label: 'Count',
                data: procurementStatusData,
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
                    text: ''
                  }
                },
                y: {
                  title: {
                    display: true,
                    text: ''
                  },
                  ticks: {
                    autoSkip: false
                  }
                }
              }
            }
          });
        }

       item_tracker_report_for_charts(); 
      });
    }
    var fnServerParams;
    fnServerParams = {
      "vendors": '[name="vendors"]',
    };
    function item_tracker_report_for_charts() {
      "use strict";
      var table_rec_campaign = $('.table-item-tracker-report');
      if ($.fn.DataTable.isDataTable('.table-item-tracker-report')) {
        $('.table-item-tracker-report').DataTable().destroy();
      }
      initDataTable('.table-item-tracker-report', admin_url + 'purchase/item_tracker_report_for_charts', false, false, fnServerParams, undefined, true);
      $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
          table_rec_campaign.DataTable().ajax.reload()
            .columns.adjust()
            .responsive.recalc();
        });
      });
    }
  })(jQuery);
</script>