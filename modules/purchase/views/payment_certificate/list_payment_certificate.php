<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'payment_certificate'; ?>
<style>
   .show_hide_columns {
      position: absolute;
      z-index: 5000;
      left: 337px
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
   .bulk-title {
      font-weight: bold;
   }
   .export-btn-div {
      position: absolute;
      z-index: 999;
      left: 240px;
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
                        <div class="col-md-12">
                           <?php if (has_permission('payment_certificate', '', 'create') || is_admin()) { ?>
                              <a href="<?php echo admin_url('purchase/ot_payment_certificate'); ?>" class="btn btn-info pull-left mright10 display-block">
                              <?php echo _l('new_payment_certificate'); ?>
                              </a>
                           <?php } ?>
                           <a href="<?php echo admin_url('purchase/activity_log?module=pc'); ?>" class="btn btn-info pull-left mright10 display-block" target="_blank">
                           <?php echo _l('activity_log'); ?>
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
                        <div class="col-md-3 form-group">
                           <?php
                           $vendors_type_filter = get_module_filter($module_name, 'vendors');
                           $vendors_type_filter_val = !empty($vendors_type_filter) ? explode(",", $vendors_type_filter->filter_value) : [];
                           echo render_select('vendors[]', $vendors, array('userid', 'company'), '', $vendors_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('pur_vendor'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-3 form-group">
                           <?php
                           $group_pur_type_filter = get_module_filter($module_name, 'group_pur');
                           $group_pur_type_filter_val = !empty($group_pur_type_filter) ? explode(",", $group_pur_type_filter->filter_value) : [];
                           echo render_select('group_pur[]', $item_group, array('id', 'name'), '', $group_pur_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('group_pur'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-3 form-group">
                           <?php
                           $approval_status_type_filter = get_module_filter($module_name, 'approval_status');
                           $approval_status_type_filter_val = !empty($approval_status_type_filter) ? explode(",", $approval_status_type_filter->filter_value) : [];
                           $payment_status = [
                              ['id' => 1, 'name' => _l('approval_request_sent')],
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
                        <div class="col-md-3 form-group">
                           <select name="projects[]" id="projects" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                              <?php foreach ($projects as $pj) { ?>
                                 <option value="<?php echo pur_html_entity_decode($pj['id']); ?>"
                                    <?php echo in_array($pj['id'], $projects_type_filter_val) ? 'selected' : ''; ?>>
                                    <?php echo pur_html_entity_decode($pj['name']); ?>
                                 </option>
                              <?php } ?>
                           </select>
                        </div>
                        <div class="col-md-3 form-group">
                           <?php
                           $applied_to_vendor_bill_filter = get_module_filter($module_name, 'applied_to_vendor_bill');
                           $applied_to_vendor_bill_filter_val = !empty($applied_to_vendor_bill_filter) ? explode(",", $applied_to_vendor_bill_filter->filter_value) : [];
                           $applied_to_vendor_bill = [
                              ['id' => 1, 'name' => _l('convert_to_vendor_bill')],
                              ['id' => 2, 'name' => 'Converted'],
                              ['id' => 3, 'name' => 'Pending'],
                           ];
                           echo render_select('applied_to_vendor_bill[]', $applied_to_vendor_bill, array('id', 'name'), '', $applied_to_vendor_bill_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('applied_to_vendor_bill'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-3">
                           <?php
                           $order_tagged_detail_filter = get_module_filter($module_name, 'order_tagged_detail');
                           $order_tagged_detail_filter_val = !empty($order_tagged_detail_filter) ? explode(",", $order_tagged_detail_filter->filter_value) : '';
                           echo render_select('order_tagged_detail[]', $order_tagged_detail, array('id', 'name'), '', $order_tagged_detail_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Order Detail'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-3 form-group">
                           <?php
                           $res_person_filter = get_module_filter($module_name, 'res_person');
                           $res_person_filter_val = !empty($res_person_filter) ? explode(",", $res_person_filter->filter_value) : [];
                           echo render_select('res_person[]', $responsible_person, array('staffid', ['firstname','lastname']), '', $res_person_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('responsible_person'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                           ?>
                        </div>
                        <div class="col-md-1 form-group ">
                           <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                              <?php echo _l('reset_filter'); ?>
                           </a>
                        </div>
                     </div>
                     <br>

                     <?php if (has_permission('payment_certificate', '', 'view') || is_admin()) { ?>
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
                                 'Checkbox',
                                 'Payment cert',
                                 'payment_certificate_number',
                                 'order_name',
                                 'vendor',
                                 'order_date',
                                 'group_pur',
                                 'this_bill',
                                 'submission_date',
                                 'approval_status',
                                 'pending_approval',
                                 'applied_to_vendor_bill',
                                 'Invoice',
                                 _l('options'),
                                 'responsible_person',
                                 'last_action_by',
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

                        <div class="row">
                           <a onclick="bulk_convert_payment_certificate(); return false;" data-toggle="modal" data-table=".table-table_payment_certificate" class=" hide bulk-actions-btn table-btn">Bulk Convert</a>
                        </div>

                        <div class="btn-group export-btn-div" id="export-btn-div">
                           <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                              <i class="fa fa-download"></i> <?php echo _l('Export'); ?> <span class="caret"></span>
                           </button>
                           <div class="dropdown-menu" style="padding: 10px;min-width: 94px;">
                              <a class="dropdown-item export-btn" href="<?php echo admin_url('purchase/payment_certificate_export_pdf'); ?>" data-type="pdf">
                                 <i class="fa fa-file-pdf text-danger"></i> PDF
                              </a><br>
                              <a class="dropdown-item export-btn" href="<?php echo admin_url('purchase/payment_certificate_export_excel'); ?>" data-type="excel">
                                 <i class="fa fa-file-excel text-success"></i> Excel
                              </a>
                           </div>
                        </div>

                        <table class="dt-table-loading table table-table_payment_certificate">
                           <thead>
                              <tr>
                                 <th style="width: 5px"><span class="hide"> - </span>
                                    <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_payment_certificate"><label></label></div>
                                 </th>
                                 <th><?php echo _l('Payment cert'); ?></th>
                                 <th><?php echo _l('payment_certificate_number'); ?></th>
                                 <th><?php echo _l('order_name'); ?></th>
                                 <th><?php echo _l('vendor'); ?></th>
                                 <th><?php echo _l('order_date'); ?></th>
                                 <th><?php echo _l('group_pur'); ?></th>
                                 <th><?php echo _l('this_bill'); ?></th>
                                 <th><?php echo _l('submission_date'); ?></th>
                                 <th><?php echo _l('approval_status'); ?></th>
                                 <th><?php echo _l('pending_approval'); ?></th>
                                 <th><?php echo _l('applied_to_vendor_bill'); ?></th>
                                 <th><?php echo _l('Invoice'); ?></th>
                                 <th><?php echo _l('options'); ?></th>
                                 <th><?php echo _l('responsible_person'); ?></th>
                                 <th><?php echo _l('last_action_by'); ?></th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                           <tbody></tbody>
                        </table>
                     <?php } ?>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="convert_payment_certificate_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
         <?php echo form_open(admin_url('purchase/add_bulk_convert_payment_certificate'), array('id' => 'convert_payment_certificate_form', 'class' => '')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><div class="bulk_convert_title"></div></h4>
         </div>
         <div class="modal-body convert-bulk-actions-body">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" name="save_convert_to_vendor_bill" value="1" class="btn btn-info">Save & Convert to Vendor Bill</button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table_payment_certificate = $('.table-table_payment_certificate');

      // On page load, fetch and apply saved preferences for the logged-in user
      $.ajax({
         url: admin_url + 'purchase/getPreferences',
         type: 'GET',
         data: {
            module: 'payment_certificate'
         },
         dataType: 'json',
         success: function(data) {
            let table_payment_certificate = $('.table-table_payment_certificate').DataTable();
            $('.toggle-column').each(function() {
               let colIndex = parseInt($(this).val(), 10);
               let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
                  data.preferences[colIndex] :
                  "true";
               let isVisible = (typeof prefValue === "string") ?
                  (prefValue.toLowerCase() === "true") :
                  prefValue;
               table_payment_certificate.column(colIndex).visible(isVisible, false);
               $(this).prop('checked', isVisible);
            });
            table_payment_certificate.columns.adjust().draw();
            let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
            $('#select-all-columns').prop('checked', allChecked);
         },
         error: function() {
            console.error('Could not retrieve column preferences.');
         }
      });

      var Params = {
         "vendors": "[name='vendors[]']",
         "group_pur": "[name='group_pur[]']",
         "approval_status": "[name='approval_status[]']",
         "projects": "[name='projects[]']",
         "applied_to_vendor_bill": "[name='applied_to_vendor_bill[]']",
         "order_tagged_detail": "[name='order_tagged_detail[]']",
         "res_person": "[name='res_person[]']",
      };
      initDataTable(table_payment_certificate, admin_url + 'purchase/table_payment_certificate', [], [], Params, [8, 'desc']);
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

         // Save updated preferences
         saveColumnPreferences();
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

      table_payment_certificate.on('draw.dt', function () {
         // $('.toggle-column[data-id="group_pur"]').prop('checked', false).trigger('change');
         $('.selectpicker').selectpicker('refresh');
      });

      // Function to collect and save preferences via AJAX
      function saveColumnPreferences() {
         var preferences = {};
         $('.toggle-column').each(function() {
            preferences[$(this).val()] = $(this).is(':checked');
         });
         $.ajax({
            url: admin_url + 'purchase/savePreferences',
            type: 'POST',
            data: {
               preferences: preferences,
               module: 'payment_certificate'

            },
            success: function(response) {
               console.log('Preferences saved successfully.');
            },
            error: function() {
               console.error('Failed to save preferences.');
            }
         });
      }

      $(document).on('change', 'select[name="responsible_person[]"]', function(e) {
         e.preventDefault();
         var responsible_person = $(this).val();
         var id = $(this).data('id');
         $.post(admin_url + 'purchase/update_pc_responsible_person', {
            id: id,
            responsible_person: responsible_person
         }).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
               alert_float('success', response.message);
               table_payment_certificate.DataTable().ajax.reload();
            }
          });
      });

      $(document).on('click', '.convert-pur-invoice', function(e) {
         e.preventDefault();
         var url = $(this).data('url');
         Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to convert this payment certificate to a vendor bill?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, convert it!',
            cancelButtonText: 'Cancel'
         }).then((result) => {
            if (result.isConfirmed) {
              window.open(url, '_blank');
            }
         });
      });

      $(document).on('click', '.delete_payment_cert', function(e) {
         e.preventDefault();
         var url = $(this).attr('href');
         Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to remove this payment certificate?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
         }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = url;
            }
         });
      });

      $('body').on('click', '.update_pc_convert', function (e) {
         e.preventDefault();
         var convert_responsible_person = $('#convert_responsible_person').val();
         if(convert_responsible_person) {
            $('select#bulk_responsible_person').val(convert_responsible_person).trigger('change');
         }
      });

      $('.buttons-collection').hide();
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

   function bulk_convert_payment_certificate() {
     "use strict";
     var print_id = '';
     var rows = $('.table-table_payment_certificate').find('tbody tr');
     $.each(rows, function() {
       var checkbox = $($(this).find('td').eq(0)).find('input');
       if (checkbox.prop('checked') === true) {
           if (print_id !== '') {
               print_id += ','; // Append a comma before adding the next value
           }
           print_id += checkbox.val();
       }
     });
     if (print_id !== '') {
       // Perform AJAX request to update the invoice date
       $.post(admin_url + 'purchase/bulk_convert_payment_certificate', {
         ids: print_id,
       }).done(function (response) {
         response = JSON.parse(response);
         if (response.success) {
           $('.convert-bulk-actions-body').html('');
           $('.convert-bulk-actions-body').html(response.bulk_html);
           $('.bulk_convert_title').html('Bulk Convert');
           init_selectpicker();
           $('#convert_payment_certificate_modal').modal('show');
         } else {
           alert_float('danger', response.message);
         }
       });
     } else {
       alert_float('danger', 'Please select at least one item from the list');
     }
   }
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>