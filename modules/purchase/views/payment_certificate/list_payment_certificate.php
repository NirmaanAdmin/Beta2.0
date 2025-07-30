<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'payment_certificate'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 204px
   }

   .show_hide_columns1 {
      position: absolute;
      z-index: 5000;
      left: 204px
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
                           <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('payment_certificate'); ?></h4>
                           <hr />
                        </div>
                        <div class="col-md-3">
                           <a href="<?php echo admin_url('purchase/ot_payment_certificate'); ?>" class="btn btn-info pull-left mright10 display-block">
                           <?php echo _l('new_payment_certificate'); ?>
                           </a>
                           <button class="btn btn-info display-block" type="button" data-toggle="collapse" data-target="#pc-charts-section" aria-expanded="true" aria-controls="pc-charts-section">
                              <?php echo _l('Payment Certificate Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                           </button>
                        </div>
                     </div>

                     <div id="pc-charts-section" class="collapse in">
                        <div class="row">
                           <div class="col-md-12 mtop20">
                              <div class="row">
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                   <div class="top_stats_wrapper">
                                     <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_title">Total Purchase Orders</span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                     <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_value total_purchase_orders"></span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                   </div>
                                 </div>
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                   <div class="top_stats_wrapper">
                                     <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_title">Total Work Orders</span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                     <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_value total_work_orders"></span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                   </div>
                                 </div>
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                   <div class="top_stats_wrapper">
                                     <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_title">Total Certified Value</span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                     <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_value total_certified_value"></span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                   </div>
                                 </div>
                                 <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                                   <div class="top_stats_wrapper">
                                     <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_title">Approved Payment Certificates</span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                     <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                       <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                         <span class="tw-truncate dashboard_stat_value approved_payment_certificates"></span>
                                       </div>
                                       <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                     </div>
                                   </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row mtop20">
                           <div class="col-md-6">
                              <p class="mbot15 dashboard_stat_title">Bar Chart for Top 10 Vendors by Certified Value</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="barChartTopVendors"></canvas>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <p class="mbot15 dashboard_stat_title">Total Certified Value Over Time</p>
                              <div style="width: 100%; height: 400px;">
                                <canvas id="lineChartOverTime"></canvas>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row all_ot_filters mtop20">
                        <div class="col-md-2 form-group">
                           <?php
                           $vendors_type_filter = get_module_filter($module_name, 'vendors');
                           $vendors_type_filter_val = !empty($vendors_type_filter) ? explode(",", $vendors_type_filter->filter_value) : [];
                           echo render_select('vendors[]', $vendors, array('userid', 'company'), '', $vendors_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('pur_vendor'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-2 form-group">
                           <?php
                           $group_pur_type_filter = get_module_filter($module_name, 'group_pur');
                           $group_pur_type_filter_val = !empty($group_pur_type_filter) ? explode(",", $group_pur_type_filter->filter_value) : [];
                           echo render_select('group_pur[]', $item_group, array('id', 'name'), '', $group_pur_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('group_pur'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-2 form-group">
                           <?php
                           $approval_status_type_filter = get_module_filter($module_name, 'approval_status');
                           $approval_status_type_filter_val = !empty($approval_status_type_filter) ? explode(",", $approval_status_type_filter->filter_value) : [];
                           $payment_status = [
                              ['id' => 1, 'name' => 'Send approval request'],
                              ['id' => 2, 'name' => 'Approved'],
                              ['id' => 3, 'name' => 'Rejected'],
                           ];
                           echo render_select('approval_status[]', $payment_status, array('id', 'name'), '', $approval_status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('approval_status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <?php
                        $projects_type_filter = get_module_filter($module_name, 'projects');
                        $projects_type_filter_val = !empty($projects_type_filter) ? explode(",", $projects_type_filter->filter_value) : [];
                        ?>
                        <div class="col-md-2 form-group">
                           <select name="projects[]" id="projects" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                              <?php foreach ($projects as $pj) { ?>
                                 <option value="<?php echo pur_html_entity_decode($pj['id']); ?>"
                                    <?php echo in_array($pj['id'], $projects_type_filter_val) ? 'selected' : ''; ?>>
                                    <?php echo pur_html_entity_decode($pj['name']); ?>
                                 </option>
                              <?php } ?>
                           </select>
                        </div>
                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
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
                              'Payment cert',
                              'project',
                              'order_name',
                              'vendor',
                              'order_date',
                              'group_pur',
                              'approval_status',
                              'applied_to_vendor_bill'
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


                     <?php $table_data = array(
                        _l('Payment cert'),
                        _l('project'),
                        _l('order_name'),
                        _l('vendor'),
                        _l('order_date'),
                        _l('group_pur'),
                        _l('approval_status'),
                        _l('applied_to_vendor_bill'),
                     );

                     foreach ($custom_fields as $field) {
                        array_push($table_data, $field['name']);
                     }
                     render_datatable($table_data, 'table_payment_certificate');
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
      var table_payment_certificate = $('.table-table_payment_certificate');
      var Params = {
         "vendors": "[name='vendors[]']",
         "group_pur": "[name='group_pur[]']",
         "approval_status": "[name='approval_status[]']",
         "projects": "[name='projects[]']",
      };
      initDataTable(table_payment_certificate, admin_url + 'purchase/table_payment_certificate', [], [], Params, [4, 'desc']);
      $.each(Params, function(i, obj) {
         $('select' + obj).on('change', function() {
            table_payment_certificate.DataTable().ajax.reload();
         });
      });
      $(document).on('click', '.reset_all_ot_filters', function() {
         var filterArea = $('.all_ot_filters');
         filterArea.find('input').val("");
         filterArea.find('select').not('select[name="projects[]"]').selectpicker("val", "");
         table_payment_certificate.DataTable().ajax.reload();
      });

      $('#pc-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#pc-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      });

      // Handle "Select All" checkbox
      $('#select-all-columns').on('change', function() {
         var isChecked = $(this).is(':checked');
         $('.toggle-column').prop('checked', isChecked).trigger('change');
      });

      // Handle individual column visibility toggling
      $('.toggle-column').on('change', function() {
         var column = table_payment_certificate.DataTable().column($(this).val());
         column.visible($(this).is(':checked'));

         // Sync "Select All" checkbox state
         var allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
         $('#select-all-columns').prop('checked', allChecked);
      });

      // Sync checkboxes with column visibility on page load
      table_payment_certificate.DataTable().columns().every(function(index) {
         var column = this;
         $('.toggle-column[value="' + index + '"]').prop('checked', column.visible());
      });

      // Prevent dropdown from closing when clicking inside
      $('.dropdown-menu').on('click', function(e) {
         e.stopPropagation();
      });

      $(document).on('click', '.convert-pur-invoice', function(e) {
       e.preventDefault();
       var url = $(this).data('url');
       if (confirm('Are you sure you want to convert this payment certificate to a vendor bill?')) {
         window.open(url, '_blank');
       }
      });

   });
</script>
<script>
   function send_payment_certificate_approve(id, rel_type){
     "use strict";
     var data = {};
     data.rel_id = id;
     data.rel_type = rel_type;
     $("body").append('<div class="dt-loader"></div>');
       $.post(admin_url + 'purchase/send_payment_certificate_approve', data).done(function(response){
           response = JSON.parse(response);
           $("body").find('.dt-loader').remove();
           if (response.success === true || response.success == 'true') {
               alert_float('success', response.message);
               window.location.reload();
           }else{
             alert_float('warning', response.message);
               window.location.reload();
           }
       });
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>