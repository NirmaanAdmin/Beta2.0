<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'per_client'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 200px
   }

   .n_width {
      width: 20% !important;
   }

   .dashboard_stat_title {
      font-size: 19px;
      font-weight: bold;
   }

   .dashboard_stat_value {
      font-size: 19px;
   }

   .bulk-title {
      font-weight: bold;
   }

   b {
      font-weight: 700;
   }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">

         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">
                     <div class="row">
                        <div class="col-md-12">
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('Client Data'); ?></h4>
                           <hr />
                        </div>
                     </div>
                     <div class="row all_filters mtop20">

                        <div class="col-md-2 form-group">
                           <?php
                           $months_filter = get_module_filter($module_name, 'months');
                           $month_filter_val = !empty($months_filter) ? $months_filter->filter_value : '';
                           ?>
                           <select class="selectpicker" name="months" data-width="100%" data-none-selected-text="<?php echo _l('Year'); ?>">
                              <option value=""></option>
                              <option value="2025" <?php echo ($month_filter_val == '2025') ? 'selected' : ''; ?>>2025</option>
                           </select>
                        </div>

                        <div class="col-md-2 form-group">
                           <?php
                           $frequency_filter = get_module_filter($module_name, 'frequency');
                           $frequency_filter_val = !empty($frequency_filter) ? $frequency_filter->filter_value : '';
                           ?>
                           <select class="selectpicker" name="frequency" data-width="100%" data-none-selected-text="<?php echo _l('Frequency'); ?>">
                              <option value="all" <?php echo ($frequency_filter_val == 'all' || $frequency_filter_val == '') ? 'selected' : ''; ?>>All</option>
                              <option value="Monthly" <?php echo ($frequency_filter_val == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                              <option value="Quarterly" <?php echo ($frequency_filter_val == 'Quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                           </select>
                        </div>

                        <div class="col-md-2 form-group">
                           <?php
                           $per_client_filter = get_module_filter($module_name, 'per_client');
                           $per_client_filter_val = !empty($per_client_filter) ? explode(",", $per_client_filter->filter_value) : [];
                           echo render_select('per_client[]', $per_clients, array('id', 'name'), '', $per_client_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Client'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>

                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                              <?php echo _l('reset_filter'); ?>
                           </a>
                        </div>
                     </div>

                     <table class="dt-table-loading table table-table_manage_client">
                        <thead>
                           <tr>
                              <th><?php echo _l('Client Id'); ?></th>
                              <th><?php echo _l('Name'); ?></th>
                              <th><?php echo _l('Phone'); ?></th>
                              <th><?php echo _l('Start Date'); ?></th>
                              <th><?php echo _l('Investment'); ?></th>
                              <th><?php echo _l('Frequency'); ?></th>
                              <th><?php echo _l('August 2025'); ?></th>
                              <th><?php echo _l('September 2025'); ?></th>
                              <th><?php echo _l('October 2025'); ?></th>
                              <th><?php echo _l('November 2025'); ?></th>
                              <th><?php echo _l('December 2025'); ?></th>
                              <th><?php echo _l('Earned To Date'); ?></th>
                              <th><?php echo _l('Percent Profits'); ?></th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                        <tbody></tbody>
                     </table>

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
      var table_manage_client = $('.table-table_manage_client');
      var Params = {
         "months": "[name='months']",
         "frequency": "[name='frequency']",
         "per_client": "[name='per_client[]']",
      };
      initDataTable(table_manage_client, admin_url + 'purchase/table_manage_client', [], [], Params, []);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_manage_client.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_filters', function() {
         var filterArea = $('.all_filters');
         filterArea.find('input').val("");
         filterArea.find('select').selectpicker("val", "");
         table_manage_client.DataTable().ajax.reload();
      });


   });
</script>
</body>

</html>