<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_name = 'cashflow'; ?>
<style>
   .n_width {
      width: 25% !important;
   }
   .n1_width {
      width: 30% !important;
   }
   .dashboard_stat_title {
      font-size: 19px;
      font-weight: bold;
   }
   .dashboard_stat_value {
      font-size: 19px;
   }
</style>
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
                     <div class="horizontal-tabs">
                        <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                           <li role="presentation" class="active">
                            <a href="#cashflow_forecast_table" aria-controls="cashflow_forecast_table" role="tab" id="tab_cashflow_forecast_table" data-toggle="tab">
                              CASHFLOW FORECAST TABLE
                            </a>
                           </li>
                           <li role="presentation">
                            <a href="#industry_standard_scurve" aria-controls="industry_standard_scurve" role="tab" id="tab_industry_standard_scurve" data-toggle="tab">
                              INDUSTRY STANDARD S-CURVE
                            </a>
                           </li>
                           <li role="presentation">
                            <a href="#actual_spending_on_project" aria-controls="actual_spending_on_project" role="tab" id="tab_actual_spending_on_project" data-toggle="tab">
                              ACTUAL SPENDING ON PROJECT
                            </a>
                           </li>
                        </ul>
                     </div>
                  </div>

                  <div class="tab-content">
                     <div role="tabpanel" class="col-md-12 tab-pane cashflow-pane active" id="cashflow_forecast_table">
                     </div>
                     <div role="tabpanel" class="col-md-12 tab-pane cashflow-pane" id="industry_standard_scurve">
                        <table class="table dt-table industry_standard_scurve_table border">
                           <thead>
                              <th><?php echo _l('Timeline %'); ?></th>
                              <th><?php echo _l('Cumulative Cashflow %'); ?></th>
                              <th><?php echo _l('Months'); ?></th>
                              <th><?php echo _l('Incremental %'); ?></th>
                              <th><?php echo _l('Budget'); ?></th>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>
                     <div role="tabpanel" class="col-md-12 tab-pane cashflow-pane" id="actual_spending_on_project">
                        <table class="table dt-table actual_spending_on_project_table border">
                           <thead>
                              <th><?php echo _l('Month #'); ?></th>
                              <th><?php echo _l('Actual Cum. %'); ?></th>
                              <th><?php echo _l('Actual Cum. Amount'); ?></th>
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
            var industry_standard_tbody = '';
            if (Array.isArray(data.industry_standard_scurve) && data.industry_standard_scurve.length > 0) {
               $.each(data.industry_standard_scurve, function(i, row){
                  industry_standard_tbody += '<tr>';
                  industry_standard_tbody += '<td>'+row.timelines_percentage+'%</td>';
                  industry_standard_tbody += '<td>'+row.cumulative_cashflow_percentage+'%</td>';
                  industry_standard_tbody += '<td>'+row.months_cal+'</td>';
                  industry_standard_tbody += '<td>'+parseFloat(row.incremental_percentage).toFixed(2)+'%</td>';
                  industry_standard_tbody += '<td>'+format_money(row.budget_value)+'</td>';
               });
            }
            $('.industry_standard_scurve_table tbody').html(industry_standard_tbody);

            var actual_spending_tbody = '';
            if (Array.isArray(data.actual_spending_on_project) && data.actual_spending_on_project.length > 0) {
               $.each(data.actual_spending_on_project, function(i, row){
                  actual_spending_tbody += '<tr>';
                  actual_spending_tbody += '<td>'+row.months_cal+'</td>';
                  actual_spending_tbody += '<td>'+parseFloat(row.actual_cum_percentage).toFixed(2)+'%</td>';
                  actual_spending_tbody += '<td>'+format_money(row.actual_cum_amount)+'</td>';
               });
            }
            $('.actual_spending_on_project_table tbody').html(actual_spending_tbody);
      });
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>