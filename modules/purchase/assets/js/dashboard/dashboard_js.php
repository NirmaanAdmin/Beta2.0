<script>
(function($) {
  "use strict";
})(jQuery);

var budgetedVsActualCategory;

get_purchase_order_dashboard();

function get_purchase_order_dashboard() {
  "use strict";

  var data = {
    vendors: $('select[name="vendors"]').val(),
    group_pur: $('select[name="group_pur"]').val(),
    kind: $('select[name="kind"]').val(),
    from_date: $('input[name="from_date"]').val(),
    to_date: $('input[name="to_date"]').val()
  };

  $.post(admin_url + 'purchase/dashboard/get_purchase_order_dashboard', data).done(function(response){
    response = JSON.parse(response);
    // Update value summaries
    $('.cost_to_complete').text(response.cost_to_complete);
    $('.rev_contract_value').text(response.rev_contract_value);
    $('.percentage_utilized').text(response.percentage_utilized);
    $('.procurement_table_data').html(response.procurement_table_data);

    // DOUGHNUT CHART - Budget Utilization
    var budgetUtilizationCtx = document.getElementById('doughnutChartbudgetUtilization').getContext('2d');
    var budgetUtilizationLabels = ['Budgeted', 'Actual'];
    var budgetUtilizationData = [
      response.total_cost_to_complete, 
      response.total_rev_contract_value
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
            backgroundColor: [
              '#00008B',
              '#1E90FF',
            ],
            borderColor: [
              '#00008B',
              '#1E90FF'
            ],
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
                  return context.label + ': ' + context.formattedValue;
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
      datasets: [
        {
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

  });
}
</script>