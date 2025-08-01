<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'payment_certificate';
$vendors_filter_name = 'vendors';
$group_pur_filter_name = 'group_pur';
$approval_status_filter_name = 'approval_status';
$projects_filter_name = 'projects';

$aColumns = [
    db_prefix() . 'payment_certificate' . '.id as id',
    '(CASE 
        WHEN ' . db_prefix() . 'payment_certificate.po_id IS NOT NULL THEN ' . db_prefix() . 'pur_orders.project 
        WHEN ' . db_prefix() . 'payment_certificate.wo_id IS NOT NULL THEN ' . db_prefix() . 'wo_orders.project
        WHEN ' . db_prefix() . 'payment_certificate.ot_id IS NOT NULL THEN ' . db_prefix() . 'pur_order_tracker.project 
        ELSE NULL 
     END) as project',
    'po_id',
    db_prefix() . 'pur_vendor' . '.company as company',
    db_prefix() . 'payment_certificate' . '.order_date as order_date',
    db_prefix() . 'assets_group' . '.group_name as group_name',
    db_prefix() . 'payment_certificate' . '.approve_status as approve_status',
    db_prefix() . 'payment_certificate' . '.pur_invoice_id as applied_to_vendor_bill',
    1,
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'payment_certificate';
$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_orders 
    ON ' . db_prefix() . 'payment_certificate.po_id IS NOT NULL 
    AND ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'payment_certificate.po_id',
    'LEFT JOIN ' . db_prefix() . 'wo_orders 
    ON ' . db_prefix() . 'payment_certificate.wo_id IS NOT NULL 
    AND ' . db_prefix() . 'wo_orders.id = ' . db_prefix() . 'payment_certificate.wo_id',
    'LEFT JOIN ' . db_prefix() . 'pur_order_tracker 
    ON ' . db_prefix() . 'payment_certificate.ot_id IS NOT NULL 
    AND ' . db_prefix() . 'pur_order_tracker.id = ' . db_prefix() . 'payment_certificate.ot_id',
    'LEFT JOIN ' . db_prefix() . 'pur_vendor 
    ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'payment_certificate.vendor',
    'LEFT JOIN ' . db_prefix() . 'assets_group ON ' . db_prefix() . 'assets_group.group_id = ' . db_prefix() . 'payment_certificate.group_pur',
];

$where = [];
if ($this->ci->input->post('vendors') && count($this->ci->input->post('vendors')) > 0) {
    $vendors = implode(',', $this->ci->input->post('vendors'));
    $where_vendors = 'AND (
        (' . db_prefix() . 'payment_certificate.po_id IS NOT NULL AND ' . db_prefix() . 'pur_orders.vendor IN (' . $vendors . '))
        OR
        (' . db_prefix() . 'payment_certificate.wo_id IS NOT NULL AND ' . db_prefix() . 'wo_orders.vendor IN (' . $vendors . '))
        OR
        (' . db_prefix() . 'payment_certificate.ot_id IS NOT NULL AND ' . db_prefix() . 'pur_order_tracker.vendor IN (' . $vendors . '))
    )';
    array_push($where, $where_vendors);
}

if ($this->ci->input->post('group_pur') && count($this->ci->input->post('group_pur')) > 0) {
    $group_pur = implode(',', $this->ci->input->post('group_pur'));
    $where_group_pur = 'AND (
        (' . db_prefix() . 'payment_certificate.po_id IS NOT NULL AND ' . db_prefix() . 'pur_orders.group_pur IN (' . $group_pur . '))
        OR
        (' . db_prefix() . 'payment_certificate.wo_id IS NOT NULL AND ' . db_prefix() . 'wo_orders.group_pur IN (' . $group_pur . '))
        OR
        (' . db_prefix() . 'payment_certificate.ot_id IS NOT NULL AND ' . db_prefix() . 'pur_order_tracker.group_pur IN (' . $group_pur . '))
    )';
    array_push($where, $where_group_pur);
}

if ($this->ci->input->post('approval_status') && count($this->ci->input->post('approval_status')) > 0) {
    array_push($where, 'AND (' . db_prefix() . 'payment_certificate.approve_status IN (' . implode(',', $this->ci->input->post('approval_status')) . '))');
}
if ($this->ci->input->post('projects') && count($this->ci->input->post('projects')) > 0) {
    $projects = implode(',', $this->ci->input->post('projects'));
    $where_projects = 'AND (
        (' . db_prefix() . 'payment_certificate.po_id IS NOT NULL AND ' . db_prefix() . 'pur_orders.project IN (' . $projects . '))
        OR
        (' . db_prefix() . 'payment_certificate.wo_id IS NOT NULL AND ' . db_prefix() . 'wo_orders.project IN (' . $projects . '))
        OR
        (' . db_prefix() . 'payment_certificate.ot_id IS NOT NULL AND ' . db_prefix() . 'pur_order_tracker.project IN (' . $projects . '))
    )';
    array_push($where, $where_projects);
}

$vendors_filter_name_value = !empty($this->ci->input->post('vendors')) ? implode(',', $this->ci->input->post('vendors')) : NULL;
update_module_filter($module_name, $vendors_filter_name, $vendors_filter_name_value);

$group_pur_filter_name_value = !empty($this->ci->input->post('group_pur')) ? implode(',', $this->ci->input->post('group_pur')) : NULL;
update_module_filter($module_name, $group_pur_filter_name, $group_pur_filter_name_value);

$approval_status_filter_name_value = !empty($this->ci->input->post('approval_status')) ? implode(',', $this->ci->input->post('approval_status')) : NULL;
update_module_filter($module_name, $approval_status_filter_name, $approval_status_filter_name_value);

$projects_filter_name_value = !empty($this->ci->input->post('projects')) ? implode(',', $this->ci->input->post('projects')) : NULL;
update_module_filter($module_name, $projects_filter_name, $projects_filter_name_value);

$having = '';

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    [
        db_prefix() . 'payment_certificate.id',
        db_prefix() . 'payment_certificate.po_id',
        db_prefix() . 'payment_certificate.wo_id',
        db_prefix() . 'payment_certificate.ot_id',
        db_prefix() . 'payment_certificate.approve_status',
        db_prefix() . 'wo_orders.wo_order_number as wo_number',
        db_prefix() . 'pur_orders.pur_order_number as po_number',
        db_prefix() . 'pur_order_tracker.pur_order_name as ot_number',
        db_prefix() . 'payment_certificate.vendor',
        db_prefix() . 'payment_certificate.group_pur',
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

$this->ci->load->model('purchase/purchase_model');
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        $base_currency = get_base_currency_pur();
        if ($aRow['currency'] != 0) {
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        if ($aColumns[$i] == 'id') {
            $_data = '';
            if (!empty($aRow['po_id'])) {
                $_data = '<a href="' . admin_url('purchase/payment_certificate/' . $aRow['po_id'] . '/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
            }
            if (!empty($aRow['wo_id'])) {
                $_data = '<a href="' . admin_url('purchase/wo_payment_certificate/' . $aRow['wo_id'] . '/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
            }
            if (!empty($aRow['ot_id'])) {
                $_data = '<a href="' . admin_url('purchase/ot_payment_certificate/' . $aRow['ot_id'] . '/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
            }
        } elseif ($aColumns[$i] == 'po_id') {
            $_data = '';
            if (!empty($aRow['po_id'])) {
                $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['po_id']) . '" target="_blank">' . $aRow['po_number'] . '</a>';
            }
            if (!empty($aRow['wo_id'])) {
                $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_id']) . '" target="_blank">' . $aRow['wo_number'] . '</a>';
            }
            if (!empty($aRow['ot_id'])) {
                $_data = $aRow['ot_number'];
            }
        } elseif ($aColumns[$i] == 'company') {
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" >' . $aRow['company'] . '</a>';
        } elseif ($aColumns[$i] == 'order_date') {
            $_data = _d($aRow['order_date']);
        } elseif ($aColumns[$i] == 'group_name') {
            $_data = $aRow['group_name'];
        } elseif ($aColumns[$i] == 'approve_status') {
            $_data = '';
            $list_approval_details = get_list_approval_details($aRow['id'], ['po_payment_certificate', 'wo_payment_certificate', 'ot_payment_certificate']);
            if (empty($list_approval_details)) {
                if ($aRow['approve_status'] == 2) {
                    $_data = '<span class="label label-success">' . _l('approved') . '</span>';
                } else if ($aRow['approve_status'] == 3) {
                    $_data = '<span class="label label-danger">' . _l('rejected') . '</span>';
                } else if (!empty($aRow['ot_id'])) {
                    $_data = '<a data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_payment_certificate_approve(' . pur_html_entity_decode($aRow['id']) . ', \'ot_payment_certificate\'); return false;">' . _l('send_request_approve_pur') . '</a>';
                } else {
                    $_data = '<span class="label label-primary">' . _l('send_request_approve_pur') . '</span>';
                }
            } else if ($aRow['approve_status'] == 1) {
                $_data = '<span class="label label-primary">' . _l('pur_draft') . '</span>';
            } else if ($aRow['approve_status'] == 2) {
                $_data = '<span class="label label-success">' . _l('approved') . '</span>';
            } else if ($aRow['approve_status'] == 3) {
                $_data = '<span class="label label-danger">' . _l('rejected') . '</span>';
            } else {
                $_data = '';
            }
        } elseif ($aColumns[$i] == 'applied_to_vendor_bill') {
            $_data = '';
            if ($aRow['approve_status'] == 2 && empty($aRow['applied_to_vendor_bill'])) {
                $_data = '<a href="' . admin_url('purchase/convert_pur_invoice_from_po/' . $aRow['id']) . '" class="btn btn-info convert-pur-invoice" data-url="' . admin_url('purchase/convert_pur_invoice_from_po/' . $aRow['id']) . '">' . _l('convert_to_vendor_bill') . '
                </a>';
            } else if(!empty($aRow['applied_to_vendor_bill'])) {
                $_data = '<span class="btn btn-success">Converted</span>';
            }
        } elseif ($aColumns[$i] == 'project') {
            $_data = get_project_name_by_id($aRow['project']);
        } elseif ($aColumns[$i] == 1) {
            $pdf = '';
            $pdf = '<div class="btn-group display-flex">';
            $pdf .= '<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf"></i><span class="caret"></span></a>';
            $pdf .= '<ul class="dropdown-menu dropdown-menu-right">';
            $pdf .= '<li class="hidden-xs"><a href="' . admin_url('purchase/payment_certificate_pdf/' . $aRow['id'] . '?output_type=I') . '">' . _l('view_pdf') . '</a></li>';
            $pdf .= '<li class="hidden-xs"><a href="' . admin_url('purchase/payment_certificate_pdf/' . $aRow['id'] . '?output_type=I') . '" target="_blank">' . _l('view_pdf_in_new_window') . '</a></li>';
            $pdf .= '<li><a href="' . admin_url('purchase/payment_certificate_pdf/' . $aRow['id']) . '">' . _l('download') . '</a></li>';
            $pdf .= '<li><a href="' . admin_url('purchase/payment_certificate_pdf/' . $aRow['id'] . '?print=true') . '" target="_blank">' . _l('print') . '</a></li>';
            $pdf .= '</ul>';
            $pdf .= '</div>';
            $_data = $pdf;
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
