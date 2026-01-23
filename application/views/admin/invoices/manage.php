<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div id="vueApp">
			<div class="row">
				<?php include_once(APPPATH.'views/admin/invoices/filter_params.php'); ?>
				<?php $this->load->view('admin/invoices/list_template'); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<div id="modal-wrapper"></div>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<script>
$(function(){
	init_invoice();
});
</script>
<script>
$(document).ready(function() {
	var table = $('.table-invoices').DataTable();

  // Handle "Select All" checkbox
  $('#select-all-columns').on('change', function() {
      var isChecked = $(this).is(':checked');
      $('.toggle-column').prop('checked', isChecked).trigger('change');
  });

  // Handle individual column visibility toggling
  $('.toggle-column').on('change', function() {
      var column = table.column($(this).val());
      column.visible($(this).is(':checked'));

      // Sync "Select All" checkbox state
      var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
      $('#select-all-columns').prop('checked', allChecked);
  });

  // Sync checkboxes with column visibility on page load
  table.columns().every(function(index) {
      var column = this;
      $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
  });

  // Prevent dropdown from closing when clicking inside
  $('.dropdown-menu').on('click', function(e) {
      e.stopPropagation();
  });
        
  $('#ci-charts-section').on('shown.bs.collapse', function () {
     $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
  });
  $('#ci-charts-section').on('hidden.bs.collapse', function () {
     $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
  });

  get_client_invoices_dashboard();

  var lineChartOverTime;

  function get_client_invoices_dashboard() {
  	"use strict";
	var data = {}
	
  	$.post(admin_url + 'invoices/get_client_invoices_dashboard', data).done(function(response){
	    response = JSON.parse(response);

	    // Update value summaries
	    $('.total_invoices_raised').text(response.total_invoices_raised);
	    $('.total_invoiced_amount').text(response.total_invoiced_amount);
	    $('.average_invoice_value').text(response.average_invoice_value);

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

	   // BAR CHART - Top 10 Vendors by Amount
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
              label: 'Amount',
              data: vendorData,
              backgroundColor: '#1E90FF',
            	borderColor: '#1E90FF',
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
                  text: 'Amount'
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

	    // PIE CHART - Pie Chart for Invoice per Status
	    var statusPieCtx = document.getElementById('pieChartForStatus').getContext('2d');
	    var statusData = response.pie_status_value;
	    var statusLabels = response.pie_status_name;

	    if (window.poByStatusChart) {
	      poByStatusChart.data.labels = statusLabels;
	      poByStatusChart.data.datasets[0].data = statusData;
	      poByStatusChart.update();
	    } else {
	      window.poByStatusChart = new Chart(statusPieCtx, {
	        type: 'pie',
	        data: {
	          labels: statusLabels,
	          datasets: [{
	            data: statusData,
	            backgroundColor: statusLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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
	                  return context.label + ': ' + context.formattedValue;
	                }
	              }
	            }
	          }
	        }
	      });
	    }

  	});
  }
});
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>