<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
   .form-inline textarea.form-control {

      width: auto !important;
   }

   .label-purple {
      background-color: rgb(205, 180, 252) !important;
      color: rgb(109, 0, 159);
   }

   .label-teal {
      background-color: #baf8ff;
      color: #0097A7;
   }

   .label-green {
      background-color: #d0fdd2;
      color: #0f8c14;
   }

   .label-secondary {
      background-color: #e1eef9;
      color: #6c757d;
   }

   .label-orange {
      background-color: #f8eedb;
      color: #FFA500;
   }

   .show_hide_columns {
      position: absolute;
      z-index: 999;
      left: 140px;
   }

   .export-btn-div {
      position: absolute;
      z-index: 999;
      left: 189px;
   }

   .loader-container {
      display: flex;
      justify-content: center;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      z-index: 9999;
   }

   .loader-gif {
      width: 100px;
      /* Adjust the size as needed */
      height: 100px;
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
<?php $module_name = 'order_tracker'; ?>
<div id="wrapper">
   <div class="content">
      <div class="loader-container hide" id="loader-container">
         <img src="<?php echo site_url('modules/purchase/uploads/lodder/lodder.gif') ?>" alt="Loading..." class="loader-gif">
      </div>
      <div class="row">
         <div class="panel_s mbot10">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-12">
                     <h4 class="no-margin font-bold"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php echo _l('order_tracker'); ?></h4>
                     <hr />
                  </div>
               </div>

               <div class="row">
                  <div class="_buttons col-md-12">
                     <button class="btn btn-info pull-left mright10 display-block" style="margin-right: 10px;" data-toggle="modal" data-target="#addNewRowModal">
                        <i class="fa fa-plus"></i> <?php echo _l('New'); ?>
                     </button>
                     <button class="btn btn-info pull-left mleft10 display-block" type="button" data-toggle="collapse" data-target="#ot-charts-section" aria-expanded="true" aria-controls="ot-charts-section">
                        <?php echo _l('Order Tracker Charts'); ?> <i class="fa fa-chevron-down toggle-icon"></i>
                     </button>
                     <a href="<?php echo admin_url('purchase/activity_log?module=ot'); ?>" class="btn btn-info pull-left mleft10 display-block" target="_blank">
                        <?php echo _l('activity_log'); ?>
                     </a>
                  </div>
               </div>

               <div id="ot-charts-section" class="collapse in">
                  <div class="row">
                     <div class="col-md-12 mtop20">
                        <div class="row">
                           <div class="quick-stats-invoices col-md-4 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Cost To Complete</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value cost_to_complete"></span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div>
                           <div class="quick-stats-invoices col-md-4 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Ordered</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value rev_contract_value"></span>&nbsp;
                                       (<span class="tw-truncate dashboard_stat_value percentage_utilized" style="font-size: 10px !important;"></span>)
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div>

                           <!-- <div class="quick-stats-invoices col-md-4 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Net Remaining</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value budgeted_procurement_net_value"></span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div> -->
                           <div class="quick-stats-invoices col-md-4 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Anticipate Variation</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value anticipate_variation"></span>
                                       &nbsp;(<span class="tw-truncate dashboard_stat_value percentage_anticipate_variation" style="font-size: 10px !important;"></span>)
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 mtop20">
                        <div class="row">

                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Unawarded Capex</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value unawarded_capex"></span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div>

                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Work Done Value</span>
                                       &nbsp;(<span class="tw-truncate" style="font-size: 10px !important;">BIL Certified Value</span>)
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value work_done_value"></span>

                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div>

                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Certified By RIL</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value ril_certified_amount"></span>
                                 </div>
                                 <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                              </div>
                           </div>

                           <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 ">
                              <div class="top_stats_wrapper">
                                 <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_title">Payment Due</span>
                                    </div>
                                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
                                 </div>
                                 <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                                       <span class="tw-truncate dashboard_stat_value total_payment_due"></span>
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
                        <p class="mbot15 dashboard_stat_title">Order Status Distribution</p>
                        <div style="width: 100%; height: 450px; display: flex; justify-content: left;">
                           <canvas id="pieChartForStatus"></canvas>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Budgeted vs Actual Procurement by Category</p>
                        <div style="width: 100%; height: 450px;">
                           <canvas id="budgetedVsActualCategory"></canvas>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <p class="mbot15 dashboard_stat_title">Total Order Value Over Time</p>
                        <div style="width: 100%; height: 400px;">
                           <canvas id="lineChartOverTime"></canvas>
                        </div>
                     </div>
                  </div>
                  <div class="row mtop20">
                     <div class="col-md-12">
                        <p class="mbot15 dashboard_stat_title">Total Certified Amount vs Paid Amount Over Period of Time</p>
                        <div style="width: 100%; height: 400px;">
                           <canvas id="lineChartCertifiedOverTime"></canvas>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row all_ot_filters">
                  <hr>
                  <div class="col-md-3 form-group">
                     <?php
                     // Fetch saved filter; ensure we end up with an array
                     $order_tracker_type_filter = get_module_filter($module_name, 'order_tracker_type');
                     if (!empty($order_tracker_type_filter) && $order_tracker_type_filter->filter_value !== '') {
                        $selected_types = explode(',', $order_tracker_type_filter->filter_value);
                     } else {
                        $selected_types = [];
                     }
                     ?>
                     <select
                        name="type[]"
                        id="order_tracker_type"
                        class="selectpicker"
                        multiple
                        data-live-search="true"
                        data-width="100%"
                        data-none-selected-text="<?php echo _l('type'); ?>">
                        <option value="pur_orders" <?php echo in_array('pur_orders', $selected_types) ? 'selected' : ''; ?>>
                           <?php echo _l('pur_order'); ?>
                        </option>
                        <option value="wo_orders" <?php echo in_array('wo_orders', $selected_types) ? 'selected' : ''; ?>>
                           <?php echo _l('wo_order'); ?>
                        </option>
                     </select>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $rli_filter = get_module_filter($module_name, 'rli_filter');
                     $rli_filter_val = !empty($rli_filter) ? $rli_filter->filter_value : '';
                     ?>
                     <select name="rli_filter" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('rli_filter'); ?>" data-actions-box="true">
                        <option value=""></option>
                        <option value="None">None</option>
                        <?php foreach ($rli_filters as $rli) { ?>
                           <option value="<?php echo $rli['id']; ?>" <?php echo ($rli_filter_val == $rli['id']) ? 'selected' : ''; ?>><?php echo $rli['name']; ?></option>
                        <?php } ?>
                     </select>
                  </div>
                  <div class="col-md-3">
                     <?php
                     $vendors_filter = get_module_filter($module_name, 'vendors');
                     $vendors_filter_val = !empty($vendors_filter) ? explode(",", $vendors_filter->filter_value) : '';
                     echo render_select('vendors[]', $vendors, array('userid', 'company'), '', $vendors_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('contractor'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                     ?>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $order_tracker_kind_filter = get_module_filter($module_name, 'order_tracker_kind');
                     $order_tracker_kind_filter_val = !empty($order_tracker_kind_filter) ? $order_tracker_kind_filter->filter_value : '';
                     ?>
                     <select name="kind" id="kind" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('cat'); ?>">
                        <option value=""></option>
                        <option value="Client Supply" <?php echo ($order_tracker_kind_filter_val == "Client Supply") ? 'selected' : ''; ?>><?php echo _l('client_supply'); ?></option>
                        <option value="Bought out items" <?php echo ($order_tracker_kind_filter_val == "Bought out items") ? 'selected' : ''; ?>><?php echo _l('bought_out_items'); ?></option>
                     </select>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $budget_head_filter = get_module_filter($module_name, 'budget_head');
                     $budget_head_filter_val = !empty($budget_head_filter) ? $budget_head_filter->filter_value : '';
                     ?>
                     <select name="budget_head" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('group_pur'); ?>" data-actions-box="true">
                        <option value=""></option>
                        <option value="None">None</option>
                        <?php foreach ($budget_head as $head) { ?>
                           <option value="<?php echo $head['id']; ?>" <?php echo ($budget_head_filter_val == $head['id']) ? 'selected' : ''; ?>><?php echo $head['name']; ?></option>
                        <?php } ?>
                     </select>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $order_type_filter_filter = get_module_filter($module_name, 'order_type_filter');
                     $order_type_filter_val = !empty($order_type_filter_filter) ? $order_type_filter_filter->filter_value : '';
                     ?>
                     <select name="order_type_filter" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('Order Type'); ?>" data-actions-box="true">
                        <option value=""></option>
                        <option value="None">None</option>
                        <option value="fetched" <?php echo ($order_type_filter_val == "fetched") ? 'selected' : ''; ?>>Fetched</option>
                        <option value="created" <?php echo ($order_type_filter_val == "created") ? 'selected' : ''; ?>>Created</option>

                     </select>
                  </div>
                  <div class="col-md-3">
                     <?php
                     $projects_filter = get_module_filter($module_name, 'projects');
                     if (!empty($projects_filter) && $projects_filter->filter_value != '') {
                        $projects_filter_val = !empty($projects_filter) ? explode(",", $projects_filter->filter_value) : '';
                     } else {
                        $projects_filter_val = explode(",", 1);
                     }

                     echo render_select('projects[]', $projects, array('id', 'name'), '', $projects_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('project'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                     ?>
                  </div>
                  <div class="col-md-3 form-group">

                     <?php
                     $aw_unw_order_status_type_filter = get_module_filter($module_name, 'aw_unw_order_status');
                     $aw_unw_order_status_type_filter_val = !empty($aw_unw_order_status_type_filter) ? explode(",", $aw_unw_order_status_type_filter->filter_value) : [];
                     $order_status = [
                        0 => ['id' => '1', 'name' => _l('Awarded')],
                        1 => ['id' => '2', 'name' => _l('Unawarded')],
                        2 => ['id' => '3', 'name' => _l('Awarded by RIL')],
                     ];

                     echo render_select('aw_unw_order_status[]', $order_status, array('id', 'name'), '', $aw_unw_order_status_type_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Order Status'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $yield_filter = get_module_filter($module_name, 'yield');
                     $yield_filter_val = !empty($yield_filter) ? explode(",", $yield_filter->filter_value) : [];
                     $yield = [
                        0 => ['id' => '1', 'name' => 'None'],
                        1 => ['id' => '2', 'name' => 'Profit'],
                        2 => ['id' => '3', 'name' => 'Loss'],
                        3 => ['id' => '4', 'name' => 'Neutral'],
                     ];
                     echo render_select('yield[]', $yield, array('id', 'name'), '', $yield_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('Yield'), 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false); ?>
                  </div>
                  <div class="col-md-3 form-group">
                     <?php
                     $is_change_order_filter = get_module_filter($module_name, 'is_change_order');
                     $is_change_order_filter_val = !empty($is_change_order_filter) ? $is_change_order_filter->filter_value : '';
                     $is_change_order_arr = [
                        [
                           'id' => 1,
                           'name' => _l('Yes'),
                        ],
                        [
                           'id' => 2,
                           'name' => _l('No'),
                        ]
                     ];
                     echo render_select('is_change_order', $is_change_order_arr, array('id', 'name'), '', $is_change_order_filter_val, array('data-width' => '100%', 'data-none-selected-text' => _l('is_change_order'), '', 'data-actions-box' => true), array(), 'no-mbot', '', true);
                     ?>
                  </div>
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
                              _l('order_status'),
                              _l('order_scope'),
                              _l('contractor'),
                              _l('order_date'),
                              _l('completion_date'),
                              _l('budget_ro_projection'),
                              _l('committed_contract_amount'),
                              _l('change_order_amount'),
                              _l('total_rev_contract_value'),
                              _l('anticipate_variation'),
                              _l('cost_to_complete'),
                              'Total Certified Amount By BIL',
                              'RIL Certified Amount',
                              'Yield',
                              'Yield Delta',
                              _l('attachment_upload'),
                              _l('attachment_download'),
                              _l('project'),
                              _l('rli_filter'),
                              _l('category'),
                              _l('group_pur'),
                              _l('remarks'),
                              _l('last_action_by')
                           ];
                           ?>
                           <div>
                              <?php foreach ($columns as $key => $label): ?>
                                 <input type="checkbox" class="toggle-column" value="<?php echo $key; ?>" checked>
                                 <?php echo $label; ?><br>
                              <?php endforeach; ?>
                           </div>

                        </div>
                     </div>
                     <div class="btn-group export-btn-div" id="export-btn-div">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px 7px;">
                           <i class="fa fa-download"></i> <?php echo _l('Export'); ?> <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu" style="padding: 10px;min-width: 94px;">
                           <a class="dropdown-item export-btn" href="<?php echo admin_url('purchase/order_tracker_pdf'); ?>" data-type="pdf">
                              <i class="fa fa-file-pdf text-danger"></i> PDF
                           </a><br>
                           <a class="dropdown-item export-btn" href="<?php echo admin_url('purchase/order_tracker_excel'); ?>" data-type="excel">
                              <i class="fa fa-file-excel text-success"></i> Excel
                           </a>
                        </div>
                     </div>

                     <div class="">
                        <table class="dt-table-loading table table-table_order_tracker">
                           <thead>
                              <tr>
                                 <th><?php echo _l('order_status'); ?></th>
                                 <th><?php echo _l('order_scope'); ?></th>
                                 <th><?php echo _l('contractor'); ?></th>
                                 <th><?php echo _l('order_date'); ?></th>
                                 <th><?php echo _l('completion_date'); ?></th>
                                 <th><?php echo _l('budget_ro_projection'); ?></th>
                                 <th><?php echo _l('committed_contract_amount'); ?></th>
                                 <th><?php echo _l('change_order_amount'); ?></th>
                                 <th><?php echo _l('total_rev_contract_value'); ?></th>
                                 <th><?php echo _l('anticipate_variation'); ?></th>
                                 <th><?php echo _l('cost_to_complete'); ?></th>
                                 <th>Total Certified Amount By BIL</th>
                                 <th>RIL Certified Amount</th>
                                 <th>Yield</th>
                                 <th>Yield Delta</th>
                                 <th><?php echo _l('attachment_upload'); ?></th>
                                 <th><?php echo _l('attachment_download'); ?></th>
                                 <th><?php echo _l('project'); ?></th>
                                 <th><?php echo _l('rli_filter'); ?></th>
                                 <th><?php echo _l('category'); ?></th>
                                 <th><?php echo _l('group_pur'); ?></th>
                                 <th><?php echo _l('remarks'); ?></th>
                                 <th><?php echo _l('last_action_by'); ?></th>
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
                              <td class="total_budget_ro_projection"></td>
                              <td class="total_committed_contract_amount"></td>
                              <td class="total_change_order_amount"></td>
                              <td class="total_rev_contract_value"></td>
                              <td class="total_anticipate_variation"></td>
                              <td class="total_cost_to_complete"></td>
                              <td class="total_final_certified_amount"></td>
                              <td class="total_ril_certified_amount"></td>
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
                           </tfoot>
                        </table>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="addNewRowModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 98%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('Add New Order'); ?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="col-md-8 pull-right">
               <div class="col-md-2 pull-right">
                  <div id="dowload_file_sample" style="margin-top: 22px;">
                     <label for="file_csv" class="control-label"> </label>
                     <a href="<?php echo site_url('modules/purchase/uploads/file_sample/Sample_import_order_tracker_en.xlsx') ?>" class="btn btn-primary">Template</a>
                  </div>
               </div>
               <div class="col-md-4 pull-right" style="display: flex;align-items: end;padding: 0px;">
                  <?php echo form_open_multipart(admin_url('purchase/import_file_xlsx_order_tracker_items'), array('id' => 'import_form')); ?>
                  <?php echo form_hidden('leads_import', 'true'); ?>
                  <?php echo render_input('file_csv', 'choose_excel_file', '', 'file'); ?>

                  <div class="form-group" style="margin-left: 10px;">
                     <button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv(this);"><?php echo _l('import'); ?></button>
                  </div>
                  <?php echo form_close(); ?>
               </div>

            </div>
            <div class="col-md-12 ">
               <div class="form-group pull-right" id="file_upload_response">

               </div>

            </div>
            <div id="box-loading" class="pull-right">

            </div>
         </div>
         <div class="modal-body invoice-item">
            <div class="row">
               <div class="col-md-12">
                  <div class="table-responsive" style="overflow-x: unset !important;">
                     <?php
                     echo form_open_multipart('', array('id' => 'order_tracker-form'));
                     ?>
                     <table class="table order-tracker-items-table items table-main-invoice-edit has-calculations no-mtop">
                        <thead>
                           <tr>
                              <!-- <th align="left"><?php echo _l('serial_no'); ?></th> -->
                              <th align="left"><?php echo _l('order_scope'); ?></th>
                              <th align="left"><?php echo _l('contractor'); ?></th>
                              <th align="left"><?php echo _l('order_date'); ?></th>
                              <th align="left"><?php echo _l('completion_date'); ?></th>
                              <th align="left"><?php echo _l('budget_ro_projection'); ?></th>
                              <th align="left"><?php echo _l('order_value'); ?></th>
                              <th align="left"><?php echo _l('committed_contract_amount'); ?></th>
                              <th align="left"><?php echo _l('change_order_amount'); ?></th>
                              <th align="left"><?php echo _l('anticipate_variation'); ?></th>
                              <th align="left">Total Certified Amount By BIL</th>
                              <th align="left"><?php echo _l('project'); ?></th>
                              <th align="left"><?php echo _l('category'); ?></th>
                              <th align="left"><?php echo _l('group_pur'); ?></th>
                              <th align="left"><?php echo _l('remarks'); ?></th>
                              <th align="center"><i class="fa fa-cog"></i></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php echo  $order_tracker_row_template; ?>
                        </tbody>
                     </table>
                     <button type="submit" class="btn btn-info pull-right"><?php echo _l('Save'); ?></button>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="viewOrderAttachmentModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('attachment'); ?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <div class="view_order_attachment_modal">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="order_tracker_file_data"></div>

<?php init_tail(); ?>
<?php require 'modules/purchase/assets/js/import_excel_items_order_tracker_js.php'; ?>
<?php require 'modules/purchase/assets/js/order_tracker_js.php'; ?>
<script src="<?php echo module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/charts/chart.js'); ?>?v=<?php echo PURCHASE_REVISION; ?>"></script>
</body>

</html>