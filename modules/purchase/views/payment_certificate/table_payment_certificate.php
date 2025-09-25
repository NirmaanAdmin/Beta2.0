<?php

defined('BASEPATH') or exit('No direct script access allowed');
$module_name = 'payment_certificate';
$vendors_filter_name = 'vendors';
$group_pur_filter_name = 'group_pur';
$approval_status_filter_name = 'approval_status';
$applied_to_vendor_bill_filter_name = 'applied_to_vendor_bill';
$projects_filter_name = 'projects';
$order_tagged_detail_filter_name = 'order_tagged_detail';
$res_person_filter_name = 'res_person';

$aColumns = [
    0,
    db_prefix() . 'payment_certificate' . '.id as id',
    db_prefix() . 'payment_certificate' . '.pc_number as pc_number',
    'po_id',
    db_prefix() . 'pur_vendor' . '.company as company',
    db_prefix() . 'payment_certificate' . '.order_date as order_date',
    db_prefix() . 'assets_group' . '.group_name as group_name',
    '(' . db_prefix() . 'payment_certificate.po_this_bill 
      + ' . db_prefix() . 'payment_certificate.pay_cert_c1_3 
      - ' . db_prefix() . 'payment_certificate.pay_cert_c2_3 
      - ' . db_prefix() . 'payment_certificate.ret_fund_3 
      - ' . db_prefix() . 'payment_certificate.works_exe_a_3
      - ' . db_prefix() . 'payment_certificate.less_3
      - ' . db_prefix() . 'payment_certificate.less_ah_3
      - ' . db_prefix() . 'payment_certificate.less_aht_3
    ) as this_bill',
    db_prefix() . 'payment_certificate' . '.dateadded as submission_date',
    db_prefix() . 'payment_certificate' . '.approve_status as approve_status',
    'pending_approval',
    '(CASE 
        WHEN ' . db_prefix() . 'payment_certificate.pur_invoice_id IS NOT NULL THEN 2 
        WHEN ' . db_prefix() . 'payment_certificate.approve_status = 2 AND ' . db_prefix() . 'payment_certificate.pur_invoice_id IS NULL THEN 1 
        ELSE 3 
     END) as applied_to_vendor_bill',
    db_prefix() . 'payment_certificate.pur_invoice_id as pur_invoice_id',
    1,
    db_prefix() . 'payment_certificate' . '.responsible_person as responsible_person',
    db_prefix() . 'payment_certificate' . '.last_action as last_action',
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
    'LEFT JOIN ' . db_prefix() . 'pur_invoices ON ' . db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'payment_certificate.pur_invoice_id',
    'LEFT JOIN (
        SELECT rel_id, GROUP_CONCAT(staffid) AS pending_approval
        FROM ' . db_prefix() . 'payment_certificate_details
        WHERE approve IS NULL
        GROUP BY rel_id
    ) AS pcd ON pcd.rel_id = ' . db_prefix() . 'payment_certificate.id',
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

if ($this->ci->input->post('applied_to_vendor_bill') && count($this->ci->input->post('applied_to_vendor_bill')) > 0) {
    $applied_to_vendor_bill = implode(',', $this->ci->input->post('applied_to_vendor_bill'));
    $where[] = 'AND (
        ( ' . db_prefix() . 'payment_certificate.pur_invoice_id IS NOT NULL AND 2 IN (' . $applied_to_vendor_bill . '))
        OR
        ( ' . db_prefix() . 'payment_certificate.approve_status = 2 AND ' . db_prefix() . 'payment_certificate.pur_invoice_id IS NULL AND 1 IN (' . $applied_to_vendor_bill . '))
        OR
        ( ( ' . db_prefix() . 'payment_certificate.approve_status != 2 OR ' . db_prefix() . 'payment_certificate.approve_status IS NULL ) AND ' . db_prefix() . 'payment_certificate.pur_invoice_id IS NULL AND 3 IN (' . $applied_to_vendor_bill . '))
    )';
}

$order_tagged_detail = $this->ci->input->post('order_tagged_detail');
if (isset($order_tagged_detail) && is_array($order_tagged_detail) && !empty($order_tagged_detail)) {
    $or_conditions = [];
    foreach ($order_tagged_detail as $t) {
        if (!empty($t)) {
            if (strpos($t, 'po_') === 0) {
                $id = str_replace('po_', '', $t);
                $or_conditions[] = db_prefix() . "payment_certificate.po_id = '$id'";
            } elseif (strpos($t, 'wo_') === 0) {
                $id = str_replace('wo_', '', $t);
                $or_conditions[] = db_prefix() . "payment_certificate.wo_id = '$id'";
            } elseif (strpos($t, 'ot_') === 0) {
                $id = str_replace('ot_', '', $t);
                $or_conditions[] = db_prefix() . "payment_certificate.ot_id = '$id'";
            }
        }
    }
    if (!empty($or_conditions)) {
        $where_order_tagged_detail = ' AND (' . implode(' OR ', $or_conditions) . ')';
        array_push($where, $where_order_tagged_detail);
    }
}

if ($this->ci->input->post('res_person') && count($this->ci->input->post('res_person')) > 0) {
    $persons = $this->ci->input->post('res_person');
    $conditions = [];
    foreach ($persons as $p) {
        $conditions[] = "FIND_IN_SET(" . (int)$p . ", " . db_prefix() . "payment_certificate.responsible_person)";
    }
    $where[] = "AND (" . implode(' OR ', $conditions) . ")";
}

$vendors_filter_name_value = !empty($this->ci->input->post('vendors')) ? implode(',', $this->ci->input->post('vendors')) : NULL;
update_module_filter($module_name, $vendors_filter_name, $vendors_filter_name_value);

$group_pur_filter_name_value = !empty($this->ci->input->post('group_pur')) ? implode(',', $this->ci->input->post('group_pur')) : NULL;
update_module_filter($module_name, $group_pur_filter_name, $group_pur_filter_name_value);

$approval_status_filter_name_value = !empty($this->ci->input->post('approval_status')) ? implode(',', $this->ci->input->post('approval_status')) : NULL;
update_module_filter($module_name, $approval_status_filter_name, $approval_status_filter_name_value);

$projects_filter_name_value = !empty($this->ci->input->post('projects')) ? implode(',', $this->ci->input->post('projects')) : NULL;
update_module_filter($module_name, $projects_filter_name, $projects_filter_name_value);

$applied_to_vendor_bill_filter_name_value = !empty($this->ci->input->post('applied_to_vendor_bill')) ? implode(',', $this->ci->input->post('applied_to_vendor_bill')) : NULL;
update_module_filter($module_name, $applied_to_vendor_bill_filter_name, $applied_to_vendor_bill_filter_name_value);

$order_tagged_detail_filter_name_value = !empty($this->ci->input->post('order_tagged_detail')) ? implode(',', $this->ci->input->post('order_tagged_detail')) : NULL;
update_module_filter($module_name, $order_tagged_detail_filter_name, $order_tagged_detail_filter_name_value);

$res_person_filter_name_value = !empty($this->ci->input->post('res_person')) ? implode(',', $this->ci->input->post('res_person')) : NULL;
update_module_filter($module_name, $res_person_filter_name, $res_person_filter_name_value);

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
        '(CASE 
            WHEN ' . db_prefix() . 'payment_certificate.po_id IS NOT NULL THEN ' . db_prefix() . 'pur_orders.project 
            WHEN ' . db_prefix() . 'payment_certificate.wo_id IS NOT NULL THEN ' . db_prefix() . 'wo_orders.project
            WHEN ' . db_prefix() . 'payment_certificate.ot_id IS NOT NULL THEN ' . db_prefix() . 'pur_order_tracker.project 
            ELSE NULL 
         END) as project',
         db_prefix() . 'pur_invoices.invoice_number',
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

$this->ci->load->model('Staff_model');
$staff_list   = $this->ci->Staff_model->get();

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
            if (has_permission('payment_certificate', '', 'edit') || is_admin()) {
                if (!empty($aRow['po_id'])) {
                    $numberOutput .= '<a href="' . admin_url('purchase/payment_certificate/' . $aRow['po_id'] . '/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
                }
                if (!empty($aRow['wo_id'])) {
                    $numberOutput .= '<a href="' . admin_url('purchase/wo_payment_certificate/' . $aRow['wo_id'] . '/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
                }
                if (!empty($aRow['ot_id'])) {
                    $numberOutput .= '<a href="' . admin_url('purchase/ot_payment_certificate/' . $aRow['ot_id'] . '/' . $aRow['id']) . '" target="_blank">' . _l('view') . '</a>';
                }
            }
            if (has_permission('payment_certificate', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_payment_certificate/' . $aRow['id']) . '" class="text-danger delete_payment_cert">' . _l('delete') . '</a>';
            }
            $_data = $numberOutput;
        } elseif ($aColumns[$i] == 'pc_number') {
            if (!empty($aRow['po_id'])) {
                $_data = '<a href="' . admin_url('purchase/payment_certificate/' . $aRow['po_id'] . '/' . $aRow['id']) . '" target="_blank">' . $aRow['pc_number'] . '</a>';
            }
            if (!empty($aRow['wo_id'])) {
                $_data = '<a href="' . admin_url('purchase/wo_payment_certificate/' . $aRow['wo_id'] . '/' . $aRow['id']) . '" target="_blank">' . $aRow['pc_number'] . '</a>';
            }
            if (!empty($aRow['ot_id'])) {
                $_data = '<a href="' . admin_url('purchase/ot_payment_certificate/' . $aRow['ot_id'] . '/' . $aRow['id']) . '" target="_blank">' . $aRow['pc_number'] . '</a>';
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
        } elseif ($aColumns[$i] == 'this_bill') {
            $_data = app_format_money($aRow['this_bill'], $base_currency->symbol);
        } elseif ($aColumns[$i] == 'submission_date') {
            $_data = date('d-m-Y', strtotime($aRow['submission_date']));
        } elseif ($aColumns[$i] == 'approve_status') {
            $_data = '';
            $list_approval_details = get_list_approval_details($aRow['id'], ['po_payment_certificate', 'wo_payment_certificate', 'ot_payment_certificate']);
            if (empty($list_approval_details)) {
                if ($aRow['approve_status'] == 2) {
                    $_data = '<span class="label label-success">' . _l('approved') . '</span>';
                } else if ($aRow['approve_status'] == 3) {
                    $_data = '<span class="label label-danger">' . _l('rejected') . '</span>';
                } else if (!empty($aRow['ot_id'])) {
                    $_data = '<a data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_payment_certificate_approve(' . pur_html_entity_decode($aRow['id']) . ', \'ot_payment_certificate\'); return false;">' . _l('approval_request_sent') . '</a>';
                } else {
                    $_data = '<span class="label label-primary">' . _l('approval_request_sent') . '</span>';
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
            if ($aRow['applied_to_vendor_bill'] == 1) {
                $_data = '<a href="' . admin_url('purchase/convert_pur_invoice_from_po/' . $aRow['id']) . '" class="btn btn-info convert-pur-invoice" data-url="' . admin_url('purchase/convert_pur_invoice_from_po/' . $aRow['id']) . '">' . _l('convert_to_vendor_bill') . '
                </a>';
            } else if($aRow['applied_to_vendor_bill'] == 2) {
                $_data = '<span class="btn btn-success">Converted</span>';
            } else if($aRow['applied_to_vendor_bill'] == 3) {
                $_data = '<span class="btn btn-warning">Pending</span>';
            } else {
                $_data = '';
            }
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
        } elseif ($aColumns[$i] == 'last_action') {
            $_data = get_last_action_full_name($aRow['last_action']);
        } elseif ($aColumns[$i] == 'responsible_person') {
            $_data = '';
            $staff_html = '<select class="form-control responsible_person selectpicker" multiple data-live-search="true" data-width="100%" name="responsible_person[]" data-id="' . $aRow['id'] . '">';
            $saved_responsible = !empty($aRow['responsible_person']) ? explode(',', $aRow['responsible_person']) : [];
            foreach ($staff_list as $st) {
                $selected = (is_array($saved_responsible) && in_array($st['staffid'], $saved_responsible)) ? ' selected' : '';
                $staff_html .= '<option value="' . $st['staffid'] . '"' . $selected . '>'
                    . html_escape($st['firstname'] . ' ' . $st['lastname'])
                    . '</option>';
            }
            $staff_html .= '</select>';
            $_data = $staff_html;
        } elseif ($aColumns[$i] == 'pending_approval') {
            $_data = get_multiple_staff_names($aRow['pending_approval']);
        } elseif ($aColumns[$i] == 'pur_invoice_id') {
            $_data = '';
            if(!empty($aRow['pur_invoice_id'])) {
                $_data = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['pur_invoice_id']) . '" target="_blank">' . $aRow['invoice_number'] . '</a>';
            }
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
