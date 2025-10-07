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
      width: 25% !important;
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
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('activity_log'); ?></h4>
                           <hr />
                        </div>
                     </div>

                     <div class="row all_filters mtop20">
                        <div class="col-md-3">
                           <?php
                           $module_name_filter = get_module_filter($module_name, 'module_name');
                           $module_name_filter_val = !empty($module_name_filter) ? explode(",", $module_name_filter->filter_value) : [];
                           if (isset($_GET['module']) && $_GET['module'] == 'vbt') {
                              $module_name_filter_val = $_GET['module'];
                           }
                           if (isset($_GET['module']) && $_GET['module'] == 'ot') {
                              $module_name_filter_val = $_GET['module'];
                           }
                           if (isset($_GET['module']) && $_GET['module'] == 'pc') {
                              $module_name_filter_val = $_GET['module'];
                           }
                           $module_name_list = [
                              ['id' => 'vbt', 'name' => _l('vendor_billing_tracker')],
                              ['id' => 'ot', 'name' => _l('order_tracker')],
                              ['id' => 'pc', 'name' => _l('payment_certificate')],
                              ['id' => 'dms', 'name' => _l('Drawing Management')],
                              ['id' => 'dmg', 'name' => _l('Document Management')],
                           ];
                           echo render_select('module_name[]', $module_name_list, array('id', 'name'), '', $module_name_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('module'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_filters">
                              <?php echo _l('reset_filter'); ?>
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
                              'decription',
                              'date',
                              'staff',
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

                     <table class="dt-table-loading table table-table_activity_log">
                        <thead>
                           <tr>
                              <th><?php echo _l('decription'); ?></th>
                              <th><?php echo _l('date'); ?></th>
                              <th><?php echo _l('staff'); ?></th>
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
      var table_activity_log = $('.table-table_activity_log');
      var Params = {
         "module_name": "[name='module_name[]']",
      };
      initDataTable(table_activity_log, admin_url + 'purchase/table_activity_log', [], [], Params, [1, 'desc']);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_activity_log.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_filters', function() {
         var filterArea = $('.all_filters');
         filterArea.find('input').val("");
         filterArea.find('select').selectpicker("val", "");
         table_activity_log.DataTable().ajax.reload();
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table_activity_log.DataTable().column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table_activity_log.DataTable().columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      table_activity_log.on('draw.dt', function () {
         $('.toggle-column[data-id="group_pur"]').prop('checked', false).trigger('change');
         $('.selectpicker').selectpicker('refresh');
      });
   });
</script>
</body>

</html>