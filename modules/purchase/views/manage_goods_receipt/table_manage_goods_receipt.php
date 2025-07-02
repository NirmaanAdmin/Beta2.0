<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_receipt_code',
    'pr_order_id',
    'supplier_name',
    'kind',
    'date_add',
    'buyer_id',
    'delivery_status',
];
$join = [];
$where = [];

if ($this->ci->input->post('day_vouchers')) {
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if ($this->ci->input->post('kind')) {
    $kind = $this->ci->input->post('kind');
}

if ($this->ci->input->post('delivery')) {
    $delivery = $this->ci->input->post('delivery');
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

if (isset($delivery)) {
    if ($delivery == "undelivered") {
        $where[] = 'AND delivery_status = "0"';
    } else if ($delivery == "partially_delivered") {
        $where[] = 'AND delivery_status = "1"';
    } else if ($delivery == "completely_delivered") {
        $where[] = 'AND delivery_status = "2"';
    } else {
        $where[] = 'AND delivery_status = "0"';
    }
}

if ($this->ci->input->post('vendors')
    && count($this->ci->input->post('vendors')) > 0) {
    $where[] = 'AND supplier_name IN (' . implode(',', $this->ci->input->post('vendors')) . ')';
}

if(get_default_project()) {
    $where[] = 'AND project = "' . get_default_project() . '"';
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
        } elseif ($aColumns[$i] == 'date_add') {
            $_data = date('d M, Y', strtotime($aRow['date_add']));
        } elseif ($aColumns[$i] == 'goods_receipt_code') {
            $name = '';
            if (!empty($aRow['goods_receipt_code'])) {
                $name .= '<a href="' . admin_url('purchase/view_purchase/' . $aRow['id']) . '" 
                onclick="init_goods_receipt(' . $aRow['id'] . '); small_table_full_view(); return false;">' .
                    $aRow['goods_receipt_code'] . '</a>';
            } else {
                $name .= '<a href="' . admin_url('purchase/view_po_tracker/' . $aRow['id']) . '" onclick="init_po_tracker(' . $aRow['id'] . '); small_table_full_view(); return false;">' . _l('Update') . '</a>';
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
        } elseif ($aColumns[$i] == 'buyer_id') {
            if ($aRow['type'] != 2) {

                $get_production_status = get_production_status($aRow['id']);
                $_data = $get_production_status;
            }else{
                $_data = '<span class="inline-block label label-danger">Not Started</span>';
            }
        } elseif ($aColumns[$i] == 'delivery_status') {
            $delivery_status = '';
            if ($aRow['delivery_status'] == 0) {
                $delivery_status = '<span class="inline-block label label-danger" task-status-table="undelivered">' . _l('undelivered');
            } else if ($aRow['delivery_status'] == 1) {
                $delivery_status = '<span class="inline-block label label-warning" task-status-table="partially_delivered">' . _l('partially_delivered');
            } else {
                $delivery_status = '<span class="inline-block label label-success" task-status-table="completely_delivered">' . _l('completely_delivered');
            }
            $_data = $delivery_status;
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
