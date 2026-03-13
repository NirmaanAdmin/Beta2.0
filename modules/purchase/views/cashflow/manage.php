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
                            <a href="#cashflow_forecast" aria-controls="cashflow_forecast" role="tab" id="tab_cashflow_forecast" data-toggle="tab">
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
                     <div role="tabpanel" class="col-md-12 tab-pane cashflow-pane active" id="cashflow_forecast">
                        <table class="table dt-table cashflow_forecast_table border">
                           <thead>
                              <tr>
                                 <th><?php echo _l('Period'); ?></th>
                                 <th><?php echo _l('Calendar Month'); ?></th>
                                 <th><?php echo _l('S-Curve Month #'); ?></th>
                                 <th><?php echo _l('Planned Cum. %'); ?></th>
                                 <th><?php echo _l('Planned Monthly CF'); ?></th>
                                 <th><?php echo _l('Planned Cum. CF'); ?></th>
                                 <th><?php echo _l('Actual/Forecast %'); ?></th>
                                 <th><?php echo _l('Forecast Monthly CF'); ?></th>
                                 <th><?php echo _l('Forecast Cum. CF'); ?></th>
                                 <th><?php echo _l('Variance (Plan vs Forecast)'); ?></th>
                                 <th><?php echo _l('Status'); ?></th>
                                 <th><?php echo _l('Realistic Month # (At Current Pace)'); ?></th>
                                 <th><?php echo _l('Realistic Calendar Month'); ?></th>
                                 <th><?php echo _l('Delay vs Plan (Months)'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                        <div class="col-md-8 col-md-offset-4 mtop15">
                           <table class="table text-right">
                              <tbody>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Last Actual Period Month :
                                       </span>
                                    </td>
                                    <td class="last_actual_period_month"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Planned Cum % at Month <span class="last_actual_period_month"></span> :
                                       </span>
                                    </td>
                                    <td class="planned_cum_percentage"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Actual Cum % at Month <span class="last_actual_period_month"></span> :
                                       </span>
                                    </td>
                                    <td class="actual_cum_percentage"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Delay Indicator (Plan - Actual) :
                                       </span>
                                    </td>
                                    <td class="delay_indicator"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Remaining Budget to Spend :
                                       </span>
                                    </td>
                                    <td class="remaining_budget_to_spend"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Remaining Months :
                                       </span>
                                    </td>
                                    <td class="remaining_months"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Current Speed Ratio :
                                       </span>
                                    </td>
                                    <td class="current_speed_ratio"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Projected Total Duration (Months) :
                                       </span>
                                    </td>
                                    <td class="projected_total_duration"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Projected Completion Date :
                                       </span>
                                    </td>
                                    <td class="projected_completion_date"></td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <span class="bold">
                                          Total Delay (Months) :
                                       </span>
                                    </td>
                                    <td class="total_delay"></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div role="tabpanel" class="col-md-12 tab-pane cashflow-pane" id="industry_standard_scurve">
                        <table class="table dt-table industry_standard_scurve_table border">
                           <thead>
                              <tr>
                                 <th><?php echo _l('Timeline %'); ?></th>
                                 <th><?php echo _l('Cumulative Cashflow %'); ?></th>
                                 <th><?php echo _l('Months'); ?></th>
                                 <th><?php echo _l('Incremental %'); ?></th>
                                 <th><?php echo _l('Budget'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>
                     <div role="tabpanel" class="col-md-12 tab-pane cashflow-pane" id="actual_spending_on_project">
                        <table class="table dt-table actual_spending_on_project_table border">
                           <thead>
                              <tr>
                                 <th><?php echo _l('Month #'); ?></th>
                                 <th><?php echo _l('Actual Cum. %'); ?></th>
                                 <th><?php echo _l('Actual Cum. Amount'); ?></th>
                              </tr>
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

            var cashflow_forecast_tbody = '';
            if (Array.isArray(data.cashflow_forecast) && data.cashflow_forecast.length > 0) {
               $.each(data.cashflow_forecast, function(i, row){
                  cashflow_forecast_tbody += '<tr>';
                  cashflow_forecast_tbody += '<td>'+row.period+'</td>';
                  cashflow_forecast_tbody += '<td>'+row.months_cal_name+'</td>';
                  cashflow_forecast_tbody += '<td>'+row.months_cal+'</td>';
                  cashflow_forecast_tbody += '<td>'+parseFloat(row.cumulative_cashflow_percentage).toFixed(2)+'%</td>';
                  cashflow_forecast_tbody += '<td>'+format_money(row.planned_monthly_cf)+'</td>';
                  cashflow_forecast_tbody += '<td>'+format_money(row.planned_cum_cf)+'</td>';
                  cashflow_forecast_tbody += '<td>'+parseFloat(row.actual_forecast_percentage).toFixed(2)+'%</td>';
                  cashflow_forecast_tbody += '<td>'+format_money(row.forecast_monthly_cf)+'</td>';
                  cashflow_forecast_tbody += '<td>'+format_money(row.forecast_cum_cf)+'</td>';
                  cashflow_forecast_tbody += '<td>'+format_money(row.variance)+'</td>';
                  cashflow_forecast_tbody += '<td>'+row.status+'</td>';
                  cashflow_forecast_tbody += '<td>'+parseFloat(row.realistic_month).toFixed(2)+'</td>';
                  cashflow_forecast_tbody += '<td>'+row.realistic_calendar_month+'</td>';
                  cashflow_forecast_tbody += '<td>'+parseFloat(row.delay_vs_plan).toFixed(2)+'</td>';
               });
            }
            $('.cashflow_forecast_table tbody').html(cashflow_forecast_tbody);

            $('.last_actual_period_month').html(data.last_actual_period_month);
            $('.planned_cum_percentage').html(parseFloat(data.planned_cum_percentage).toFixed(2)+'%');
            $('.actual_cum_percentage').html(parseFloat(data.actual_cum_percentage).toFixed(2)+'%');
            $('.delay_indicator').html(parseFloat(data.delay_indicator).toFixed(2)+'%');
            $('.remaining_budget_to_spend').html(format_money(data.remaining_budget_to_spend));
            $('.remaining_months').html(data.remaining_months);
            $('.current_speed_ratio').html(parseFloat(data.current_speed_ratio).toFixed(2)+'%');
            $('.projected_total_duration').html(parseFloat(data.projected_total_duration).toFixed(2));
            $('.projected_completion_date').html(data.projected_completion_date);
            $('.total_delay').html(parseFloat(data.total_delay).toFixed(2));
      });
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>