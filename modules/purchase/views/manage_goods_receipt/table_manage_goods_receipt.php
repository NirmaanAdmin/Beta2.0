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
    'last_action',
    'type',
    'wo_order_id', // Needed for WO links
];
$join = [];
$where = [];

$ci = &get_instance();

$day_vouchers = $ci->input->post('day_vouchers') ? to_sql_date($ci->input->post('day_vouchers')) : null;
$kind = $ci->input->post('kind');
$delivery = $ci->input->post('delivery');
$vendors = $ci->input->post('vendors');
$wo_po_orders = $ci->input->post('wo_po_order') ? $ci->input->post('wo_po_order') : [];
if ($ci->input->post('toggle-filter')) {
    $where[] = 'AND type IN (2, 3)';
}

if ($day_vouchers) {
    $where[] = 'AND date_add <= "' . $day_vouchers . '"';
}

if ($kind) {
    $where[] = 'AND kind = "' . $kind . '"';
}

if ($delivery !== null) {
    switch ($delivery) {
        case 'undelivered':
            $where[] = 'AND delivery_status = "0"';
            break;
        case 'partially_delivered':
            $where[] = 'AND delivery_status = "1"';
            break;
        case 'completely_delivered':
            $where[] = 'AND delivery_status = "2"';
            break;
        default:
            $where[] = 'AND delivery_status = "0"';
    }
}

if ($vendors && count($vendors) > 0) {
    $escaped_vendors = array_map(function ($v) {
        return "'" . trim($v) . "'";
    }, $vendors);
    $where[] = 'AND supplier_name IN (' . implode(',', $escaped_vendors) . ')';
}

$project_id = get_default_project();
if ($project_id) {
    $where[] = 'AND project = "' . $project_id . '"';
}

if (!empty($wo_po_orders)) {
    $where_conditions = [];
    foreach ($wo_po_orders as $order_value) {
        // Split the value into type and id (format: "type-id")
        $parts = explode('-', $order_value);
        if (count($parts) === 3) {
            $order_type = (int)$parts[1];
            $order_id = (int)$parts[0];
            
            if ($order_type === 2) { // Purchase Order
                $where_conditions[] = '(pr_order_id = ' . $order_id . ' AND type = 2)';
            } elseif ($order_type === 3) { // Work Order
                $where_conditions[] = '(wo_order_id = ' . $order_id . ' AND type = 3)';
            }
        }
    }
    if (!empty($where_conditions)) {
        $where[] = 'AND (' . implode(' OR ', $where_conditions) . ')';
    }
}
$result = data_tables_purchase_tracker_init($aColumns, $join, $where, ['type']);
$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    foreach ($aColumns as $col) {
        $_data = $aRow[$col];

        switch ($col) {
            case 'supplier_name':
                $_data = wh_get_vendor_company_name($_data);
                break;

            case 'date_add':
                $_data = date('d M, Y', strtotime($_data));
                break;

            case 'goods_receipt_code':
                if (!empty($_data)) {
                    $_data = '<a href="' . admin_url('purchase/view_purchase/' . $aRow['id']) . '" 
                        onclick="init_goods_receipt(' . $aRow['id'] . '); small_table_full_view(); return false;">' . $_data . '</a>';
                } else {
                    if ($aRow['type'] == 2) {
                        $_data = '<a href="' . admin_url('purchase/view_po_tracker/' . $aRow['id']) . '" 
                            onclick="init_po_tracker(' . $aRow['id'] . '); small_table_full_view(); return false;">' . _l('Update') . '</a>';
                    } elseif ($aRow['type'] == 3) {
                        $_data = '<a href="' . admin_url('purchase/view_wo_tracker/' . $aRow['id']) . '" 
                            onclick="init_wo_tracker(' . $aRow['id'] . '); small_table_full_view(); return false;">' . _l('Update') . '</a>';
                    }
                }
                break;

            case 'pr_order_id':
                if ($aRow['type'] == 2) {
                    $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '">' . get_pur_order_name($aRow['id']) . '</a>';
                } elseif ($aRow['type'] == 3) {
                    $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow['id']) . '">' . get_work_order_name($aRow['id']) . '</a>';
                } else {
                    if (!empty($aRow['pr_order_id'])) {
                        $_data = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '">' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                    } elseif (!empty($aRow['wo_order_id'])) {
                        $_data = '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order_id']) . '">' . get_work_order_name($aRow['wo_order_id']) . '</a>';
                    }
                }
                break;

            case 'buyer_id':
                $_data = ($aRow['type'] == 1) ? get_production_status($aRow['id']) : '<span class="inline-block label label-danger">Not Started</span>';
                break;

            case 'delivery_status':
                if ($_data == 0) {
                    $_data = '<span class="inline-block label label-danger" task-status-table="undelivered">' . _l('undelivered') . '</span>';
                } elseif ($_data == 1) {
                    $_data = '<span class="inline-block label label-warning" task-status-table="partially_delivered">' . _l('partially_delivered') . '</span>';
                } else {
                    $_data = '<span class="inline-block label label-success" task-status-table="completely_delivered">' . _l('completely_delivered') . '</span>';
                }
                break;
            case 'last_action':
                $_data = get_last_action_full_name($aRow['last_action']);
                break;
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
