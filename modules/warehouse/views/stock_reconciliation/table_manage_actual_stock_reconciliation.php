<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'stock_reconciliation.goods_delivery_code',   // Voucher Code
    db_prefix() . 'stock_reconciliation_detail.commodity_name',
    db_prefix() . 'stock_reconciliation_detail.description',
    db_prefix() . 'stock_reconciliation_detail.area',
    db_prefix() . 'stock_reconciliation_detail.warehouse_id',
    1,                                         // TEMP placeholder
    db_prefix() . 'stock_reconciliation_detail.received_quantity',
    db_prefix() . 'stock_reconciliation_detail.issued_quantities',
    db_prefix() . 'stock_reconciliation_detail.returnable_date',
    2,                                              // TEMP placeholder
    db_prefix() . 'stock_reconciliation_detail.reconciliation_date',
    db_prefix() . 'stock_reconciliation_detail.return_quantity',
    db_prefix() . 'stock_reconciliation_detail.used_quantity',
    db_prefix() . 'stock_reconciliation_detail.location',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'stock_reconciliation';
$join         = ['LEFT JOIN ' . db_prefix() . 'stock_reconciliation_detail ON ' . db_prefix() . 'stock_reconciliation.id = ' . db_prefix() . 'stock_reconciliation_detail.goods_delivery_id'];
$where = [];




if (get_default_project()) {
    $where[] = 'AND ' . db_prefix() . 'stock_reconciliation.project = ' . get_default_project() . '';
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'stock_reconciliation.id']);

$output  = $result['output'];
$rResult = $result['rResult'];
// echo '<pre>'; print_r($rResult); exit;
foreach ($rResult as $aRow) {

    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

         if($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.area'){
            $name = get_area_name_by_id($aRow[db_prefix() . 'stock_reconciliation_detail.area']);

            $_data = $name;
         }

         if($aColumns[$i] == db_prefix() . 'stock_reconciliation_detail.warehouse_id'){
            $name = get_warehouse_name($aRow[db_prefix() . 'stock_reconciliation_detail.warehouse_id']);
            $_data = $name->warehouse_name;
         }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
