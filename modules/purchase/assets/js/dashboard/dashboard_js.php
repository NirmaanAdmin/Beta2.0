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

        // Procurement Summary
        $('.cost_to_complete').text(response.cost_to_complete);
        $('.rev_contract_value').text(response.rev_contract_value);
        $('.percentage_utilized').text(response.percentage_utilized + '%');
        $('.budgeted_procurement_net_value').text(response.budgeted_procurement_net_value);
        $('.po_amount_incl_co').text(response.po_amount_incl_co);
        $('.wo_amount_incl_co').text(response.wo_amount_incl_co);
        $('.unawarded_capex').text(response.unawarded_capex);

        // Procurement Data
        $('.procurement_table_data').html(response.procurement_table_data);

        // Delivery Schedules
        $('.on_time_deliveries_percentage').text(response.on_time_deliveries_percentage + '%');
        $('.average_delay').text(response.average_delay + ' Days');
        $('.total_procurement_items').text(response.total_procurement_items);
        $('.late_deliveries').text(response.late_deliveries);
        $('.shop_drawing_approved').text(response.shop_drawing_approved);
        $('.shop_drawing_pending_approval').text(response.shop_drawing_pending_approval);

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
        const ctx = document.getElementById('barChartProcurementStatus').getContext('2d');
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

       po_issued_in_last_month();
       wo_issued_in_last_month();
       po_critical_deliver();
       po_upcoming_deliver();
      });
    }

    function po_issued_in_last_month() {
      "use strict";
      var poIssuedParams = {};
      if ($('select[name="vendors"]').val() != '') {
        poIssuedParams["vendor[]"] = '[name="vendors"]';
      }
      if ($('select[name="group_pur"]').val() != '') {
        poIssuedParams["group_pur[]"] = '[name="group_pur"]';
      }
      if ($('select[name="projects"]').val() != '') {
        poIssuedParams["project[]"] = '[name="projects"]';
      }
      var table_po_issued_in_last_month = $('.table-table_po_issued_in_last_month');
      if ($.fn.DataTable.isDataTable('.table-table_po_issued_in_last_month')) {
        $('.table-table_po_issued_in_last_month').DataTable().destroy();
      }
      initDataTable('.table-table_po_issued_in_last_month', admin_url + 'purchase/table_po_issued_in_last_month', [], [], poIssuedParams, [4, 'desc']);
    }

    function wo_issued_in_last_month() {
      "use strict";
      var woIssuedParams = {};
      if ($('select[name="vendors"]').val() != '') {
        woIssuedParams["vendor[]"] = '[name="vendors"]';
      }
      if ($('select[name="group_pur"]').val() != '') {
        woIssuedParams["group_pur[]"] = '[name="group_pur"]';
      }
      if ($('select[name="projects"]').val() != '') {
        woIssuedParams["project[]"] = '[name="projects"]';
      }
      var table_wo_issued_in_last_month = $('.table-table_wo_issued_in_last_month');
      if ($.fn.DataTable.isDataTable('.table-table_wo_issued_in_last_month')) {
        $('.table-table_wo_issued_in_last_month').DataTable().destroy();
      }
      initDataTable('.table-table_wo_issued_in_last_month', admin_url + 'purchase/table_wo_issued_in_last_month', [], [], woIssuedParams, [4, 'desc']);
    }

    function po_critical_deliver() {
      "use strict";
      var poCriticalParams = {};
      if ($('select[name="vendors"]').val() != '') {
        poCriticalParams["vendor"] = '[name="vendors"]';
      }
      if ($('select[name="group_pur"]').val() != '') {
        poCriticalParams["group_pur"] = '[name="group_pur"]';
      }
      if ($('select[name="projects"]').val() != '') {
        poCriticalParams["project"] = '[name="projects"]';
      }
      if ($('select[name="kind"]').val() != '') {
        poCriticalParams["kind"] = '[name="kind"]';
      }
      if ($('input[name="from_date"]').val() != '') {
        poCriticalParams["from_date"] = '[name="from_date"]';
      }
      if ($('input[name="to_date"]').val() != '') {
        poCriticalParams["to_date"] = '[name="to_date"]';
      }
      var table_po_critical_deliver = $('.table-table_po_critical_deliver');
      if ($.fn.DataTable.isDataTable('.table-table_po_critical_deliver')) {
        $('.table-table_po_critical_deliver').DataTable().destroy();
      }
      initDataTable('.table-table_po_critical_deliver', admin_url + 'purchase/dashboard/table_po_critical_deliver', [], [], poCriticalParams, [3, 'desc']);
    }

    function po_upcoming_deliver() {
      "use strict";
      var poUpcomingParams = {};
      if ($('select[name="vendors"]').val() != '') {
        poUpcomingParams["vendor"] = '[name="vendors"]';
      }
      if ($('select[name="group_pur"]').val() != '') {
        poUpcomingParams["group_pur"] = '[name="group_pur"]';
      }
      if ($('select[name="projects"]').val() != '') {
        poUpcomingParams["project"] = '[name="projects"]';
      }
      if ($('select[name="kind"]').val() != '') {
        poUpcomingParams["kind"] = '[name="kind"]';
      }
      if ($('input[name="from_date"]').val() != '') {
        poUpcomingParams["from_date"] = '[name="from_date"]';
      }
      if ($('input[name="to_date"]').val() != '') {
        poUpcomingParams["to_date"] = '[name="to_date"]';
      }
      var table_po_upcoming_deliver = $('.table-table_po_upcoming_deliver');
      if ($.fn.DataTable.isDataTable('.table-table_po_upcoming_deliver')) {
        $('.table-table_po_upcoming_deliver').DataTable().destroy();
      }
      initDataTable('.table-table_po_upcoming_deliver', admin_url + 'purchase/dashboard/table_po_upcoming_deliver', [], [], poUpcomingParams, [3, 'desc']);
    }
  })(jQuery);
</script>