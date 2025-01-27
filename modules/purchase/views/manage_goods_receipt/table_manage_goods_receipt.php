<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_receipt_code',
    'pr_order_id',
    'supplier_name',
    'buyer_id',
    'kind',
    'date_add',
    'approval',
];
$join = [];
$where = [];

if ($this->ci->input->post('day_vouchers')) {
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if ($this->ci->input->post('kind')) {
    $kind = $this->ci->input->post('kind');
}

if ($this->ci->input->post('toggle-filter')) {
    $where[] = 'AND type = 2';
}

if (isset($day_vouchers)) {
    $where[] = 'AND date_add <= "' . $day_vouchers . '"';
}

if (isset($kind)) {
    $where[] = 'AND kind = "' . $kind . '"';
}

$result = data_tables_purchase_tracker_init($aColumns, $join, $where, [
   'type',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'supplier_name') {
            $_data = wh_get_vendor_company_name($aRow['supplier_name']);
        } elseif ($aColumns[$i] == 'buyer_id') {
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['buyer_id']) . '">' . staff_profile_image($aRow['buyer_id'], [
                'staff-profile-image-small',
            ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['buyer_id']) . '">' . get_staff_full_name($aRow['buyer_id']) . '</a>';
        } elseif ($aColumns[$i] == 'date_add') {
            $_data = _d($aRow['date_add']);
        } elseif($aColumns[$i] == 'goods_receipt_code') {
            $name = '';
            if(!empty($aRow['goods_receipt_code'])) {
                $name .= '<a href="' . admin_url('warehouse/view_purchase/' . $aRow['id']) . '" onclick="init_goods_receipt(' . $aRow['id'] . '); return false;">' . $aRow['goods_receipt_code'] . '</a>';
                $name .= '<div class="row-options">';
                $name .= '<a href="' . admin_url('warehouse/edit_purchase/' . $aRow['id']) . '" >' . _l('view') . '</a>';
                $name .= '</div>';
            }
            $_data = $name;
        } elseif ($aColumns[$i] == 'pr_order_id') {
            $name = '';
            if ($aRow['type'] == 2) {
                if (($aRow['id'] != '') && ($aRow['id'] != 0)) {
                    $name = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; display: inline-block;">' . get_pur_order_name($aRow['id']) . '</a>';
                }
            } else {
                if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                    $name = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; display: inline-block;">' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                }
            }
            $_data = $name;
        } elseif ($aColumns[$i] == 'approval') {
            $status = 0;
            if ($aRow['type'] == 1) {
                $po_quantities = get_sum_goods_receipt_po_quantities($aRow['id']);
                $quantities = get_sum_goods_receipt_quantities($aRow['id']);
                $remaining = $po_quantities - $quantities;
                if($po_quantities == $quantities) {
                    $status = 2;
                } else if($quantities == 0) {
                    $status = 0;
                } else if($quantities > 0) {
                    $status = 1;
                }  else {
                    $status = 0;
                }
            }
            $delivery_status = '';
            if($status == 0) {
                $delivery_status = '<span class="inline-block label label-danger" task-status-table="undelivered">'._l('undelivered');
            } else if($status == 1) {
                $delivery_status = '<span class="inline-block label label-warning" task-status-table="partially_delivered">'._l('partially_delivered');
            } else {
                $delivery_status = '<span class="inline-block label label-success" task-status-table="completely_delivered">'._l('completely_delivered');
            }
            $_data = $delivery_status;
        } 

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
