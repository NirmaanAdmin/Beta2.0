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
      });
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>
</html>