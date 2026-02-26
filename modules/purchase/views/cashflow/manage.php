<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'cashflow'; ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-3">
                     <?php
                     $total_months_type_filter = get_module_filter($module_name, 'total_months');
                     $total_months_type_filter_val = !empty($total_months_type_filter) ?  $total_months_type_filter->filter_value : 42;
                     echo render_input('total_months', _l('Total months'), $total_months_type_filter_val, 'number'); 
                     ?>
                  </div>
                  <div class="col-md-3">
                     <?php
                     $start_date_type_filter = get_module_filter($module_name, 'start_date');
                     $start_date_type_filter_val = !empty($start_date_type_filter) ?  $start_date_type_filter->filter_value : '01-01-2026'; 
                     echo render_date_input('start_date', _l('Start date'), $start_date_type_filter_val); 
                     ?>
                  </div>
                  <div class="col-md-3">
                     <?php
                     $budgeted_type_filter = get_module_filter($module_name, 'budgeted');
                     $budgeted_type_filter_val = !empty($budgeted_type_filter) ?  $budgeted_type_filter->filter_value : 4070000000; 
                     echo render_input('budgeted', _l('Budgeted'), $budgeted_type_filter_val, 'number'); 
                     ?>
                  </div>
               </div>
            </div>
         </div>
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-12">
                     <div style="width: 100%; height: 550px;">
                       <canvas id="cashflowChart"></canvas>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <table class="table dt-table border">
                     <thead>
                        <th><?php echo _l('Timeline'); ?></th>
                        <th><?php echo _l('Cumulative Cashflow (%)'); ?></th>
                        <th><?php echo _l('Months'); ?></th>
                        <th><?php echo _l('Actual/Forecast %'); ?></th>
                        <th><?php echo _l('Month'); ?></th>
                        <th><?php echo _l('Monthly Cashflow Planned ('.$base_currency->name.')'); ?></th>
                        <th><?php echo _l('Cumulative Cashflow Planned ('.$base_currency->name.')'); ?></th>
                        <th><?php echo _l('Actual Monthly Cashflow'); ?></th>
                        <th><?php echo _l('Actual Cumulative Cashflow'); ?></th>
                        <th><?php echo _l('Predicted Cumulative Cashflow'); ?></th>
                        <th><?php echo _l('Predicted Monthly Cashflow'); ?></th>
                     </thead>
                     <tbody>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   var cashflowChart = null;
   $(document).ready(function() {
      "use strict";
      load_cashflow_data();
      $("body").on('change', 'input[name="total_months"], input[name="start_date"], input[name="budgeted"]', function() {
         load_cashflow_data();
      });
   });

   function load_cashflow_data() {
      var total_months = $('input[name="total_months"]').val();
      var start_date   = $('input[name="start_date"]').val();
      var budgeted     = $('input[name="budgeted"]').val();
      $.post(admin_url + 'purchase/get_cashflow_data', {
         total_months: total_months,
         start_date: start_date,
         budgeted: budgeted
        }, function(response){
            var data = JSON.parse(response);
            var tbody = '';
            var months_cal_name = [];
            var monthly_cashflow_value = [];
            var actual_monthly_cashflow = [];
            var cumulative_cashflow_value = [];
            var actual_cumulative_cashflow = [];
            var predicted_cumulative_cashflow = [];
            var predicted_monthly_cashflow = [];
            if (Array.isArray(data) && data.length > 0) {
               $.each(data, function(i, row){
                  months_cal_name.push(row.months_cal_name);
                  monthly_cashflow_value.push(parseFloat(row.monthly_cashflow_value) || 0);
                  actual_monthly_cashflow.push(parseFloat(row.actual_monthly_cashflow) || 0);
                  cumulative_cashflow_value.push(parseFloat(row.cumulative_cashflow_value) || 0);
                  actual_cumulative_cashflow.push(parseFloat(row.actual_cumulative_cashflow) || 0);
                  predicted_cumulative_cashflow.push(parseFloat(row.predicted_cumulative_cashflow) || 0);
                  predicted_monthly_cashflow.push(parseFloat(row.predicted_monthly_cashflow) || 0);
                  tbody += '<tr>';
                  tbody += '<td>'+row.timeline+'%</td>';
                  tbody += '<td>'+row.cumulative_cashflow+'%</td>';
                  tbody += '<td>'+row.months_cal+'</td>';
                  tbody += '<td>'+parseFloat(row.actual_forecast_percentage).toFixed(2)+'%</td>';
                  tbody += '<td>'+row.months_cal_name+'</td>';
                  tbody += '<td>'+format_money(row.monthly_cashflow_value)+'</td>';
                  tbody += '<td>'+format_money(row.cumulative_cashflow_value)+'</td>';
                  tbody += '<td>'+format_money(row.actual_monthly_cashflow)+'</td>';
                  tbody += '<td>'+format_money(row.actual_cumulative_cashflow)+'</td>';
                  tbody += '<td>'+format_money(row.predicted_cumulative_cashflow)+'</td>';
                  tbody += '<td>'+format_money(row.predicted_monthly_cashflow)+'</td>';
                  tbody += '</tr>';
               });

            }
            $('table tbody').html(tbody);
            render_cashflow_chart(
               months_cal_name,
               monthly_cashflow_value,
               actual_monthly_cashflow,
               cumulative_cashflow_value,
               actual_cumulative_cashflow,
               predicted_cumulative_cashflow,
               predicted_monthly_cashflow
            );
      });
   }

   function render_cashflow_chart(
      months_cal_name,
      monthly_cashflow_value,
      actual_monthly_cashflow,
      cumulative_cashflow_value,
      actual_cumulative_cashflow,
      predicted_cumulative_cashflow,
      predicted_monthly_cashflow
   ) {
      var ctx = document.getElementById('cashflowChart').getContext('2d');
      if (cashflowChart !== null) {
         cashflowChart.destroy();
      }
      cashflowChart = new Chart(ctx, {
         data: {
            labels: months_cal_name,
            datasets: [
               {
                  type: 'bar',
                  label: 'Monthly Cashflow',
                  data: monthly_cashflow_value,
                  backgroundColor: '#1E90FF'
               },
               {
                  type: 'bar',
                  label: 'Actual Monthly Cashflow',
                  data: actual_monthly_cashflow,
                  backgroundColor: '#00A300'
               },
               {
                  type: 'line',
                  label: 'Cumulative Cashflow',
                  data: cumulative_cashflow_value,
                  borderColor: '#dc3545',
                  backgroundColor: '#dc3545',
                  tension: 0.3,
                  fill: false
               },
               {
                  type: 'line',
                  label: 'Actual Cumulative Cashflow',
                  data: actual_cumulative_cashflow,
                  borderColor: '#A300A3',
                  backgroundColor: '#A300A3',
                  tension: 0.3,
                  fill: false
               },
               {
                  type: 'line',
                  label: 'Predicted Cumulative Cashflow',
                  data: predicted_cumulative_cashflow,
                  borderColor: '#8B4000',
                  backgroundColor: '#8B4000',
                  tension: 0.3,
                  fill: false
               },
               {
                  type: 'line',
                  label: 'Predicted Monthly Cashflow',
                  data: predicted_monthly_cashflow,
                  borderColor: '#FFC000',
                  backgroundColor: '#FFC000',
                  tension: 0.3,
                  fill: false,
                  hidden: true
               }
            ]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
               mode: 'index',
               intersect: false
            },
            plugins: {
               legend: {
                  position: 'bottom'
               },
               tooltip: {
                  callbacks: {
                     label: function(context) {
                        return context.dataset.label + ': ' + format_money(context.raw);
                     }
                  }
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
                     text: 'Value'
                  },
                  ticks: {
                     callback: function(value) {
                        return format_money(value);
                     }
                  }
               }
            }
         }
      });
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>