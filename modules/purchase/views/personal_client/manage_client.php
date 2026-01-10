<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'module_activity_log'; ?>
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
   b{
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
      var report_from = $('input[name="report-from"]');
      var report_to = $('input[name="report-to"]');
      var date_range = $('#date-range');
      var table_manage_client = $('.table-table_manage_client');
      var Params = {};
      initDataTable(table_manage_client, admin_url + 'purchase/table_manage_client', [], [], Params, []);

     

   });
</script>
</body>

</html>