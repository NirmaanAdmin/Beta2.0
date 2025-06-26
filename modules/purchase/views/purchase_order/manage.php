<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
   .dashboard_stat_title {
      font-size: 18px;
      font-weight: bold;
   }
</style>
<?php $module_name = 'purchase_order'; ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="_buttons col-md-3">
                     <?php if (has_permission('purchase_orders', '', 'create') || is_admin()) { ?>
                        <a href="<?php echo admin_url('purchase/pur_order'); ?>" class="btn btn-info pull-left mright10 display-block">
                           <?php echo _l('new_pur_order'); ?>
                        </a>
                     <?php } ?>
                     <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#po-charts-section" aria-expanded="true"aria-controls="po-charts-section">
                         <?php echo _l('PO Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                     </button>
                  </div>
                  <div class="_buttons col-md-1 pull-right">
                     <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right" onclick="toggle_small_pur_order_view('.table-table_pur_order','#pur_order'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                  </div>
               </div>
               <div id="po-charts-section" class="collapse in">
                  <div class="row">
                     <div class="col-md-12 mtop20">
                        <div class="row">
                           <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                          </path>                          
                                       </svg>                          
                                       <span class="tw-truncate">Total PO Value</span>                 
                                    </div>                      
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 total_po_value"></span>  
                                 </div>                    
                                 <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                    <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                    </div>                  
                                 </div>              
                              </div>          
                           </div>

                           <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                          </path>                          
                                       </svg>                          
                                       <span class="tw-truncate">Approved PO Value</span>                 
                                    </div>                      
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 approved_po_value"></span>  
                                 </div>                    
                                 <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                    <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                    </div>                  
                                 </div>              
                              </div>          
                           </div>

                           <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3 tw-mb-2 sm:tw-mb-0">    <div class="top_stats_wrapper">                                  
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600">                              
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                          </path>                          
                                       </svg>                          
                                       <span class="tw-truncate">Draft PO Value</span>                 
                                    </div>                      
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0 draft_po_value"></span>  
                                 </div>                    
                                 <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini">                      
                                    <div class="progress-bar progress-bar-primary no-percent-text not-dynamic" role="progressbar" aria-valuenow="100.00" aria-valuemin="0" aria-valuemax="100" style="width: 100%;" data-percent="100.00">                      
                                    </div>                  
                                 </div>              
                              </div>          
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mtop20">
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Pie Chart for PO Approval Status</p>
                        <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                           <canvas id="pieChartForPOApprovalStatus"></canvas>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Pie Chart for PO per Budget Head</p>
                        <div style="width: 100%; height: 490px; display: flex; justify-content: left;">
                           <canvas id="pieChartForPoByBudget"></canvas>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Doughnut Chart for Delivery Status</p>
                        <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                           <canvas id="doughnutChartDeliveryStatus"></canvas>
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
                  <?php
                  // Retrieve the filter values for purchase request type
                  $purchase_request_type_filter = get_module_filter($module_name, 'purchase_request');
                  $purchase_request_type_filter_val = !empty($purchase_request_type_filter) ? explode(",", $purchase_request_type_filter->filter_value) : [];

                  ?>
                  <div class="col-md-2 form-group">
                     <label for="pur_request"><?php echo _l('pur_request'); ?></label>
                     <select name="pur_request[]" id="pur_request" class="selectpicker" onchange="coppy_pur_request(); return false;" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <?php foreach ($pur_request as $s) { ?>
                           <option value="<?php echo pur_html_entity_decode($s['id']); ?>"
                              <?php if (in_array($s['id'], $purchase_request_type_filter_val)) {
                                 echo 'selected';
                              } ?>>
                              <?php echo pur_html_entity_decode($s['pur_rq_code'] . ' - ' . $s['pur_rq_name']); ?>
                           </option>
                        <?php } ?>
                     </select>
                  </div>

                  <div class="col-md-3 form-group">

                     <?php
                     $approval_status_type_filter = get_module_filter($module_name, 'pur_approval_status');
                     $approval_status_type_filter_val = !empty($approval_status_type_filter) ? explode(",", $approval_status_type_filter->filter_value) : [];
                     $statuses = [
                        0 => ['id' => '1', 'name' => _l('purchase_not_yet_approve')],
                        1 => ['id' => '2', 'name' => _l('purchase_approved')],
                        2 => ['id' => '3', 'name' => _l('purchase_reject')],
                        3 => ['id' => '4', 'name' => _l('cancelled')],
                     ];

                     echo render_select('status[]', $statuses, array('id', 'name'), 'approval_status', $approval_status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $vendor_type_filter = get_module_filter($module_name, 'pur_vendor_filter');
                     $vendor_type_filter_val = !empty($vendor_type_filter) ? explode(",", $vendor_type_filter->filter_value) : [];

                     echo render_select('vendor_ft[]', $vendors, array('userid', 'company'), 'vendor', $vendor_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>

                  <div class="col-md-3 form-group">
                     <?php
                     $group_pur_type_filter = get_module_filter($module_name, 'group_pur');
                     $group_pur_type_filter_val = !empty($group_pur_type_filter) ? explode(",", $group_pur_type_filter->filter_value) : [];
                     /* <label for="type"><?php echo _l('type'); ?></label>
                     <select name="type[]" id="type" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                        <option value="capex"><?php echo _l('capex'); ?></option>
                        <option value="opex"><?php echo _l('opex'); ?></option>
                     </select> */ ?>
                     <?php echo render_select('group_pur[]', $item_group, array('id', 'name'), 'group_pur', $group_pur_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>
                  <?php
                  // Retrieve the project filter value for the current module
                  $project_type_filter = get_module_filter($module_name, 'project');
                  // Convert the filter value into an array or default to an empty array
                  $project_type_filter_val = !empty($project_type_filter) ? explode(",", $project_type_filter->filter_value) : [];
                  ?>
                  <div class="col-md-3 form-group">
                     <label for="project"><?php echo _l('project'); ?></label>
                     <select name="project[]" id="project" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                        <?php foreach ($projects as $pj) { ?>
                           <option value="<?php echo pur_html_entity_decode($pj['id']); ?>"
                              <?php echo in_array($pj['id'], $project_type_filter_val) ? 'selected' : ''; ?>>
                              <?php echo pur_html_entity_decode($pj['name']); ?>
                           </option>
                        <?php } ?>
                     </select>
                  </div>
                  <?php
                  // Retrieve the department filter value for the current module
                  $department_type_filter = get_module_filter($module_name, 'department');
                  // Convert the filter value into an array or default to an empty array
                  $department_type_filter_val = !empty($department_type_filter) ? explode(",", $department_type_filter->filter_value) : [];
                  ?>
                  <div class="col-md-3 form-group">
                     <label for="department"><?php echo _l('department'); ?></label>
                     <select name="department[]" id="department" class="selectpicker" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>" <?php echo !empty($department_type_filter_val) ? 'disabled' : ''; ?>>

                        <?php foreach ($departments as $dpm) { ?>
                           <option value="<?php echo pur_html_entity_decode($dpm['departmentid']); ?>"
                              <?php echo in_array($dpm['departmentid'], $department_type_filter_val) ? 'selected' : ''; ?>>
                              <?php echo pur_html_entity_decode($dpm['name']); ?>
                           </option>
                        <?php } ?>
                     </select>
                  </div>
                  <?php
                  // Retrieve the delivery status filter value for the current module
                  $delivery_status_type_filter = get_module_filter($module_name, 'delivery_status');
                  // Convert the filter value into an array or default to an empty array
                  $delivery_status_type_filter_val = !empty($delivery_status_type_filter) ? explode(",", $delivery_status_type_filter->filter_value) : [];
                  ?>
                  <div class="col-md-3 form-group">
                     <label for="delivery_status"><?php echo _l('delivery_status'); ?></label>
                     <select name="delivery_status[]" id="delivery_status" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('leads_all'); ?>">
                        <option value="0" <?php echo in_array('0', $delivery_status_type_filter_val) ? 'selected' : ''; ?>><?php echo _l('undelivered'); ?></option>
                        <option value="1" <?php echo in_array('1', $delivery_status_type_filter_val) ? 'selected' : ''; ?>><?php echo _l('completely_delivered'); ?></option>
                        <option value="2" <?php echo in_array('2', $delivery_status_type_filter_val) ? 'selected' : ''; ?>><?php echo _l('pending_delivered'); ?></option>
                        <option value="3" <?php echo in_array('3', $delivery_status_type_filter_val) ? 'selected' : ''; ?>><?php echo _l('partially_delivered'); ?></option>
                     </select>
                  </div>


               </div>
               <div class="row">
                  <div class="col-md-1 form-group">
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
                              'purchase_order',
                              'vendor',
                              'po_description',
                              'order_date',
                              'group_pur',
                              //'sub_groups_pur',
                              'cat',
                              'project',
                              'department',
                              'approval_status',
                              // 'convert_expense',
                              'po_value',
                              'tax_value',
                              'po_value_included_tax',
                              'tags',
                              'delivery_date',
                              'delivery_status',
                              'payment_status'
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
                                 <th><?php echo _l('purchase_order'); ?></th>
                                 <th><?php echo _l('vendor'); ?></th>
                                 <th><?php echo _l('po_description'); ?></th>
                                 <th><?php echo _l('order_date'); ?></th>
                                 <th><?php echo _l('group_pur'); ?></th>
                                 <th><?php echo _l('cat'); ?></th>
                                 <th><?php echo _l('project'); ?></th>
                                 <th><?php echo _l('department'); ?></th>
                                 <th><?php echo _l('approval_status'); ?></th>
                                 <th><?php echo _l('po_value'); ?></th>
                                 <th><?php echo _l('tax_value'); ?></th>
                                 <th><?php echo _l('po_value_included_tax'); ?></th>
                                 <th><?php echo _l('tags'); ?></th>
                                 <th><?php echo _l('delivery_date'); ?></th>
                                 <th><?php echo _l('delivery_status'); ?></th>
                                 <th><?php echo _l('payment_status'); ?></th>
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
                              <td class="total_po_value"></td>
                              <td class="total_tax_value"></td>
                              <td class="total_po_value_included_tax"></td>
                              <td></td>
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
         <?php echo form_open(admin_url('purchase/add_expense'), array('id' => 'pur_order-expense-form', 'class' => 'dropzone dropzone-manual')); ?>
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
            <?php echo render_input('expense_name', 'description_of_services'); ?>
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
                           <option value="<?php echo pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo pur_html_entity_decode($tax['name']); ?>"><?php echo pur_html_entity_decode($tax['taxrate']); ?>%</option>
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
                           <option value="<?php echo pur_html_entity_decode($tax['id']); ?>" data-subtext="<?php echo pur_html_entity_decode($tax['name']); ?>"><?php echo pur_html_entity_decode($tax['taxrate']); ?>%</option>
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

      $('#po-charts-section').on('shown.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
      });

      $('#po-charts-section').on('hidden.bs.collapse', function () {
         $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
      });
   });
</script>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>