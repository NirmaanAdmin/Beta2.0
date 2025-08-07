<script>
  var report_from = $('input[name="report-from"]');
  var report_to = $('input[name="report-to"]');
  var date_range = $('#date-range');
  (function($) {
    "use strict";

    $(document).ready(function() {
      $('select[name="vendors"], select[name="projects"], select[name="order_tagged_detail[]"]').on('change', function() {
        get_billing_dashboard();
      });

      $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="projects"]').selectpicker("val", "");
        get_billing_dashboard();
      });

      $('select[name="year_requisition"]').on('change', function() {
        get_billing_dashboard();
      });

      report_from.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val != '') {
          report_to.attr('disabled', false);
          if (report_to_val != '') {
            get_billing_dashboard();
          }
        } else {
          report_to.attr('disabled', true);
        }
      });

      report_to.on('change', function() {
        var val = $(this).val();
        if (val != '') {
          get_billing_dashboard();
        }
      });

      $('select[name="months-report"]').on('change', function() {
        var val = $(this).val();
        report_to.attr('disabled', true);
        report_to.val('');
        report_from.val('');
        if (val == 'custom') {
          date_range.addClass('fadeIn').removeClass('hide');
          return;
        } else {
          if (!date_range.hasClass('hide')) {
            date_range.removeClass('fadeIn').addClass('hide');
          }
        }
        get_billing_dashboard();
      });

      get_billing_dashboard();
    });

    var lineChartBilOverTime;
    var lineChartRilOverTime;
    var lineChartPaidOverTime;
    var lineChartUnpaidOverTime;

    function get_billing_dashboard() {
      "use strict";

      var data = {
        vendors: $('select[name="vendors"]').val(),
        projects: $('select[name="projects"]').val(),
        order_tagged_detail: $('select[name="order_tagged_detail[]"]').val(),
        report_months: $('select[name="months-report"]').val(),
        report_from: $('input[name="report-from"]').val(),
        report_to: $('input[name="report-to"]').val(),
        year_requisition: $('select[name="year_requisition"]').val()
      };

      $.post(admin_url + 'purchase/dashboard/get_billing_dashboard', data).done(function(response) {
        response = JSON.parse(response);

        // Update value summaries
        $('.total_bil_count').text(response.total_bil_count);
        $('.total_bil_amount').text(response.total_bil_amount);
        $('.total_ril_count').text(response.total_ril_count);
        $('.total_ril_amount').text(response.total_ril_amount);
        $('.total_paid_count').text(response.total_paid_count);
        $('.total_paid_amount').text(response.total_paid_amount);
        $('.total_unpaid_count').text(response.total_unpaid_count);
        $('.total_unpaid_amount').text(response.total_unpaid_amount);
        $('.bill_pending_by_bil').text(response.bill_pending_by_bil);
        $('.bill_pending_by_ril').text(response.bill_pending_by_ril);

        // LINE CHART - Total Certified Amount Over Period of Time
        var lineBilCtx = document.getElementById('lineChartBilOverTime').getContext('2d');
        if (lineChartBilOverTime) {
          lineChartBilOverTime.data.labels = response.line_bil_order_date;
          lineChartBilOverTime.data.datasets[0].data = response.line_bil_order_total;
          lineChartBilOverTime.update();
        } else {
          lineChartBilOverTime = new Chart(lineBilCtx, {
            type: 'line',
            data: {
              labels: response.line_bil_order_date,
              datasets: [{
                label: 'Total Certified Amount',
                data: response.line_bil_order_total,
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
                    text: 'Total Certified Amount'
                  }
                }
              }
            }
          });
        }

        // LINE CHART - Total Certified Amount Over Period of Time
        var lineRilCtx = document.getElementById('lineChartRilOverTime').getContext('2d');
        if (lineChartRilOverTime) {
          lineChartRilOverTime.data.labels = response.line_ril_order_date;
          lineChartRilOverTime.data.datasets[0].data = response.line_ril_order_total;
          lineChartRilOverTime.update();
        } else {
          lineChartRilOverTime = new Chart(lineRilCtx, {
            type: 'line',
            data: {
              labels: response.line_ril_order_date,
              datasets: [{
                label: 'Total Certified Amount',
                data: response.line_ril_order_total,
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
                    text: 'Total Certified Amount'
                  }
                }
              }
            }
          });
        }

        // LINE CHART - Total Certified Amount Over Period of Time
        var linePaidCtx = document.getElementById('lineChartPaidOverTime').getContext('2d');
        if (lineChartPaidOverTime) {
          lineChartPaidOverTime.data.labels = response.line_paid_order_date;
          lineChartPaidOverTime.data.datasets[0].data = response.line_paid_order_total;
          lineChartPaidOverTime.update();
        } else {
          lineChartPaidOverTime = new Chart(linePaidCtx, {
            type: 'line',
            data: {
              labels: response.line_paid_order_date,
              datasets: [{
                label: 'Total Certified Amount',
                data: response.line_paid_order_total,
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
                    text: 'Total Certified Amount'
                  }
                }
              }
            }
          });
        }

        // LINE CHART - Total Certified Amount Over Period of Time
        var lineUnpaidCtx = document.getElementById('lineChartUnpaidOverTime').getContext('2d');
        if (lineChartUnpaidOverTime) {
          lineChartUnpaidOverTime.data.labels = response.line_unpaid_order_date;
          lineChartUnpaidOverTime.data.datasets[0].data = response.line_unpaid_order_total;
          lineChartUnpaidOverTime.update();
        } else {
          lineChartUnpaidOverTime = new Chart(lineUnpaidCtx, {
            type: 'line',
            data: {
              labels: response.line_unpaid_order_date,
              datasets: [{
                label: 'Total Certified Amount',
                data: response.line_unpaid_order_total,
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
                    text: 'Total Certified Amount'
                  }
                }
              }
            }
          });
        }

      });
    }
  })(jQuery);
</script>