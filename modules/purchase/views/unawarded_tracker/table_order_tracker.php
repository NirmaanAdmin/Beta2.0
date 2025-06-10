<?php

defined('BASEPATH') or exit('No direct script access allowed');

$module_name = 'unawareded_tracker';
$type_filter_name = 'order_tracker_type';
$rli_filter_name = 'rli_filter';
$vendors_filter_name = 'vendors';
$kind_filter_name = 'order_tracker_kind';
$budget_head_filter_name = 'budget_head';
$order_type_filter_name = 'order_type_filter';
$project_filter_name = 'projects';
$aw_unw_order_status_filter_name = 'aw_unw_order_status';

// Define common columns for both tables
$aColumns = [
   'budget_head', // Will represent 'pur_order_name' or 'wo_order_name'
   'project',
   'awarded_value',
   'unawarded_value',
   'unallocated_value',
   'secured_desposit',
   'cost_to_complete',
   'budget_health',
   'entity_table',
   'remarks',
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
   'LEFT JOIN ' . db_prefix() . 'assets_group ON ' . db_prefix() . 'assets_group.group_id = combined_orders.budget_head',
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
            $where_project .= ' AND (project = "' . $t . '"';
         } else {
            $where_project .= ' or project = "' . $t . '"';
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

// Query and process data
$result = data_tables_init_union_unawarded($aColumns, $sIndexColumn, $sTable, $join, $where, [
   'combined_orders.id as id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
   
];
$this->ci->load->model('purchase/purchase_model');
$vendor_list  = $this->ci->purchase_model->get_vendor();
$vendor_by_id       = array_column($vendor_list,  null, 'userid');

$sr = 1;
foreach ($rResult as $aRow) {
   $row = [];
   foreach ($aColumns as $column) {
      $_data = isset($aRow[$column]) ? $aRow[$column] : '';

      // Process specific columns
      if ($column == 'budget_head') {
         // 1) Raw budget-head list
         $raw_heads = get_group_name_item(); // e.g. [ ['id'=>5,'name'=>'Foo'], … ]


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

         $_data = $status_labels_budget_head[$aRow['budget_head']];;
      } elseif ($column == 'project') {
         $_data = $aRow['project'];
      } elseif ($column == 'awarded_value') {
         $base_currency = get_base_currency_pur();
         $_data = app_format_money($aRow['awarded_value'], $base_currency->symbol);
      } elseif ($column == 'unawarded_value') {
         $base_currency = get_base_currency_pur();
         $_data = app_format_money($aRow['unawarded_value'], $base_currency->symbol);
      } elseif ($column == 'unallocated_value') {
         $base_currency = get_base_currency_pur();
         $_data = app_format_money($aRow['unallocated_value'], $base_currency->symbol);
      } elseif ($column == 'secured_desposit') {
         $base_currency = get_base_currency_pur();
         $_data = app_format_money($aRow['cost_to_complete'], $base_currency->symbol);
      } elseif ($column == 'budget_health') {
         $_data = $aRow['budget_health'];
      } elseif ($column == 'entity_table') {
         echo '<button type="button" class="btn btn-info pull-right" onclick="cost_control_sheet(4,2,"")" id="cost_control_sheet">Cost Control Sheet</button>';
      } elseif ($column == 'remarks') {
         // If remarks exist, display as plain text with an inline editing option
         $_data = '<span class="remarks-display" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '">' .
            htmlspecialchars($aRow['remarks']) .
            '</span>';
         // If empty, allow direct input
         if (empty($aRow['remarks'])) {
            $_data = '<textarea class="form-control remarks-input" placeholder="Enter remarks" data-id="' . $aRow['id'] . '" data-type="' . $aRow['source_table'] . '"></textarea>';
         }
      }

      $row[] = $_data;
   }

   // $footer_data['total_budget_ro_projection'] += [];
   $output['aaData'][] = $row;
   $sr++;
}

// foreach ($footer_data as $key => $total) {
//    $footer_data[$key] = app_format_money($total, $base_currency->symbol);
// }
// $output['sums'] = $footer_data;
