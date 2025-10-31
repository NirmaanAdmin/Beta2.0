<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'pur_bills';
$vendors_filter_name = 'vendors';
$approval_status_filter_name = 'approval_status';
$order_tagged_detail_filter_name = 'order_tagged_detail';

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
    db_prefix() . 'pur_bills' . '.date_add as date_add',
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
if ($this->ci->input->post('vendors') && count($this->ci->input->post('vendors')) > 0) {
    $vendors = implode(',', $this->ci->input->post('vendors'));
    $where_vendors = 'AND ' . db_prefix() . "pur_bills.vendor IN (" . $vendors . ")";
    array_push($where, $where_vendors);
}

if ($this->ci->input->post('approval_status') && count($this->ci->input->post('approval_status')) > 0) {
    array_push($where, 'AND (' . db_prefix() . 'pur_bills.approve_status IN (' . implode(',', $this->ci->input->post('approval_status')) . '))');
}

$order_tagged_detail = $this->ci->input->post('order_tagged_detail');
if (isset($order_tagged_detail) && is_array($order_tagged_detail) && !empty($order_tagged_detail)) {
    $or_conditions = [];
    foreach ($order_tagged_detail as $t) {
        if (!empty($t)) {
            if (strpos($t, 'po_') === 0) {
                $id = str_replace('po_', '', $t);
                $or_conditions[] = db_prefix() . "pur_bills.pur_order = '$id'";
            } elseif (strpos($t, 'wo_') === 0) {
                $id = str_replace('wo_', '', $t);
                $or_conditions[] = db_prefix() . "pur_bills.wo_order = '$id'";
            } elseif (strpos($t, 'ot_') === 0) {
                $id = str_replace('ot_', '', $t);
                $or_conditions[] = db_prefix() . "pur_bills.order_tracker_id = '$id'";
            }
        }
    }
    if (!empty($or_conditions)) {
        $where_order_tagged_detail = ' AND (' . implode(' OR ', $or_conditions) . ')';
        array_push($where, $where_order_tagged_detail);
    }
}

$vendors_filter_name_value = !empty($this->ci->input->post('vendors')) ? implode(',', $this->ci->input->post('vendors')) : NULL;
update_module_filter($module_name, $vendors_filter_name, $vendors_filter_name_value);

$approval_status_filter_name_value = !empty($this->ci->input->post('approval_status')) ? implode(',', $this->ci->input->post('approval_status')) : NULL;
update_module_filter($module_name, $approval_status_filter_name, $approval_status_filter_name_value);

$order_tagged_detail_filter_name_value = !empty($this->ci->input->post('order_tagged_detail')) ? implode(',', $this->ci->input->post('order_tagged_detail')) : NULL;
update_module_filter($module_name, $order_tagged_detail_filter_name, $order_tagged_detail_filter_name_value);

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
        db_prefix() . 'pur_orders.pur_order_number',
        db_prefix() . 'pur_orders.pur_order_name',
        db_prefix() . 'wo_orders.wo_order_number',
        db_prefix() . 'wo_orders.wo_order_name',
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
            if (has_permission('bill_bifurcation', '', 'edit') || is_admin()) {
                $numberOutput .= '<a href="' . admin_url('purchase/edit_pur_bills/' . $aRow['id']) . '" target="_blank">' . _l('edit') . '</a>';
            }
            if (has_permission('bill_bifurcation', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_bill/' . $aRow['id']) . '" class="text-danger delete_bill">' . _l('delete') . '</a>';
            }
            $_data = $numberOutput;
        } elseif ($aColumns[$i] == 'bill_number') {
            $_data = '<a href="' . admin_url('purchase/edit_pur_bills/' . $aRow['id']) . '" target="_blank">' . $aRow['bill_number'] . '</a>';
        } elseif ($aColumns[$i] == 'order_type') {
            $_data = '';
            if ($aRow['order_type'] == 1) {
                $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pur_order']) . '" target="_blank">' . pur_html_entity_decode($aRow['pur_order_number'] . ' - ' . $aRow['pur_order_name']) . '</a>';
            }
            if ($aRow['order_type'] == 2) {
                $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order']) . '" target="_blank">' . pur_html_entity_decode($aRow['wo_order_number'] . ' - ' . $aRow['wo_order_name']) . '</a>';
            }
        } elseif ($aColumns[$i] == 'company') {
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['company'] . '</a>';
        } elseif ($aColumns[$i] == 'date_add') {
            $_data = _d($aRow['date_add']);
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
            $pdf .= '<li class="hidden-xs"><a href="' . admin_url('purchase/bill_bifurcation_pdf/' . $aRow['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>';
            $pdf .= '<li class="hidden-xs"><a href="' . admin_url('purchase/bill_bifurcation_pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
            $pdf .= '<li><a href="' . admin_url('purchase/bill_bifurcation_pdf/' . $aRow['id']) . '">' . _l('download') . '</a></li>';
            $pdf .= '<li><a href="' . admin_url('purchase/bill_bifurcation_pdf/' . $aRow['id'] . '?print=true') . '" target="_blank">' . _l('print') . '</a></li>';
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
