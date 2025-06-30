<?php

defined('BASEPATH') or exit('No direct script access allowed');

$module_name = 'unawareded_tracker';
$budget_head_filter_name = 'budget_head';
$project_filter_name = 'projects';

$aColumns = [
   'project',
   'package_name',
   1,
   'budget_head_name',
   'kind',
   'rli_filter',
   'total_package',
   'awarded_value',
   'sdeposit_value',
   'pending_value_in_package',
   2,
   'percentage_of_capex_used',
   3,
];

if($estimate_id != 0) {
   $aColumns = [
      'package_name',
      1,
      'budget_head_name',
      'kind',
      'rli_filter',
      'total_package',
      'awarded_value',
      'sdeposit_value',
      'pending_value_in_package',
      2,
      'percentage_of_capex_used',
      3,
   ];
}

$sIndexColumn = 'id';
$sTable = "";
$where = [];
$join = [];

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
         $where_project .= ' AND (project_id = "' . $t . '"';
      }
   }
   if ($where_project != '') {
      $where_project .= ')';
      array_push($where, $where_project);
   }
}

if($estimate_id != 0) {
   array_push($where, ' AND (estimate_id = "' . $estimate_id . '")');
}

$having = '';

$budget_head_filter_name_value = !empty($this->ci->input->post('budget_head')) ? $this->ci->input->post('budget_head') : NULL;
update_module_filter($module_name, $budget_head_filter_name, $budget_head_filter_name_value);

$projects_filter_value = !empty($this->ci->input->post('projects')) ? implode(',', $this->ci->input->post('projects')) : NULL;
update_module_filter($module_name, $project_filter_name, $projects_filter_value);

// Query and process data
$result = data_tables_init_union_unawarded($aColumns, $sIndexColumn, $sTable, $join, $where, [
   'id',
   'estimate_id',
   'budget_head',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

$sr = 1;
foreach ($rResult as $aRow) {
   $row = [];
   foreach ($aColumns as $column) {
      $_data = isset($aRow[$column]) ? $aRow[$column] : '';
      $base_currency = get_base_currency_pur();
      // Process specific columns
      if ($column == 'project') {
         $_data = $aRow['project'];
      } elseif ($column == 'package_name') {
         $package_info = '';
         $package_info .= '<p>'.$aRow['package_name'].'</p>';
         $package_info .= '<div class="row-options">';
         $package_info .= '<a href="' . admin_url('estimates/delete_package/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
         $package_info .= '</div>';
         $_data = $package_info;
      } elseif ($column == 'budget_head_name') {
         $_data = $aRow['budget_head_name'];
      } elseif ($column == 'total_package') {
         $_data = app_format_money($aRow['total_package'], $base_currency->symbol);
      } elseif ($column == 'sdeposit_value') {
         $_data = app_format_money($aRow['sdeposit_value'], $base_currency->symbol);
      } elseif ($column == 'awarded_value') {
         $_data = app_format_money($aRow['awarded_value'], $base_currency->symbol);
      } elseif ($column == 'pending_value_in_package') {
         $_data = app_format_money($aRow['pending_value_in_package'], $base_currency->symbol);
      } elseif ($column == 'percentage_of_capex_used') {
         $_data = round($aRow['percentage_of_capex_used']).'%';
      } elseif ($column == 'kind') {
         $_data = $aRow['kind'];
      } elseif ($column == 'rli_filter') {
         $rli_filter_text = '';
         $rli_filter_key = $aRow['rli_filter'];
         $status_labels = [
            0 => ['label' => 'danger', 'table' => 'provided_by_ril', 'text' => _l('provided_by_ril')],
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
         ];
         if (isset($status_labels[$rli_filter_key])) {
            $rli_filter_text = $status_labels[$rli_filter_key]['text'];
         }
         $_data = $rli_filter_text;
      } elseif ($column == 1) {
         $_data = '';
         $preview = '<a href="javascript:void(0);" onclick="get_package_info(\'' . $aRow['id'] . '\', \'' . $aRow['estimate_id'] . '\', \'' . $aRow['budget_head'] . '\'); return false;" class="btn btn-info btn-sm">'
         . _l('Preview') . '</a>';
         $_data = $preview;
      } elseif ($column == 2) {
         $_data = '';
         if($aRow['awarded_value'] == 0) {
            $_data = '<span class="inline-block label label-danger">Not Booked</span>';
         } elseif ($aRow['pending_value_in_package'] == 0) {
            $_data = '<span class="inline-block label label-success">Fully Booked</span>';
         } elseif ($aRow['pending_value_in_package'] > 0) {
            $_data = '<span class="inline-block label label-warning">Partially Booked</span>';
         } else {
            $_data = '';
         }
      } elseif ($column == 3) {
         $_data = '<div class="btn-group mright5">
            <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
               Book Order <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
               <li class="hidden-xs"><a href="' . admin_url('purchase/pur_order?package='.$aRow['id'].'') . '" target="_blank">Purchase Order</a></li>
               <li class="hidden-xs"><a href="' . admin_url('purchase/wo_order') . '" target="_blank">Work Order</a></li>
               <li class="hidden-xs"><a href="' . admin_url('changee/pur_order') . '" target="_blank">Change Order</a></li>
            </ul>
         </div>';
      } else {
         $_data = '';
      }

      $row[] = $_data;
   }
   $output['aaData'][] = $row;
   $sr++;
}

?>
