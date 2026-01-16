<?php

defined('BASEPATH') or exit('No direct script access allowed');

$module_name = 'order_tracker';
$type_filter_name = 'order_tracker_type';
$rli_filter_name = 'rli_filter';
$vendors_filter_name = 'vendors';
$kind_filter_name = 'order_tracker_kind';
$budget_head_filter_name = 'budget_head';
$order_type_filter_name = 'order_type_filter';
$project_filter_name = 'projects';
$aw_unw_order_status_filter_name = 'aw_unw_order_status';
$yield_filter_name = 'yield';
$is_change_order_filter_name = 'is_change_order';

// Define common columns for both tables
$aColumns = [
   'aw_unw_order_status',
   'order_name', // Will represent 'pur_order_name' or 'wo_order_name'
   'vendor',
   'order_date',
   'completion_date',
   'budget',
   // 'order_value',
   'total',
   'co_total',
   'total_rev_contract_value',
   'anticipate_variation',
   'cost_to_complete',
   'vendor_submitted_amount_without_tax',
   'ril_certified_amount',
   'yield',
   'yield_delta',
   1,
   2,
   'project',
   'rli_filter',
   'kind',
   'group_name',
   'remarks',
   'last_action',
];

$sIndexColumn = 'id';

// Use a derived table to union both tables
$sTable = "(
    SELECT 
        " . db_prefix() . "pur_orders.id,
        " . db_prefix() . "pur_orders.pur_order_name as order_name,
        " . db_prefix() . "pur_orders.vendor,
        " . db_prefix() . "pur_orders.order_date,
        " . db_prefix() . "pur_orders.total,
        " . db_prefix() . "pur_orders.group_name
    FROM " . db_prefix() . "pur_orders
    UNION ALL
    SELECT 
        " . db_prefix() . "wo_orders.id,
        " . db_prefix() . "wo_orders.wo_order_name as order_name,
        " . db_prefix() . "wo_orders.vendor,
        " . db_prefix() . "wo_orders.order_date,
        " . db_prefix() . "wo_orders.total,
        " . db_prefix() . "wo_orders.group_name
    FROM " . db_prefix() . "wo_orders
) as combined_orders";

$join = [
   'LEFT JOIN ' . db_prefix() . 'assets_group ON ' . db_prefix() . 'assets_group.group_id = combined_orders.group_pur',
];

$where = [];

$type = $this->ci->input->post('type');
if (isset($type)) {
   $where_type = '';
   foreach ($type as $t) {
      if ($t != '') {
         if ($where_type == '') {
            $where_type .= ' AND (source_table  = "' . $t . '"';
         } else {
            $where_type .= ' or source_table  = "' . $t . '"';
         }
      }
   }
   if ($where_type != '') {
      $where_type .= ')';
      array_push($where, $where_type);
   }
}

$orderType = $this->ci->input->post('order_type_filter');
if (isset($orderType)) {
   $where_order_type = '';
   if ($orderType == 'created') {
      if ($where_order_type == '') {
         $where_order_type .= ' AND (source_table  = "order_tracker"';
      }
   }
   if ($orderType == 'fetched') {
      if ($where_order_type == '') {
         $where_order_type .= ' AND (source_table  = "pur_orders"';
         $where_order_type .= ' or source_table = "wo_orders"';
      }
   }
   if ($where_order_type != '') {
      $where_order_type .= ')';
      array_push($where, $where_order_type);
   }
}

$vendors = $this->ci->input->post('vendors');
if (isset($vendors)) {
   $where_vendors = '';
   foreach ($vendors as $t) {
      if ($t != '') {
         if ($where_vendors == '') {
            $where_vendors .= ' AND (vendor_id = "' . $t . '"';
         } else {
            $where_vendors .= ' or vendor_id = "' . $t . '"';
         }
      }
   }
   if ($where_vendors != '') {
      $where_vendors .= ')';
      array_push($where, $where_vendors);
   }
}

$budget_head = $this->ci->input->post('budget_head');
if (isset($budget_head)) {
   $where_budget_head = '';
   if ($budget_head != '') {
      if ($where_budget_head == '') {
         $where_budget_head .= ' AND (group_pur = "' . $budget_head . '"';
      } else {
         $where_budget_head .= ' or group_pur = "' . $budget_head . '"';
      }
   }
   if ($where_budget_head != '') {
      $where_budget_head .= ')';
      array_push($where, $where_budget_head);
   }
}

$budget_head = $this->ci->input->post('budget_head');
if (isset($budget_head)) {
   $where_budget_head = '';
   if ($budget_head != '') {
      if ($where_budget_head == '') {
         $where_budget_head .= ' AND (group_pur = "' . $budget_head . '"';
      } else {
         $where_budget_head .= ' or group_pur = "' . $budget_head . '"';
      }
   }
   if ($where_budget_head != '') {
      $where_budget_head .= ')';
      array_push($where, $where_budget_head);
   }
}

$rli_filter = $this->ci->input->post('rli_filter');
if (isset($rli_filter)) {
   $where_rli_filter = '';
   if ($rli_filter != '') {
      if ($where_rli_filter == '') {
         $where_rli_filter .= ' AND (rli_filter = "' . $rli_filter . '"';
      } else {
         $where_rli_filter .= ' or rli_filter = "' . $rli_filter . '"';
      }
   }
   if ($where_rli_filter != '') {
      $where_rli_filter .= ')';
      array_push($where, $where_rli_filter);
   }
}

$kind = $this->ci->input->post('kind');
if (isset($kind)) {
   $where_kind = '';
   if ($kind != '') {
      if ($where_kind == '') {
         $where_kind .= ' AND (kind = "' . $kind . '"';
      } else {
         $where_kind .= ' or kind = "' . $kind . '"';
      }
   }
   if ($where_kind != '') {
      $where_kind .= ')';
      array_push($where, $where_kind);
   }
}

$project = $this->ci->input->post('projects');
if (isset($project)) {
   $where_project = '';
   foreach ($project as $t) {
      if ($t != '') {
         if ($where_project == '') {
            $where_project .= ' AND (project_id = "' . $t . '"';
         } else {
            $where_project .= ' or project_id = "' . $t . '"';
         }
      }
   }
   if ($where_project != '') {
      $where_project .= ')';
      array_push($where, $where_project);
   }
}


$aw_unw_order_status = $this->ci->input->post('aw_unw_order_status');
if (isset($aw_unw_order_status)) {
   $where_aw_unw_order_status = '';
   foreach ($aw_unw_order_status as $t) {
      if ($t != '') {
         if ($where_aw_unw_order_status == '') {
            $where_aw_unw_order_status .= ' AND (aw_unw_order_status = "' . $t . '"';
         } else {
            $where_aw_unw_order_status .= ' or aw_unw_order_status = "' . $t . '"';
         }
      }
   }
   if ($where_aw_unw_order_status != '') {
      $where_aw_unw_order_status .= ')';
      array_push($where, $where_aw_unw_order_status);
   }
}

$yield = $this->ci->input->post('yield');
if (isset($yield)) {
   $where_yield = '';
   foreach ($yield as $t) {
      if ($t != '') {
         if ($where_yield == '') {
            $where_yield .= ' AND (yield = "' . $t . '"';
         } else {
            $where_yield .= ' or yield = "' . $t . '"';
         }
      }
   }
   if ($where_yield != '') {
      $where_yield .= ')';
      array_push($where, $where_yield);
   }
}

if ($this->ci->input->post('is_change_order') && $this->ci->input->post('is_change_order') != '') {
   $is_change_order = $this->ci->input->post('is_change_order');
   if($is_change_order == 1) {
      array_push($where, 'AND (co_total > 0)');
   } elseif ($is_change_order == 2) {
      array_push($where, 'AND (co_total <= 0)');
   }
}

$having = '';

$type_filter_value = !empty($this->ci->input->post('type')) ? implode(',', $this->ci->input->post('type')) : NULL;
update_module_filter($module_name, $type_filter_name, $type_filter_value);

$vendors_filter_value = !empty($this->ci->input->post('vendors')) ? implode(',', $this->ci->input->post('vendors')) : NULL;
update_module_filter($module_name, $vendors_filter_name, $vendors_filter_value);

$rli_filter_value = !empty($this->ci->input->post('rli_filter')) ? $this->ci->input->post('rli_filter') : NULL;
update_module_filter($module_name, $rli_filter_name, $rli_filter_value);

$kind_filter_value = !empty($this->ci->input->post('kind')) ? $this->ci->input->post('kind') : NULL;
update_module_filter($module_name, $kind_filter_name, $kind_filter_value);

$budget_head_filter_name_value = !empty($this->ci->input->post('budget_head')) ? $this->ci->input->post('budget_head') : NULL;
update_module_filter($module_name, $budget_head_filter_name, $budget_head_filter_name_value);

$order_type_filter_name_value = !empty($this->ci->input->post('order_type_filter')) ? $this->ci->input->post('order_type_filter') : NULL;
update_module_filter($module_name, $order_type_filter_name, $order_type_filter_name_value);

$projects_filter_value = !empty($this->ci->input->post('projects')) ? implode(',', $this->ci->input->post('projects')) : NULL;
update_module_filter($module_name, $project_filter_name, $projects_filter_value);

$aw_unw_order_status_filter_value = !empty($this->ci->input->post('aw_unw_order_status')) ? implode(',', $this->ci->input->post('aw_unw_order_status')) : NULL;
update_module_filter($module_name, $aw_unw_order_status_filter_name, $aw_unw_order_status_filter_value);

$yield_filter_value = !empty($this->ci->input->post('yield')) ? implode(',', $this->ci->input->post('yield')) : NULL;
update_module_filter($module_name, $yield_filter_name, $yield_filter_value);

$is_change_order_filter_name_value = !empty($this->ci->input->post('is_change_order')) ? $this->ci->input->post('is_change_order') : NULL;
update_module_filter($module_name, $is_change_order_filter_name, $is_change_order_filter_name_value);

// Query and process data
$result = data_tables_init_union($aColumns, $sIndexColumn, $sTable, $join, $where, [
   'combined_orders.id as id',
   'rli_filter',
   'vendor',
   'vendor_id',
   'order_date',
   'completion_date',
   'budget',
   // 'order_value',
   'co_total',
   'total',
   'total_rev_contract_value',
   'anticipate_variation',
   'cost_to_complete',
   'vendor_submitted_amount_without_tax',
   'kind',
   'group_name',
   'remarks',
   'group_pur',
   'source_table',
   'order_number',
   'subtotal',
   'project',

]);

$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
   'total_budget_ro_projection' => 0,
   'total_order_value' => 0,
   'total_committed_contract_amount' => 0,
   'total_change_order_amount' => 0,
   'total_rev_contract_value' => 0,
   'total_anticipate_variation' => 0,
   'total_cost_to_complete' => 0,
   'total_final_certified_amount' => 0,
   'total_ril_certified_amount' => 0,
];
$this->ci->load->model('purchase/purchase_model');
$vendor_list  = $this->ci->purchase_model->get_vendor();
$vendor_by_id       = array_column($vendor_list,  null, 'userid');
$base_currency = get_base_currency_pur();

$sr = 1;
foreach ($rResult as $aRow) {
   $row = [];
   foreach ($aColumns as $column) {
      $_data = isset($aRow[$column]) ? $aRow[$column] : '';

      // Process specific columns
      if ($column == 'total') {
         if ($aRow['source_table']  == "order_tracker") {
            $_data = app_format_money($aRow['total'], $base_currency->symbol);

            // Check if total exists in the database
            if (!empty($aRow['total'])) {
               // Display as plain text
               $_data = '<span class="contract-amount-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
                  app_format_money($aRow['total'], '₹') .
                  '</span>';
            } else {
               // Render as an editable input if no total exists
               $_data = '<input type="number" class="form-control contract-amount-input" 
                         placeholder="Enter Conatact Amount" 
                         data-id="' . $aRow['id'] . '" 
                         data-type="' . $aRow['source_table'] . '">';
            }
         } else {
            $_data = app_format_money($aRow['subtotal'], $base_currency->symbol);
         }
      } elseif ($column == 'order_name') {
         if ($aRow['source_table'] == "pur_orders") {
            $_data = '<a href="' . admin_url('purchase/pur_order/' . $aRow['id']) . '" target="_blank">' . $aRow['order_number'] . '-' . $aRow['order_name'] . '</a>';
         } elseif ($aRow['source_table'] == "wo_orders") {
            $_data = '<a href="' . admin_url('purchase/wo_order/' . $aRow['id']) . '" target="_blank">' . $aRow['order_number'] . '-' . $aRow['order_name'] . '</a>';
         } elseif ($aRow['source_table'] == "order_tracker") {
            $name = $aRow['order_name'];
            $name .= '<div class="row-options">';
            if ((has_permission('purchase-order', '', 'delete') || is_admin()) && ($aRow['source_table'] == "order_tracker")) {
               $name .= '<a href="' . admin_url('purchase/delete_order_tracker/' . $aRow['id']) . '" class="text-danger _delete" >' . _l('delete') . '</a>';
            }
            $name .= '</div>';
            $_data = $name;
         }
      } elseif ($column == 'vendor') {

         if ($aRow['source_table'] == "order_tracker") {
            $vendor_raw = trim($aRow['vendor_id']);
            if ($vendor_raw !== '') {
               // Vendor is already selected
               $name = '';
               if (isset($vendor_by_id[$vendor_raw])) {
                  $u = $vendor_by_id[$vendor_raw];
                  $name = $u['company'];
               }
               $_data = '<span class="vendor-display" 
                           data-id="' . $aRow['id'] . '" 
                           data-vendor="' . html_escape($vendor_raw) . '">'
                  . html_escape($name) .
                  '</span>';
            } else {
               // No vendor selected - show dropdown
               $_data = '<select class="form-control vendor-input selectpicker" 
                           data-live-search="true" 
                           data-width="100%" 
                           data-id="' . $aRow['id'] . '">
                           <option value="">' . _l('') . '</option>';

               foreach ($vendor_by_id as $vendor) {
                  $_data .= '<option value="' . $vendor['userid'] . '">'
                     . html_escape($vendor['company'])
                     . '</option>';
               }

               $_data .= '</select>';

               // Initialize selectpicker if it exists
               $_data .= '<script>
                           if($.fn.selectpicker) {
                              $(".vendor-input").selectpicker();
                           }
                        </script>';
            }
         } else {
            $_data = $aRow['vendor'];
         }
      } elseif ($column == 'order_date') {

         if ($aRow['source_table'] == "order_tracker") {
            // Inline editable input for Order Date
            $_data = '<input type="date" class="form-control order-date-input" 
                        value="' . $aRow['order_date'] . '" 
                        data-id="' . $aRow['id'] . '" 
                        data-type="' . $aRow['source_table'] . '">';
         } else {
            $_data = _d($aRow['order_date']);
         }
      } elseif ($column == 'completion_date') {
         // Inline editable input for Completion Date
         $_data = '<input type="date" class="form-control completion-date-input" 
                        value="' . $aRow['completion_date'] . '" 
                        data-id="' . $aRow['id'] . '" 
                        data-type="' . $aRow['source_table'] . '">';
      } elseif ($column == 'rli_filter') {
         // Define an array of statuses with their corresponding labels and table attributes
         $status_labels = [
            1 => ['label' => 'success', 'table' => 'new_item_service_been_addded_as_per_instruction', 'text' => _l('new_item_service_been_addded_as_per_instruction')],
            2 => ['label' => 'info', 'table' => 'due_to_spec_change_then_original_cost', 'text' => _l('due_to_spec_change_then_original_cost')],
            3 => ['label' => 'warning', 'table' => 'deal_slip', 'text' => _l('deal_slip')],
            4 => ['label' => 'primary', 'table' => 'to_be_provided_by_ril_but_managed_by_bil', 'text' => _l('to_be_provided_by_ril_but_managed_by_bil')],
            5 => ['label' => 'secondary', 'table' => 'due_to_additional_item_as_per_apex_instrution', 'text' => _l('due_to_additional_item_as_per_apex_instrution')],
            6 => ['label' => 'purple', 'table' => 'event_expense', 'text' => _l('event_expense')],
            7 => ['label' => 'teal', 'table' => 'pending_procurements', 'text' => _l('pending_procurements')],
            8 => ['label' => 'orange', 'table' => 'common_services_in_ghj_scope', 'text' => _l('common_services_in_ghj_scope')],
            9 => ['label' => 'green', 'table' => 'common_services_in_ril_scope', 'text' => _l('common_services_in_ril_scope')],
            10 => ['label' => 'default', 'table' => 'due_to_site_specfic_constraint', 'text' => _l('due_to_site_specfic_constraint')],
            11 => ['label' => 'danger', 'table' => 'provided_by_ril', 'text' => _l('provided_by_ril')],
         ];
         // Start generating the HTML
         $rli_filter = '';

         if (isset($status_labels[$aRow['rli_filter']])) {
            $status = $status_labels[$aRow['rli_filter']];
            $rli_filter = '<span class="inline-block label label-' . $status['label'] . '" id="status_span_' . $aRow['id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];
         } else {
            $rli_filter = '<span class="inline-block label " id="status_span_' . $aRow['id'] . '" >';
         }

         if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $rli_filter .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $rli_filter .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $rli_filter .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $rli_filter .= '</a>';

            $rli_filter .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

            foreach ($status_labels as $key => $status) {
               if ($key != $aRow['rli_filter']) {
                  $rli_filter .= '<li>
                       <a href="#" onclick="change_rli_filter(' . $key . ', ' . $aRow['id'] . ', \'' . htmlspecialchars($aRow['source_table'], ENT_QUOTES) . '\'); return false;">
                           ' . $status['text'] . '
                       </a>
                   </li>';
               }
            }


            $rli_filter .= '</ul>';
            $rli_filter .= '</div>';
         }

         $rli_filter .= '</span>';
         $_data = $rli_filter;
      } elseif ($column == 'budget') {
         // Check if budget exists in the database
         if (!empty($aRow['budget'])) {
            // Display as plain text
            $_data = '<span class="budget-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
               app_format_money($aRow['budget'], '₹') .
               '</span>';
         } else {
            // Render as an editable input if no budget exists
            $_data = '<input type="number" class="form-control budget-input" 
                         placeholder="Enter budget" 
                         data-id="' . $aRow['id'] . '" 
                         data-type="' . $aRow['source_table'] . '">';
         }
      } elseif ($column == 'co_total') {
         // $_data = app_format_money($aRow['co_total'], $base_currency->symbol);

         // Check if anticipate_variation exists in the database
         if (!empty($aRow['co_total'])) {
            if ($aRow['source_table'] == "order_tracker") {
               if (!empty($aRow['co_total'])) {
                  // Display as plain text
                  $_data = '<span class="co-total-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
                     app_format_money($aRow['co_total'], '₹') .
                     '</span>';
               } else {
                  $_data = '<input type="number" class="form-control co-total-input"
                           placeholder="Enter Change Order"
                           data-id="' . $aRow['id'] . '"
                           data-type="' . $aRow['source_table'] . '">';
               }
            } else {
               // Display as plain text
               $_data = '<span class="" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
                  app_format_money($aRow['co_total'], '₹') .
                  '</span>';
            }
         } else {
            // Render as an editable input if no value exists
            // $_data = '<input type="number" class="form-control co-total-input"
            //          placeholder="Enter Change Order"
            //          data-id="' . $aRow['id'] . '"
            //          data-type="' . $aRow['source_table'] . '">';
            $_data = '<span style="font-style: italic;font-size: 12px;">Values will be fetched directly from the change order module</span>';
         }
      } elseif ($column == 'total_rev_contract_value') {
         $_data = app_format_money($aRow['total_rev_contract_value'], $base_currency->symbol);
      } elseif ($column == 'anticipate_variation') {
         // Check if anticipate_variation exists in the database
         if (!empty($aRow['anticipate_variation'])) {
            // Display as plain text
            $_data = '<span class="anticipate-variation-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
               app_format_money($aRow['anticipate_variation'], '₹') .
               '</span>';
         } else {
            // Render as an editable input if no value exists
            $_data = '<input type="number" class="form-control anticipate-variation-input" 
                     placeholder="Enter variation" 
                     data-id="' . $aRow['id'] . '" 
                     data-type="' . $aRow['source_table'] . '">';
         }
      } elseif ($column == 'cost_to_complete') {
         $_data = app_format_money($aRow['cost_to_complete'], $base_currency->symbol);
      } elseif ($column == 'vendor_submitted_amount_without_tax') {
         if (!empty($aRow['vendor_submitted_amount_without_tax']) && $aRow['vendor_submitted_amount_without_tax'] != 0) {

            $_data = '<span class=  data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
               app_format_money($aRow['vendor_submitted_amount_without_tax'], '₹') .
               '</span>';
         } else {
            // Render as an editable input if no value exists
            $_data = '<span style="font-style: italic;font-size: 12px;">Values will be fetched directly from the vendor billing tracker</span>';
         }
      } elseif ($column == 'yield') {
         if($aRow['yield'] == 1) {
            $_data = '<span class="label label-default">None</span>';
         } else if($aRow['yield'] == 2) {
            $_data = '<span class="label label-success">Profit</span>';
         } else if($aRow['yield'] == 3) {
            $_data = '<span class="label label-danger">Loss</span>';
         } else if($aRow['yield'] == 4) {
            $_data = '<span class="label label-warning">Neutral</span>';
         } else {
            $_data = '';
         }
      } elseif ($column == 'yield_delta') {
         $_data = app_format_money($aRow['yield_delta'], $base_currency->symbol);
      } elseif ($column == 'ril_certified_amount') {
         $_data = '<span class=  data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' . app_format_money($aRow['ril_certified_amount'], '₹') . '</span>';
      } elseif ($column == 1) {
         $_data = '
            <div class="input-group" style="width: 100%;">
               <input type="file" 
                      name="attachments[]" 
                      class="form-control upload_order_tracker_files" 
                      data-id="' . $aRow['id'] . '" 
                      data-source="' . $aRow['source_table'] . '" 
                      multiple 
                      style="min-width: 200px; width: 100%;">
               <span class="input-group-btn">
                  <button type="button" 
                          class="btn btn-success upload_order_tracker_attachments" 
                          data-id="' . $aRow['id'] . '" 
                          data-source="' . $aRow['source_table'] . '" 
                          title="Upload Attachments">
                     <i class="fa fa-upload"></i>
                  </button>
               </span>
            </div>
         ';
      } elseif ($column == 2) {
         $this->ci->load->model('purchase/purchase_model');
         $attachments = $this->ci->purchase_model->get_order_tracker_attachments($aRow['id'], $aRow['source_table']);
         $file_html = '';
         if (!empty($attachments)) {
            $file_html = '<a href="javascript:void(0)" onclick="view_order_tracker_attachments(' . $aRow['id'] . ', \'' . $aRow['source_table'] . '\'); return false;" class="btn btn-info btn-icon">View Files</a>';
         }
         $_data = $file_html;
      } elseif ($column == 'remarks') {
         // If remarks exist, display as plain text with an inline editing option
         $_data = '<span class="remarks-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
            htmlspecialchars($aRow['remarks']) .
            '</span>';
         // If empty, allow direct input
         if (empty($aRow['remarks'])) {
            $_data = '<textarea class="form-control remarks-input" placeholder="Enter remarks" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '"></textarea>';
         }
      }  elseif ($column == 'last_action') {
         $_data = get_last_action_full_name($aRow['last_action']);
      }
      //  elseif ($column == 'order_value') {
      //    $_data = '<span class="order-value-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' . app_format_money($aRow['order_value'], $base_currency->symbol) . '</span>';
      // }
      elseif ($column == 'aw_unw_order_status') {
         $status_labels_aw_uw = [
            1 => ['label' => 'success', 'table' => 'awarded', 'text' => _l('Awarded')],
            2 => ['label' => 'default', 'table' => 'unawarded', 'text' => _l('Unawarded')],
            3 => ['label' => 'warning', 'table' => 'awarded_by_ril', 'text' => _l('Awarded by RIL')],
         ];
         // Start generating the HTML
         $aw_uw = '';
         if (isset($status_labels_aw_uw[$aRow['aw_unw_order_status']])) {
            $status = $status_labels_aw_uw[$aRow['aw_unw_order_status']];
            $aw_uw = '<span class="inline-block label label-' . $status['label'] . '" id="status_aw_uw_span_' . $aRow['id'] . '" task-status-table="' . $status['table'] . '">' . $status['text'];
         } else {
            $aw_uw = '<span class="inline-block label " id="status_aw_uw_span_' . $aRow['id'] . '" >';
         }

         if (has_permission('order_tracker', '', 'edit') || is_admin()) {
            $aw_uw .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $aw_uw .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $aw_uw .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $aw_uw .= '</a>';

            $aw_uw .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

            foreach ($status_labels_aw_uw as $key => $status) {
               if ($key != $aRow['aw_unw_order_status']) {
                  $aw_uw .= '<li>
                       <a href="javascript:void(0);" onclick="change_aw_unw_order_status(' . $key . ', ' . $aRow['id'] . ', \'' . htmlspecialchars($aRow['source_table'], ENT_QUOTES) . '\'); return false;">
                           ' . $status['text'] . '
                       </a>
                   </li>';
               }
            }


            $aw_uw .= '</ul>';
            $aw_uw .= '</div>';
         }

         $aw_uw .= '</span>';
         $_data = $aw_uw;
      } elseif ($column == 'project') {
         $_data = $aRow['project'];
      } elseif ($column == 'group_name') {
         if ($aRow['source_table'] == "order_tracker") {
            // 1) Raw budget-head list
            $raw_heads = get_group_name_item(); // e.g. [ ['id'=>5,'name'=>'Foo'], … ]

            // 2) Your 11-item label palette
            $label_palette = [
               'danger',
               'success',
               'info',
               'warning',
               'primary',
               'secondary',
               'purple',
               'teal',
               'orange',
               'green',
               'default',
            ];

            // 3) Build status_labels_budget_head, cycling labels
            $status_labels_budget_head = [];
            $i = 0;
            foreach ($raw_heads as $h) {
               $label = $label_palette[$i % count($label_palette)];
               $status_labels_budget_head[$h['id']] = [
                  'label' => $label,
                  'name'  => $h['name'],
               ];
               $i++;
            }

            // 4) Render exactly like your aw_unw_order_status block but for group_pur
            $budget_head_html = '';
            if (isset($status_labels_budget_head[$aRow['group_pur']])) {
               $bh = $status_labels_budget_head[$aRow['group_pur']];
               $budget_head_html = '<span class="inline-block label label-' . $bh['label'] . '" '
                  . 'id="budget_head_span_' . $aRow['id'] . '">'
                  . $bh['name'];
            } else {
               $budget_head_html = '<span class="inline-block label " '
                  . 'id="budget_head_span_' . $aRow['id'] . '">';
            }

            if (has_permission('order_tracker', '', 'edit') || is_admin()) {
               $budget_head_html .= '<div class="dropdown inline-block mleft5 table-export-exclude">'
                  . '<a href="#" class="dropdown-toggle text-dark" '
                  .    'id="tableBudgetHead-' . $aRow['id'] . '" '
                  .    'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                  .    '<span data-toggle="tooltip" title="' . _l('change_budget_head') . '">'
                  .        '<i class="fa fa-caret-down"></i>'
                  .    '</span>'
                  . '</a>'
                  . '<ul class="dropdown-menu dropdown-menu-right" '
                  .     'aria-labelledby="tableBudgetHead-' . $aRow['id'] . '">';
               foreach ($status_labels_budget_head as $id => $bh) {
                  if ($id != $aRow['group_pur']) {
                     $budget_head_html .= '<li>'
                        .   '<a href="javascript:void(0);" '
                        .      'onclick="update_budget_head(' . $id . ', ' . $aRow['id']
                        .      ', \'' . htmlspecialchars($aRow['source_table'], ENT_QUOTES)
                        .      '\'); return false;">'
                        .          $bh['name']
                        .   '</a>'
                        . '</li>';
                  }
               }
               $budget_head_html .= '</ul>'
                  . '</div>';
            }

            $budget_head_html .= '</span>';
            $_data = $budget_head_html;
         } else {
            // For other source tables, just display the group name
            $_data = $aRow['group_name'];
         }
      }


      $row[] = $_data;
   }

   $footer_data['total_budget_ro_projection'] += $aRow['budget'];
   // $footer_data['total_order_value'] += $aRow['order_value'];
   if ($aRow['source_table'] === 'order_tracker') {
      $footer_data['total_committed_contract_amount'] += $aRow['total'];
   } else {
      $footer_data['total_committed_contract_amount'] += $aRow['subtotal'];
   }
   $footer_data['total_change_order_amount'] += $aRow['co_total'];
   $footer_data['total_rev_contract_value'] += $aRow['total_rev_contract_value'];
   $footer_data['total_anticipate_variation'] += $aRow['anticipate_variation'];
   $footer_data['total_cost_to_complete'] += $aRow['cost_to_complete'];
   $footer_data['total_final_certified_amount'] += $aRow['vendor_submitted_amount_without_tax'];
   $footer_data['total_ril_certified_amount'] += $aRow['ril_certified_amount'];
   $output['aaData'][] = $row;
   $sr++;
}

foreach ($footer_data as $key => $total) {
   $footer_data[$key] = app_format_money($total, $base_currency->symbol);
}
$output['sums'] = $footer_data;
