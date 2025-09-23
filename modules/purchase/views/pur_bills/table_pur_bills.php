<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'pur_bills';

$aColumns = [
    0,
    db_prefix() . 'pur_bills' . '.id as id',
    db_prefix() . 'pur_bills' . '.bill_number as bill_number',
    '(CASE 
        WHEN ' . db_prefix() . 'pur_bills.pur_order IS NOT NULL THEN 1 
        WHEN ' . db_prefix() . 'pur_bills.wo_order IS NOT NULL THEN 2
        ELSE 3 
    END) as order_type',
    db_prefix() . 'pur_vendor' . '.company as company',
    db_prefix() . 'pur_bills' . '.invoice_date as payment_certificate_date',
    db_prefix() . 'pur_bills' . '.total as total',
    db_prefix() . 'pur_bills' . '.approve_status as approve_status',
    1,
    db_prefix() . 'pur_bills' . '.last_action as last_action',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'pur_bills';
$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_orders 
    ON ' . db_prefix() . 'pur_bills.pur_order IS NOT NULL 
    AND ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_bills.pur_order',
    'LEFT JOIN ' . db_prefix() . 'wo_orders 
    ON ' . db_prefix() . 'pur_bills.wo_order IS NOT NULL 
    AND ' . db_prefix() . 'wo_orders.id = ' . db_prefix() . 'pur_bills.wo_order',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor 
    ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_bills.vendor',
];

$where = [];

$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        db_prefix() . 'pur_bills.pur_order',
        db_prefix() . 'pur_bills.wo_order',
        db_prefix() . 'pur_orders.pur_order_number as po_number',
        db_prefix() . 'wo_orders.wo_order_number as wo_number',
        db_prefix() . 'pur_bills.vendor',
    ],
    '',
    [],
    $having
);

$output  = $result['output'];
$rResult = $result['rResult'];

$aColumns = array_map(function ($col) {
    $col = trim($col);
    if (stripos($col, ' as ') !== false) {
        $parts = preg_split('/\s+as\s+/i', $col);
        return trim($parts[1], '"` ');
    }
    return trim($col, '"` ');
}, $aColumns);

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        $base_currency = get_base_currency_pur();
        if ($aRow['currency'] != 0) {
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        if ($aColumns[$i] == '0') {
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        } elseif ($aColumns[$i] == 'id') {
            $numberOutput = '';
            $numberOutput .= '<a href="' . admin_url('purchase/edit_pur_bills/' . $aRow['id']) . '" target="_blank">' . _l('edit') . '</a>';
            $numberOutput .= ' | <a href="' . admin_url('purchase/delete_bill/' . $aRow['id']) . '" class="text-danger delete_bill">' . _l('delete') . '</a>';
            $_data = $numberOutput;
        } elseif ($aColumns[$i] == 'bill_number') {
            $_data = '<a href="' . admin_url('purchase/edit_pur_bills/' . $aRow['id']) . '" target="_blank">' . $aRow['bill_number'] . '</a>';
        } elseif ($aColumns[$i] == 'order_type') {
            $_data = '';
            if ($aRow['order_type'] == 1) {
                $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pur_order']) . '" target="_blank">' . $aRow['po_number'] . '</a>';
            }
            if ($aRow['order_type'] == 2) {
                $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order']) . '" target="_blank">' . $aRow['wo_number'] . '</a>';
            }
        } elseif ($aColumns[$i] == 'company') {
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['company'] . '</a>';
        } elseif ($aColumns[$i] == 'payment_certificate_date') {
            $_data = _d($aRow['payment_certificate_date']);
        } elseif ($aColumns[$i] == 'total') {
            $_data = app_format_money($aRow['total'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'approve_status') {
            if ($aRow['approve_status'] == 1) {
                $_data = '<span class="label label-primary">' . _l('pur_draft') . '</span>';
            } else if ($aRow['approve_status'] == 2) {
                $_data = '<span class="label label-success">' . _l('approved') . '</span>';
            } else if ($aRow['approve_status'] == 3) {
                $_data = '<span class="label label-danger">' . _l('rejected') . '</span>';
            } else {
                $_data = '<span class="label label-primary">' . _l('pur_draft') . '</span>';
            }
        } elseif ($aColumns[$i] == 1) {
            $pdf = '';
            $pdf = '<div class="btn-group display-flex">';
            $pdf .= '<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>';
            $pdf .= '<ul class="dropdown-menu dropdown-menu-right">';
            $pdf .= '<li class="hidden-xs"><a href="#">' . _l('view_pdf') . '</a></li>';
            $pdf .= '<li class="hidden-xs"><a href="#" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
            $pdf .= '<li><a href="#">' . _l('download') . '</a></li>';
            $pdf .= '<li><a href="#" target="_blank">' . _l('print') . '</a></li>';
            $pdf .= '</ul>';
            $pdf .= '</div>';
            $_data = $pdf;
        } elseif ($aColumns[$i] == 'last_action') {
            $_data = get_last_action_full_name($aRow['last_action']);
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
    $sr++;
}
