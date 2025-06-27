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

	    // PIE CHART - Pie Chart for Invoice per Project
	    var projectPieCtx = document.getElementById('pieChartForProject').getContext('2d');
	    var projectData = response.pie_project_value;
	    var projectLabels = response.pie_project_name;

	    if (window.poByProjectChart) {
	      poByProjectChart.data.labels = projectLabels;
	      poByProjectChart.data.datasets[0].data = projectData;
	      poByProjectChart.update();
	    } else {
	      window.poByProjectChart = new Chart(projectPieCtx, {
	        type: 'pie',
	        data: {
	          labels: projectLabels,
	          datasets: [{
	            data: projectData,
	            backgroundColor: projectLabels.map((_, i) => `hsl(${i * 35 % 360}, 70%, 60%)`),
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