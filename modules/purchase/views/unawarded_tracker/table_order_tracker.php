<?php

defined('BASEPATH') or exit('No direct script access allowed');

$module_name = 'unawareded_tracker';
$budget_head_filter_name = 'budget_head';
$project_filter_name = 'projects';

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
$sTable = "";

$join = [
   'LEFT JOIN ' . db_prefix() . 'assets_group ON ' . db_prefix() . 'assets_group.group_id = combined_orders.budget_head',
];

$where = [];




$budget_head = $this->ci->input->post('budget_head');
if (isset($budget_head)) {
   $where_budget_head = '';
   if ($budget_head != '') {
      if ($where_budget_head == '') {
         $where_budget_head .= ' AND (budget_head = "' . $budget_head . '"';
      } else {
         $where_budget_head .= ' or budget_head = "' . $budget_head . '"';
      }
   }
   if ($where_budget_head != '') {
      $where_budget_head .= ')';
      array_push($where, $where_budget_head);
   }
}



$project = $this->ci->input->post('projects');
if (isset($project)) {
   $where_project = '';
   foreach ($project as $t) {
      if ($t != '') {
         // if ($where_project == '') {
         //    $where_project .= ' AND (project = "' . $t . '"';
         // } else {
         //    $where_project .= ' or project = "' . $t . '"';
         // }
      }
   }
   if ($where_project != '') {
      $where_project .= ')';
      array_push($where, $where_project);
   }
}


$having = '';




$budget_head_filter_name_value = !empty($this->ci->input->post('budget_head')) ? $this->ci->input->post('budget_head') : NULL;
update_module_filter($module_name, $budget_head_filter_name, $budget_head_filter_name_value);


$projects_filter_value = !empty($this->ci->input->post('projects')) ? implode(',', $this->ci->input->post('projects')) : NULL;
update_module_filter($module_name, $project_filter_name, $projects_filter_value);



// Query and process data
$result = data_tables_init_union_unawarded($aColumns, $sIndexColumn, $sTable, $join, $where, [
   'combined_orders.id as id',
   'estimate',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
// echo '<pre>';
// print_r($rResult);
// die;

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
         $_data = get_group_name_item($aRow['budget_head'])->name;
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
         // assuming $aRow['estimate'] and $aRow['budget_head'] are both integers:
         $_data = '<button
    type="button"
    class="btn btn-info"
    onclick="cost_control_sheet_for_unawarded_tracker('
            . intval($aRow['estimate']) . ', '
            . intval($aRow['budget_head']) . ', '
            . '\'\''
            . ')"
    id="cost_control_sheet"
>Cost Control Sheet</button>';
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

   $output['aaData'][] = $row;
   $sr++;
}
