<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .show_hide_columns {
     position: absolute;
     z-index: 99999;
     left: 291px;
     top: 792px;
   }
   .n_width {
      width: 33.33% !important;
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
         <div class="col-md-12">
            <div class="_filters _hidden_inputs hidden">
               <?php
               $pur_order_status = [1, 2, 3, 4];
               $estimate_status = [1, 2, 3];
               echo form_hidden('my_vendors');
               foreach ($vendor_categorys as $group) {
                  echo form_hidden('vendor_category_' . $group['id']);
               }

               foreach ($pur_order_status as $status) {
                  echo form_hidden('pur_order_status_' . $status);
               }

               foreach ($estimate_status as $status) {
                  echo form_hidden('estimate_status_' . $status);
               }

               ?>
            </div>

            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-5">
                        <div class="_buttons">
                           <?php if (has_permission('purchase_vendors', '', 'create')) { ?>
                              <a href="<?php echo admin_url('purchase/vendor'); ?>" class="btn btn-info mright5 test pull-left display-block">
                                 <?php echo _l('new_vendor'); ?></a>

                              <a href="<?php echo admin_url('purchase/vendor_import'); ?>" class="btn btn-info mright5 test pull-left display-block">
                                 <?php echo _l('import_vendors'); ?></a>

                              <a href="<?php echo admin_url('purchase/all_contacts'); ?>" class="btn btn-info pull-left display-block mright5">
                                 <?php echo _l('vendor_contacts'); ?></a>


                           <?php } ?>
                           <a href="javascript:void(0);" class="btn btn-info pull-left mright5"
                              data-toggle="collapse"
                              data-target="#vendors-charts-section"
                              aria-expanded="true"
                              aria-controls="vendors-charts-section">
                              <?php echo _l('Vendors Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                           </a>
                        </div>
                     </div>
                     <div class="col-md-7">

                        <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                           <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-filter" aria-hidden="true"></i>
                           </button>
                           <ul class="dropdown-menu dropdown-menu-left widthul">
                              <li class="active"><a href="#" data-cview="all" onclick="dt_custom_view('','.table-vendors',''); return false;"><?php echo _l('customers_sort_all'); ?></a>
                              </li>

                              <li class="divider"></li>
                              <li>
                                 <a href="#" data-cview="my_vendors" onclick="dt_custom_view('my_vendors','.table-vendors','my_vendors'); return false;">
                                    <?php echo _l('vendors_assigned_to_me'); ?>
                                 </a>
                              </li>
                              <li class="divider"></li>
                              <?php if (count($vendor_categorys) > 0) { ?>
                                 <li class="dropdown-submenu pull-left groups">
                                    <a href="#" tabindex="-1"><?php echo _l('vendor_category'); ?></a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                       <?php foreach ($vendor_categorys as $group) { ?>
                                          <li><a href="#" data-cview="vendor_category_<?php echo pur_html_entity_decode($group['id']); ?>" onclick="dt_custom_view('vendor_category_<?php echo pur_html_entity_decode($group['id']); ?>','.table-vendors','vendor_category_<?php echo pur_html_entity_decode($group['id']); ?>'); return false;"><?php echo pur_html_entity_decode($group['category_name']); ?></a></li>
                                       <?php } ?>
                                    </ul>
                                 </li>
                                 <div class="clearfix"></div>
                                 <li class="divider"></li>
                              <?php } ?>

                              <?php if (count($pur_order_status) > 0) { ?>
                                 <li class="dropdown-submenu pull-left groups">
                                    <a href="#" tabindex="-1"><?php echo _l('purchase_order'); ?></a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                       <?php foreach ($pur_order_status as $status) { ?>
                                          <li><a href="#" data-cview="pur_order_status_<?php echo pur_html_entity_decode($status); ?>" onclick="dt_custom_view('pur_order_status_<?php echo pur_html_entity_decode($status); ?>','.table-vendors','pur_order_status_<?php echo pur_html_entity_decode($status); ?>'); return false;"><?php echo _l('contains_purchase_order_by_status') . ' ' . get_status_approve_str($status); ?></a></li>
                                       <?php } ?>
                                    </ul>
                                 </li>
                                 <div class="clearfix"></div>
                                 <li class="divider"></li>
                              <?php } ?>

                              <?php if (count($estimate_status) > 0) { ?>
                                 <li class="dropdown-submenu pull-left groups">
                                    <a href="#" tabindex="-1"><?php echo _l('estimate'); ?></a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                       <?php foreach ($estimate_status as $status) { ?>
                                          <li><a href="#" data-cview="estimate_status_<?php echo pur_html_entity_decode($status); ?>" onclick="dt_custom_view('estimate_status_<?php echo pur_html_entity_decode($status); ?>','.table-vendors','estimate_status_<?php echo pur_html_entity_decode($status); ?>'); return false;"><?php echo _l('contains_purchase_estimate_by_status') . ' ' . get_status_approve_str($status); ?></a></li>
                                       <?php } ?>
                                    </ul>
                                 </li>
                                 <div class="clearfix"></div>
                                 <li class="divider"></li>
                              <?php } ?>
                           </ul>
                        </div>
                        <div class="pull-right">
                           <a href="<?php echo site_url('purchase/vendors_portal'); ?>" target="_blank" class="btn btn-info mright5">
                              <?php echo _l('vendor_login'); ?>
                           </a>
                        </div>
                     </div>
                  </div>

                  <div id="vendors-charts-section" class="collapse in">
                     <div class="row">
                       <div class="col-md-12 mtop20">
                         <div class="row">
                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                             <div class="top_stats_wrapper">
                               <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_title">Total Vendors</span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                               <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_value total_vendors"></span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                             </div>
                           </div>
                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                             <div class="top_stats_wrapper">
                               <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_title">Active / Inactive</span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                               <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_value total_active"></span>  /  <span class="tw-truncate dashboard_stat_value total_inactive"></span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                             </div>
                           </div>
                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                             <div class="top_stats_wrapper">
                               <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_title">Onboarded this week</span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                               <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_value onboarded_this_week"></span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                             </div>
                           </div>
                         </div>
                       </div>
                     </div>
                     <div class="row mtop20">
                        <div class="col-md-4">
                         <p class="mbot15 dashboard_stat_title">Top 10 Vendors by State</p>
                         <div style="width: 100%; height: 500px;">
                           <canvas id="barChartState"></canvas>
                         </div>
                        </div>
                        <div class="col-md-5">
                           <p class="mbot15 dashboard_stat_title">Top 10 Vendors by Category</p>
                           <div style="width: 100%; height: 500px;">
                              <canvas id="barChartCategory"></canvas>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <p class="mbot15 dashboard_stat_title">Missing Info</p>
                           <div class="scroll-wrapper" style="max-height: 461px; overflow-y: auto;overflow-x: clip;">
                              <table class="table table-missing-info">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th><?php echo _l('clients_list_company'); ?></th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="modal bulk_actions" id="table_vendors_list_bulk_actions" tabindex="-1" role="dialog">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           </div>
                           <div class="modal-body">
                              <?php if (has_permission('purchase_vendors', '', 'delete') || is_admin()) { ?>
                                 <div class="checkbox checkbox-danger">
                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                 </div>

                              <?php } ?>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                              <?php if (has_permission('purchase_vendors', '', 'delete') || is_admin()) { ?>
                                 <a href="#" class="btn btn-info" onclick="purchase_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                              <?php } ?>
                           </div>
                        </div>

                     </div>

                  </div>
                  <div class="row col-md-12">
                     <hr />
                  </div>
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
                           'checkbox',
                           'the_number_sign',
                           'clients_list_company',
                           'Company Email',
                           'clients_list_phone',
                           'vendor_category',
                           'customer_active',
                           'pur_date_created',
                        ];
                        ?>
                        <div>
                           <?php foreach ($columns as $key => $label): ?>
                              <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                              <?php echo _l($label); ?><br>
                           <?php endforeach; ?>
                        </div>

                     </div>
                  </div>
                  <a href="#" onclick="staff_bulk_actions(); return false;" data-table=".table-vendors" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
                  <?php
                  $table_data = array();
                  $_table_data = array(
                     '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="vendors"><label></label></div>',
                     array(
                        'name' => _l('the_number_sign'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-number')
                     ),
                     array(
                        'name' => _l('clients_list_company'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-company')
                     ),
                     array(
                        'name' => _l('Company Email'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-company-email')
                     ),

                     array(
                        'name' => _l('clients_list_phone'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-phone')
                     ),
                     array(
                        'name' => _l('vendor_category'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-groups')
                     ),
                     array(
                        'name' => _l('customer_active'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-active')
                     ),

                     array(
                        'name' => _l('pur_date_created'),
                        'th_attrs' => array('class' => 'toggleable', 'id' => 'th-date-created')
                     ),
                  );
                  foreach ($_table_data as $_t) {
                     array_push($table_data, $_t);
                  }

                  $custom_fields = get_custom_fields('vendors', array('show_on_table' => 1));
                  foreach ($custom_fields as $field) {
                     array_push($table_data, $field['name']);
                  }

                  render_datatable($table_data, 'vendors', [], [
                     'data-last-order-identifier' => 'vendors',
                     'data-default-order'         => get_table_last_order('vendors'),
                  ]);
                  ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table = $('.table-vendors').DataTable();

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table.column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table.columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      $('#vendors-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#vendors-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      });
   });
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>