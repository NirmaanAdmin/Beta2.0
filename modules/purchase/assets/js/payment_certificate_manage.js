(function ($) {
    "use strict";

    $(document).on('click', '.reset_all_ot_filters', function () {
        var filterArea = $('.all_ot_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="projects[]"]').selectpicker("val", "");
        get_payment_certificate_dashboard();
    });

    $(document).on('change', 'select[name="vendors[]"], select[name="projects[]"], select[name="group_pur[]"]', function() {
        get_payment_certificate_dashboard();
    });

    get_payment_certificate_dashboard();

})(jQuery);

var lineChartOverTime;

function get_payment_certificate_dashboard() {
  "use strict";

  var data = {
    vendors: $('select[name="vendors[]"]').val(),
    projects: $('select[name="projects[]"]').val(),
    group_pur: $('select[name="group_pur[]"]').val(),
  }

  $.post(admin_url + 'purchase/get_pc_charts', data).done(function(response){
    response = JSON.parse(response);

    // Update value summaries
    $('.total_purchase_orders').text(response.total_purchase_orders);
    $('.total_work_orders').text(response.total_work_orders);
    $('.total_certified_value').text(response.total_certified_value);
    $('.approved_payment_certificates').text(response.approved_payment_certificates);

    // BAR CHART - Top 10 Vendors by Payment Certificate
    var vendorBarCtx = document.getElementById('barChartTopVendors').getContext('2d');
    var vendorLabels = response.bar_top_vendor_name;
    var vendorData = response.bar_top_vendor_value;

    if (window.barTopVendorsChart) {
      barTopVendorsChart.data.labels = vendorLabels;
      barTopVendorsChart.data.datasets[0].data = vendorData;
      barTopVendorsChart.update();
    } else {
      window.barTopVendorsChart = new Chart(vendorBarCtx, {
        type: 'bar',
        data: {
          labels: vendorLabels,
          datasets: [{
            label: 'Certified Value',
            data: vendorData,
            backgroundColor: 'rgba(153, 102, 255, 0.7)',
            borderColor: 'rgba(153, 102, 255, 1)',
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
                text: 'Certified Value'
              }
            },
            y: {
              ticks: {
                autoSkip: false
              },
              title: {
                display: true,
                text: 'Vendors'
              }
            }
          }
        }
      });
    }

    // LINE CHART - Certified Value Over Time
    var lineCtx = document.getElementById('lineChartOverTime').getContext('2d');

    if (lineChartOverTime) {
      lineChartOverTime.data.labels = response.line_order_date;
      lineChartOverTime.data.datasets[0].data = response.line_order_total;
      lineChartOverTime.update();
    } else {
      lineChartOverTime = new Chart(lineCtx, {
        type: 'line',
        data: {
          labels: response.line_order_date,
          datasets: [{
            label: 'Certified Value',
            data: response.line_order_total,
            fill: false,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'bottom'
            },
            tooltip: {
              mode: 'index',
              intersect: false
            }
          },
          scales: {
            x: {
              title: {
                display: true,
                text: 'Month'
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Certified Value'
              }
            }
          }
        }
      });
    }

  });
}
