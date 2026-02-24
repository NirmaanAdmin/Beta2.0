<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-3">
                     <?php echo render_input('total_months', _l('Total months'), 42, 'number'); ?>
                  </div>
                  <div class="col-md-3">
                     <?php echo render_date_input('start_date', _l('Start date'), '01-01-2026'); ?>
                  </div>
                  <div class="col-md-3">
                     <?php echo render_input('budgeted', _l('Budgeted'), 4070000000, 'number'); ?>
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
                        <th><?php echo _l('Monthly Cashflow ('.$base_currency->name.')'); ?></th>
                        <th><?php echo _l('Cumulative Cashflow ('.$base_currency->name.')'); ?></th>
                        <th><?php echo _l('Forecast Monthly Cashflow'); ?></th>
                        <th><?php echo _l('Actual Cumulative Cashflow'); ?></th>
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
         $.each(data, function(i, row){
            tbody += '<tr>';
            tbody += '<td>'+row.timeline+'%</td>';
            tbody += '<td>'+row.cumulative_cashflow+'%</td>';
            tbody += '<td>'+row.months_cal+'</td>';
            tbody += '<td>'+row.actual_forecast_percentage.toFixed(2)+'%</td>';
            tbody += '<td>'+row.months_cal_name+'</td>';
            tbody += '<td>'+format_money(row.monthly_cashflow_value)+'</td>';
            tbody += '<td>'+format_money(row.cumulative_cashflow_value)+'</td>';
            tbody += '<td>'+format_money(row.forecast_monthly_cashflow)+'</td>';
            tbody += '<td>'+format_money(row.actual_cumulative_cashflow)+'</td>';
            tbody += '</tr>';
         });
         $('table tbody').html(tbody);
     });
   }
</script>
</body>
</html>