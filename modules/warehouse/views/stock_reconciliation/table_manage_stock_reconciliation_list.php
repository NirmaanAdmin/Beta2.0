<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'goods_delivery_code',
    1,
    'date_add',
    'delivery_status'
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'stock_reconciliation';
$join         = [];
$where = [];




if (get_default_project()) {
    $where[] = 'AND ' . db_prefix() . 'stock_reconciliation.project = ' . get_default_project() . '';
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'date_add', 'date_c', 'goods_delivery_code', 'pr_order_id', 'wo_order_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'date_add') {
            $_data = date('d M, Y', strtotime($aRow['date_add']));
        } elseif ($aColumns[$i] == 'goods_delivery_code') {
            $name = '<a href="' . admin_url('warehouse/view_purchase/' . $aRow['id']) . '" onclick="init_goods_receipt(' . $aRow['id'] . '); return false;">' . $aRow['goods_delivery_code'] . '</a>';

            $_data = $name;
        } elseif ($aColumns[$i] == 'delivery_status') {

           $_data = _l($aRow['delivery_status']);
        }  elseif ($aColumns[$i] == 1) {
            $get_order_name = '';
            if (get_status_modules_wh('purchase')) {
                if (($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0)) {
                    $get_order_name .= '<a href="' . admin_url('purchase/purchase_order/' . $aRow['pr_order_id']) . '" >' . get_pur_order_name($aRow['pr_order_id']) . '</a>';
                }
            }
            if (get_status_modules_wh('purchase')) {
                if (($aRow['wo_order_id'] != '') && ($aRow['wo_order_id'] != 0)) {
                    $get_order_name .= '<a href="' . admin_url('purchase/work_order/' . $aRow['wo_order_id']) . '" >' . get_wo_order_name($aRow['wo_order_id']) . '</a>';
                }
            }

            $_data = $get_order_name;
        }



        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
