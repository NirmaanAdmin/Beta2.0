<?php
defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency_pur();

$select = [
    'id',
    'bill_number',
    'total',
    'invoice_date',
    1,
];

$join = [];

$where = [];
$po_id = isset($po_id) ? $po_id : null;
if ($po_id) {
    $where[] = 'AND pb.pur_order = ' . $po_id;
}

$additionalSelect = [];

$sIndexColumn = 'pb.id';
$sTable       = db_prefix() . 'pur_bills pb';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $key => $aRow) {
    $row = [];

    $row[] = $key + 1;
    $row[] = $aRow['bill_number'];
    $row[] = app_format_money($aRow['total'], $base_currency->symbol);
    $row[] = date('d-M-Y', strtotime($aRow['invoice_date']));
    $actions = '';
    if (has_permission('purchase_invoices', '', 'edit') || is_admin()) {
        $actions .= '<a href="' . admin_url('purchase/edit_pur_bills/' . $aRow['id']) . '" 
            target="_blank" 
            class="btn btn-default btn-icon" 
            data-toggle="tooltip" 
            data-placement="top" 
            title="' . _l('edit') . '">
            <i class="fa fa-pencil-square"></i></a> ';
    }
    if (has_permission('purchase_invoices', '', 'delete') || is_admin()) {
        $actions .= '<a href="' . admin_url('purchase/delete_bill/' . $aRow['id']) . '" 
            class="btn btn-danger btn-icon _delete" 
            data-toggle="tooltip" 
            data-placement="top" 
            title="' . _l('delete') . '">
            <i class="fa fa-remove"></i></a>';
    }
    $row[] = $actions;

    $output['aaData'][] = $row;
}

?>