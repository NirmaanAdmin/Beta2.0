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
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('ASSAR'); ?></h4>
                           <hr />
                        </div>
                        <div class="_buttons col-md-3">
                           <a href="<?php echo admin_url('purchase/add_assar'); ?>" class="btn btn-info pull-left mright10 display-block">
                              <?php echo _l('New'); ?>
                           </a>
                        </div>
                     </div>


                     <br>


                     <div class="btn-group show_hide_columns" id="show_hide_columns">
                        <!-- Settings Icon -->
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                           <i class="fa fa-cog"></i> <?php  ?> <span class="caret"></span>
                        </button>
                        <!-- Dropdown Menu with Checkboxes -->
                        <div class="dropdown-menu" style="padding: 10px; min-width: 250px;">
                           <!-- Select All / Deselect All -->
                           <div>
                              <input type="checkbox" id="select-all-columns"> <strong><?php echo _l('select_all'); ?></strong>
                           </div>
                           <hr>
                           <!-- Column Checkboxes -->
                           <?php
                           $columns = [
                              'Client ID',
                              'Name',
                              'Phone',
                              'Start Date',
                              'Investment Amount',
                              'Status (Active/Inactive)',
                              'Referred by',
                              'Remarks',
                           ];
                           ?>
                           <div>
                              <?php foreach ($columns as $key => $label): ?>
                                 <input type="checkbox" class="toggle-column" data-id="<?php echo $label; ?>" value="<?php echo $key; ?>" checked>
                                 <?php echo _l($label); ?><br>
                              <?php endforeach; ?>
                           </div>

                        </div>
                     </div>

                     <table class="dt-table-loading table table-table_assar">
                        <thead>
                           <tr>
                              <th><?php echo _l('Client ID'); ?></th>
                              <th><?php echo _l('Name'); ?></th>
                              <th><?php echo _l('Phone'); ?></th>
                              <th><?php echo _l('Start Date'); ?></th>
                              <th><?php echo _l('Investment Amount'); ?></th>
                              <th><?php echo _l('Status (Active/Inactive)'); ?></th>
                              <th><?php echo _l('Referred by'); ?></th>
                              <th><?php echo _l('Remarks'); ?></th>
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
      var table_assar = $('.table-table_assar');
      var Params = {};
      initDataTable(table_assar, admin_url + 'purchase/table_assar', [], [], Params, [3, 'asc']);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_assar.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_filters', function() {
         var filterArea = $('.all_filters');
         filterArea.find('input').val("");
         filterArea.find('select').selectpicker("val", "");
         table_assar.DataTable().ajax.reload();
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table_assar.DataTable().column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table_assar.DataTable().columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      table_assar.on('draw.dt', function() {
         $('.toggle-column[data-id="group_pur"]').prop('checked', false).trigger('change');
         $('.selectpicker').selectpicker('refresh');
      });


   });
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>