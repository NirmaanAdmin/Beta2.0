<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
   'order_number',
   'description',
   'budget_head_name',
   'vendor_name',
   'amount',
];

$sIndexColumn = 'id';
$sTable = "";
$where = [];
$join = [];

$budget_head = $this->ci->input->post('budget_head');
if (isset($budget_head)) {
   $where_budget_head = '';
   if ($budget_head != '') {
      if ($where_budget_head == '') {
         $where_budget_head .= 'AND (budget_head = "' . $budget_head . '"';
      } else {
         $where_budget_head .= 'or budget_head = "' . $budget_head . '"';
      }
   }
   if ($where_budget_head != '') {
      $where_budget_head .= ')';
      array_push($where, $where_budget_head);
   }
}

if($estimate_id != 0) {
   array_push($where, 'AND (estimate = "' . $estimate_id . '")');
}

$having = '';

// Query and process data
$result = data_tables_budget_usage_init($aColumns, $join, $where, [
   'id',
   'type',
   'vendor_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

$footer_data = [
   'usage_budgeted_amount' => 0,
   'usage_order_amount' => 0,
   'usage_remaining_amount' => 0,
];
$base_currency = get_base_currency_pur();

$sr = 1;
foreach ($rResult as $aRow) {
   $row = [];
   foreach ($aColumns as $column) {
      $_data = isset($aRow[$column]) ? $aRow[$column] : '';
      // Process specific columns
      if ($column == 'order_number') {
         if($aRow['type'] == 'po') {
            $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
         } else if($aRow['type'] == 'wo') {
            $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow['id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
         } else if($aRow['type'] == 'co') {
            $_data = '<a href="' . admin_url('changee/changee_order/' . $aRow['id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
         } else {
            $_data = "";
         }
      } elseif ($column == 'description') {
         $_data = $aRow['description'];
      } elseif ($column == 'budget_head_name') {
         $_data = $aRow['budget_head_name'];
      } elseif ($column == 'vendor_name') {
         $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' .  $aRow['vendor_name'] . '</a>';
      } elseif ($column == 'amount') {
         $_data = app_format_money($aRow['amount'], $base_currency->symbol);
      } else {
         $_data = '';
      }

      $row[] = $_data;
   }

   $footer_data['usage_order_amount'] += $aRow['amount'];

   $output['aaData'][] = $row;
   $sr++;
}

$footer_data['usage_budgeted_amount'] = get_total_budgeted_amount($estimate_id, $budget_head);
$footer_data['usage_remaining_amount'] = $footer_data['usage_budgeted_amount'] - $footer_data['usage_order_amount'];

foreach ($footer_data as $key => $total) {
   $footer_data[$key] = app_format_money($total, $base_currency->symbol);
}
$output['sums'] = $footer_data;

?>
