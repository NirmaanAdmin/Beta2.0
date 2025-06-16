<script>
  (function($) {
    "use strict";

    $(document).ready(function() {
      $('select[name="vendors"], select[name="projects"], select[name="group_pur"], select[name="kind"]').on('change', function() {
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
        vendors: $('select[name="vendors"]').val(),
        projects: $('select[name="projects"]').val(),
        group_pur: $('select[name="group_pur"]').val(),
        kind: $('select[name="kind"]').val(),
        from_date: $('input[name="from_date"]').val(),
        to_date: $('input[name="to_date"]').val()
      };

      $.post(admin_url + 'warehouse/dashboard/get_inventory_dashboard', data).done(function(response) {
        response = JSON.parse(response);

        // Update value summaries
        $('.total_items_in_inventory').text(response.total_inventory_count);
        $('.fully_po_material_receipt').text(response.fully_po_material_receipt);
        $('.missing_security_signature').text(response.missing_security_signature);
        $('.missing_production_certificate').text(response.missing_production_certificate);
        $('.missing_transport_document').html(response.missing_transport_document);

        $('.on_time_deliveries_percentage').text(response.on_time_deliveries_percentage + '%');
        $('.delivery_table_data').html(response.delivery_table_data);
        $('.average_delay').text(response.average_delay + ' Days');

        $('.total_procurement_items').text(response.total_procurement_items);
        $('.late_deliveries').text(response.late_deliveries);
        $('.shop_drawing_approved').text(response.shop_drawing_approved);
        $('.shop_drawing_pending_approval').text(response.shop_drawing_pending_approval)
        $('.procurement_table_data_secound').html(response.procurement_table_data_secound);
        // DOUGHNUT CHART - Budget Utilization
        var budgetUtilizationCtx = document.getElementById('doughnutChartDocumentationStatus').getContext('2d');
        var budgetUtilizationLabels = ['Fully Documented', 'Incomplete'];
        var budgetUtilizationData = [
          response.fully_documented,
          response.incompleted
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

        var ctx = document.getElementById('lineChartConsumptionOverTime').getContext('2d');

        var labels = response.getconsumption_months;
        var data = response.getconsumption_data;

        var monthlyDeliveryChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels, // e.g. ['Jan', 'Feb', 'Mar']
            
            datasets: [{
              label: '',
              data: data,
              borderColor: '#1E90FF',
              backgroundColor: 'rgba(30, 144, 255, 0.1)',
              tension: 0.4,
              fill: false,
              pointBackgroundColor: '#1E90FF'
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: false,  //hide legend
              }
            },
            scales: {
              y: {
                beginAtZero: true
              },
              x: {
                grid: {
                  display: false
                }
              }
            }
          }
        });

      });
    }


  })(jQuery);
</script>