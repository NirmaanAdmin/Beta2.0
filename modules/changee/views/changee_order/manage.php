<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$module_name = 'changee_order'; ?>
<style>
   .show_hide_columns {
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
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="_buttons col-md-3">
                     <?php if (has_permission('changee_orders', '', 'create') || is_admin()) { ?>
                        <a href="<?php echo admin_url('changee/pur_order'); ?>" class="btn btn-info pull-left mright10 display-block">
                           <?php echo _l('new_pur_order'); ?>
                        </a>
                     <?php } ?>
                     <div class="btn-group pull-left">
                        <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('co_voucher'); ?>&nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                           <li class="hidden-xs"><a href="<?php echo admin_url('changee/po_voucher?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                           <li class="hidden-xs"><a href="<?php echo admin_url('changee/po_voucher?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                           <li><a href="<?php echo admin_url('changee/po_voucher'); ?>"><?php echo _l('download'); ?></a></li>
                           <li>
                              <a href="<?php echo admin_url('changee/po_voucher?print=true'); ?>" target="_blank">
                                 <?php echo _l('print'); ?>
                              </a>
                           </li>
                        </ul>
                     </div>
                     <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#co-charts-section" aria-expanded="true"aria-controls="co-charts-section">
                     <?php echo _l('CO Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                     </button>
                  </div>

                  <div class="_buttons col-md-1 pull-right">
                     <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right" onclick="toggle_small_pur_order_view('.table-table_pur_order','#pur_order'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                  </div>
               </div>
               <div id="co-charts-section" class="collapse in">
                  <div class="row">
                     <div class="col-md-12 mtop20">
                        <div class="row">
                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                             <div class="top_stats_wrapper">
                               <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_title">Total CO Value</span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                               <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_value total_co_value"></span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                             </div>
                           </div>
                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                             <div class="top_stats_wrapper">
                               <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_title">Approved CO Value</span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                               <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_value approved_co_value"></span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                             </div>
                           </div>
                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
                             <div class="top_stats_wrapper">
                               <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_title">Draft CO Value</span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                               </div>
                               <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                 <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                   <span class="tw-truncate dashboard_stat_value draft_co_value"></span>
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
                        <p class="mbot15 dashboard_stat_title">Pie Chart for CO Approval Status</p>
                        <div style="width: 100%; height: 440px; display: flex; justify-content: left;">
                           <canvas id="pieChartForCOApprovalStatus"></canvas>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Pie Chart for CO per Budget Head</p>
                        <div style="width: 100%; height: 500px; display: flex; justify-content: left;">
                           <canvas id="pieChartForCoByBudget"></canvas>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Total Amount Over Time</p>
                        <div style="width: 100%; height: 490px;">
                          <canvas id="lineChartOverTime"></canvas>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row all_ot_filters">
                  <hr>
                  <div class="col-md-2">
                     <?php
                     $from_date_type_filter = get_module_filter($module_name, 'from_date');
                     $from_date_type_filter_val = !empty($from_date_type_filter) ?  $from_date_type_filter->filter_value : '';
                     echo render_date_input('from_date', _l('from_date'), $from_date_type_filter_val); ?>
                  </div>
                  <div class="col-md-2">
                     <?php
                     $to_date_type_filter = get_module_filter($module_name, 'to_date');
                     $to_date_type_filter_val = !empty($to_date_type_filter) ?  $to_date_type_filter->filter_value : '';
                     echo render_date_input('to_date', _l('to_date'), $to_date_type_filter_val); ?>
                  </div>

                  <div class=" col-md-2 form-group">
                     <label for="co_request"><?php echo _l('co_request'); ?></label>
                     <?php
                     $changee_request_type_filter = get_module_filter($module_name, 'changee_request');
                     $changee_request_type_filter_val = !empty($changee_request_type_filter) ?  $changee_request_type_filter->filter_value : '';

                     ?>
                     <select name="co_request[]" id="co_request" class="selectpicker" onchange="coppy_co_request(); return false;" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <?php foreach ($co_request as $s) { ?>
                           <option value="<?php echo changee_pur_html_entity_decode($s['id']); ?>" <?php if ($changee_request_type_filter_val == $s['id']) {
                                                                                                      echo 'selected';
                                                                                                   } ?>><?php echo changee_pur_html_entity_decode($s['pur_rq_code'] . ' - ' . $s['pur_rq_name']); ?></option>
                        <?php } ?>
                     </select>
                  </div>

                  <div class="col-md-3 form-group">
                     <?php
                     $status_type_filter = get_module_filter($module_name, 'status');
                     $status_type_filter_val = !empty($status_type_filter) ? explode(",", $status_type_filter->filter_value) : [];
                     $statuses = [
                        0 => ['id' => '1', 'name' => _l('changee_not_yet_approve')],
                        1 => ['id' => '2', 'name' => _l('changee_approved')],
                        2 => ['id' => '3', 'name' => _l('changee_reject')],
                        3 => ['id' => '4', 'name' => _l('cancelled')],
                     ];

                     echo render_select('status[]', $statuses, array('id', 'name'), 'approval_status', $status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $vendor_type_filter = get_module_filter($module_name, 'vendor');
                     $vendor_type_filter_val = !empty($vendor_type_filter) ? explode(",", $vendor_type_filter->filter_value) : [];
                     echo render_select('vendor_ft[]', $vendors, array('userid', 'company'), 'vendor', $vendor_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>

                  <div class="col-md-3 form-group">
                     <div class="col-md-6">

                        <label for="type"><?php echo _l('type'); ?></label>
                        <?php
                        $type_type_filter = get_module_filter($module_name, 'type');
                        $type_type_filter_val = !empty($type_type_filter) ? explode(",", $type_type_filter->filter_value) : [];
                        ?>
                        <select name="type[]" id="type" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                           <option value="capex" <?php echo in_array('capex', $type_type_filter_val) ? 'selected' : ''; ?>>
                              <?php echo _l('capex'); ?>
                           </option>
                           <option value="opex" <?php echo in_array('opex', $type_type_filter_val) ? 'selected' : ''; ?>>
                              <?php echo _l('opex'); ?>
                           </option>
                        </select>
                     </div>
                     <div class="col-md-6">
                        <label for="project"><?php echo _l('project'); ?></label>
                        <?php
                        $project_type_filter = get_module_filter($module_name, 'project');
                        $project_type_filter_val = !empty($project_type_filter) ? explode(",", $project_type_filter->filter_value) : [];
                        ?>
                        <select name="project[]" id="project" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                           <?php foreach ($projects as $pj) {
                              $project_id = changee_pur_html_entity_decode($pj['id']);
                              $is_selected = in_array($project_id, $project_type_filter_val);
                           ?>
                              <option value="<?php echo $project_id; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                 <?php echo changee_pur_html_entity_decode($pj['name']); ?>
                              </option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>

                  <div class="col-md-3 form-group">
                     <label for="department"><?php echo _l('department'); ?></label>
                     <select name="department[]" readonly="true" id="department" class="selectpicker" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                        <?php
                        $department_type_filter = get_module_filter($module_name, 'department');
                        $department_type_filter_val = !empty($department_type_filter) ? explode(",", $department_type_filter->filter_value) : [];

                        foreach ($departments as $dpm) {
                           $department_id = changee_pur_html_entity_decode($dpm['departmentid']);
                           $is_selected = in_array($department_id, $department_type_filter_val);
                        ?>
                           <option value="<?php echo $department_id; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                              <?php echo changee_pur_html_entity_decode($dpm['name']); ?>
                           </option>
                        <?php } ?>
                     </select>
                  </div>

                  <div class="col-md-3 form-group">
                     <label for="delivery_status"><?php echo _l('delivery_status'); ?></label>
                     <?php
                     $delivery_status_type_filter = get_module_filter($module_name, 'delivery_status'); // Fixed filter key to match field name
                     $delivery_status_type_filter_val = !empty($delivery_status_type_filter) ? explode(",", $delivery_status_type_filter->filter_value) : [];
                     ?>
                     <select name="delivery_status[]" id="delivery_status" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                        <option value="0" <?php echo in_array('0', $delivery_status_type_filter_val) ? 'selected' : ''; ?>>
                           <?php echo _l('undelivered'); ?>
                        </option>
                        <option value="1" <?php echo in_array('1', $delivery_status_type_filter_val) ? 'selected' : ''; ?>>
                           <?php echo _l('completely_delivered'); ?>
                        </option>
                        <option value="2" <?php echo in_array('2', $delivery_status_type_filter_val) ? 'selected' : ''; ?>>
                           <?php echo _l('pending_delivered'); ?>
                        </option>
                        <option value="3" <?php echo in_array('3', $delivery_status_type_filter_val) ? 'selected' : ''; ?>>
                           <?php echo _l('partially_delivered'); ?>
                        </option>
                     </select>
                  </div>
                  <div class="col-md-1 form-group pull-right" style="margin-top: 2%;">
                     <a href="javascript:void(0)" class="btn btn-info btn-icon reset_all_ot_filters">
                        <?php echo _l('reset_filter'); ?>
                     </a>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12" id="small-table">
               <div class="panel_s">
                  <div class="panel-body">

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
                              'changee_order',
                              'vendor',
                              'order_date',
                              'group_pur',
                              'sub_groups_pur',
                              'area_pur',
                              'type',
                              'project',
                              'department',
                              'co_description',
                              'approval_status',
                              'co_value',
                              'tax_value',
                              'co_value_included_tax',
                              'tags',
                              'payment_status',
                              'convert_expense',
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
                     <?php echo form_hidden('pur_orderid', $pur_orderid); ?>

                     <div class="">
                        <table class="dt-table-loading table table-table_pur_order">
                           <thead>
                              <tr>
                                 <th><?php echo _l('changee_order'); ?></th>
                                 <th><?php echo _l('vendor'); ?></th>
                                 <th><?php echo _l('order_date'); ?></th>
                                 <th><?php echo _l('group_pur'); ?></th>
                                 <th><?php echo _l('sub_groups_pur'); ?></th>
                                 <th><?php echo _l('area_pur'); ?></th>
                                 <th><?php echo _l('type'); ?></th>
                                 <th><?php echo _l('project'); ?></th>
                                 <th><?php echo _l('department'); ?></th>
                                 <th><?php echo _l('co_description'); ?></th>
                                 <th><?php echo _l('approval_status'); ?></th>
                                 <th><?php echo _l('co_value'); ?></th>
                                 <th><?php echo _l('tax_value'); ?></th>
                                 <th><?php echo _l('co_value_included_tax'); ?></th>
                                 <th><?php echo _l('tags'); ?></th>
                                 <th><?php echo _l('payment_status'); ?></th>
                                 <th><?php echo _l('convert_expense'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                           </tbody>
                           <tfoot>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td class="total_co_value"></td>
                              <td class="total_tax_value"></td>
                              <td class="total_co_value_included_tax"></td>
                              <td></td>
                              <td></td>
                              <td></td>
                           </tfoot>
                        </table>
                     </div>


                  </div>
               </div>
            </div>

            <div class="col-md-7 small-table-right-col">
               <div id="pur_order" class="hide">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="pur_order_expense" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <?php echo form_open(admin_url('changee/add_expense'), array('id' => 'pur_order-expense-form', 'class' => 'dropzone dropzone-manual')); ?>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('add_new', _l('expense_lowercase')); ?></h4>
         </div>
         <div class="modal-body">
            <div id="dropzoneDragArea" class="dz-default dz-message">
               <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
            </div>
            <div class="dropzone-previews"></div>
            <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('expense_name_help'); ?>"></i>
            <?php echo form_hidden('vendor'); ?>
            <?php echo render_input('expense_name', 'expense_name'); ?>
            <?php echo render_textarea('note', 'expense_add_edit_note', '', array('rows' => 4), array()); ?>
            <?php echo render_select('clientid', $customers, array('userid', 'company'), 'customer'); ?>

            <?php echo render_select('project_id', $projects, array('id', 'name'), 'project'); ?>

            <?php echo render_select('category', $expense_categories, array('id', 'name'), 'expense_category'); ?>
            <?php echo render_date_input('date', 'expense_add_edit_date', _d(date('Y-m-d'))); ?>
            <?php echo render_input('amount', 'expense_add_edit_amount', '', 'number'); ?>
            <div class="row mbot15">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php foreach ($taxes as $tax) { ?>
                           <option value="<?php echo changee_pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo changee_pur_html_entity_decode($tax['name']); ?>"><?php echo changee_pur_html_entity_decode($tax['taxrate']); ?>%</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                     <select class="selectpicker display-block" data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" disabled>
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php foreach ($taxes as $tax) { ?>
                           <option value="<?php echo changee_pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo changee_pur_html_entity_decode($tax['name']); ?>"><?php echo changee_pur_html_entity_decode($tax['taxrate']); ?>%</option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="hide">
               <?php echo render_select('currency', $currencies, array('id', 'name', 'symbol'), 'expense_currency', $currency->id); ?>
            </div>

            <div class="checkbox checkbox-primary">
               <input type="checkbox" id="billable" name="billable" checked>
               <label for="billable"><?php echo _l('expense_add_edit_billable'); ?></label>
            </div>
            <?php echo render_input('reference_no', 'expense_add_edit_reference_no'); ?>

            <?php
            // Fix becuase payment modes are used for invoice filtering and there needs to be shown all
            // in case there is payment made with payment mode that was active and now is inactive
            $expenses_modes = array();
            foreach ($payment_modes as $m) {
               if (isset($m['invoices_only']) && $m['invoices_only'] == 1) {
                  continue;
               }
               if ($m['active'] == 1) {
                  $expenses_modes[] = $m;
               }
            }
            ?>
            <?php echo render_select('paymentmode', $expenses_modes, array('id', 'name'), 'payment_mode'); ?>
            <div class="clearfix mbot15"></div>
            <?php echo render_custom_fields('expenses'); ?>
            <div id="pur_order_additional"></div>
            <div class="clearfix"></div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>


<?php init_tail(); ?>
<script>
   $(document).ready(function() {
      var table = $('.table-table_pur_order').DataTable();

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

      $('#co-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#co-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      });
   });

   $(document).ready(function() {
        var table = $('.table-table_pur_order').DataTable();

        // On page load, fetch and apply saved preferences for the logged-in user
        $.ajax({
            url: admin_url + 'purchase/getPreferences',
            type: 'GET',
            data: {
                module: 'changee_order'
            },
            dataType: 'json',
            success: function(data) {
                console.log("Retrieved preferences:", data);

                // Ensure DataTable is initialized
                let table = $('.table-table_pur_order').DataTable();

                // Loop through each toggle checkbox to update column visibility
                $('.toggle-column').each(function() {
                    // Parse the column index (ensuring it's a number)
                    let colIndex = parseInt($(this).val(), 10);

                    // Use the saved preference if available; otherwise, default to visible ("true")
                    let prefValue = data.preferences && data.preferences[colIndex] !== undefined ?
                        data.preferences[colIndex] :
                        "true";

                    // Convert string to boolean if needed
                    let isVisible = (typeof prefValue === "string") ?
                        (prefValue.toLowerCase() === "true") :
                        prefValue;

                    // Set column visibility but prevent immediate redraw (redraw = false)
                    table.column(colIndex).visible(isVisible, false);
                    // Update the checkbox state accordingly
                    $(this).prop('checked', isVisible);
                });

                // Finally, adjust columns and redraw the table once
                table.columns.adjust().draw();

                // Update the "Select All" checkbox based on individual toggle states
                let allChecked = $('.toggle-column').length === $('.toggle-column:checked').length;
                $('#select-all-columns').prop('checked', allChecked);
            },
            error: function() {
                console.error('Could not retrieve column preferences.');
            }
        });



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

            // Save updated preferences
            saveColumnPreferences();
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
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
                    module: 'changee_order'

                },
                success: function(response) {
                    console.log('Preferences saved successfully.');
                },
                error: function() {
                    console.error('Failed to save preferences.');
                }
            });
        }
    });
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>