<?php
defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency_pur();

$select = [
    'CASE 
        WHEN gd.pr_order_id IS NOT NULL THEN gd.pr_order_id 
        WHEN gd.wo_order_id IS NOT NULL THEN gd.wo_order_id 
        ELSE NULL 
     END as order_id',
    'gd.goods_delivery_code',
    'gdd.commodity_name',
    'gdd.description',
    'CASE 
        WHEN gd.pr_order_id IS NOT NULL THEN po.vendor 
        WHEN gd.wo_order_id IS NOT NULL THEN wo.vendor 
        ELSE NULL 
     END as vendor_id',
    'CAST(gdd.quantities AS DECIMAL(10,2))',
    'gd.date_add',
    'gdd.returnable',
    'srd.id as returned',
    'srd.returnable_date',
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'goods_delivery gd ON gd.id = gdd.goods_delivery_id',
    'LEFT JOIN ' . db_prefix() . 'pur_orders po ON po.id = gd.pr_order_id',
    'LEFT JOIN ' . db_prefix() . 'wo_orders wo ON wo.id = gd.wo_order_id',
    'LEFT JOIN ' . db_prefix() . 'stock_reconciliation sr ON sr.pr_order_id = gd.pr_order_id',
    'LEFT JOIN ' . db_prefix() . 'stock_reconciliation_detail srd ON 
        srd.goods_delivery_id = sr.id 
        AND srd.commodity_code = gdd.commodity_code 
        AND REPLACE(REPLACE(REPLACE(REPLACE(srd.description, "\r", ""), "\n", ""), "<br />", ""), "<br/>", "") = 
            REPLACE(REPLACE(REPLACE(REPLACE(gdd.description, "\r", ""), "\n", ""), "<br />", ""), "<br/>", "")'
];

$where = [];
$where[] = 'AND (gd.goods_delivery_code IS NOT NULL)';

$additionalSelect = [
    'gd.id as goods_delivery_id',
    'CASE 
        WHEN gd.pr_order_id IS NOT NULL THEN pur_order_number
        WHEN gd.wo_order_id IS NOT NULL THEN wo_order_number
        ELSE NULL 
     END as order_number',
    'CASE 
        WHEN gd.pr_order_id IS NOT NULL THEN "po" 
        WHEN gd.wo_order_id IS NOT NULL THEN "wo"
        ELSE NULL 
     END as source_table',
     'CAST(gdd.quantities AS DECIMAL(10,2)) AS quantities',
];

$sIndexColumn = 'gdd.id';
$sTable       = db_prefix() . 'goods_delivery_detail gdd';

$result  = data_tables_init($select, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    if($aRow['source_table'] == "po") {
        $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    } else {
        $row[] = '<a href="' . admin_url('purchase/work_order/' . $aRow['order_id']) . '" target="_blank">'.$aRow['order_number']. '</a>';
    }
    $row[] = '<a href="' . admin_url('warehouse/goods_delivery/' . $aRow['goods_delivery_id']) . '" target="_blank">'.$aRow['goods_delivery_code']. '</a>';
    $row[] = $aRow['commodity_name'];
    $row[] = $aRow['description'];
    $row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor_id']) . '" target="_blank">' . wh_get_vendor_company_name($aRow['vendor_id']) . '</a>';
    $row[] = $aRow['quantities'];
    $row[] = _d($aRow['date_add']);
    $row[] = $aRow['returnable'] == 1 ? 'Yes' : 'No';
    $row[] = !empty($aRow['returned']) ? 'Yes' : 'No';
    $returnable_date = "";
    if(!empty($aRow['returnable_date'])) {
        $returnable_date = json_decode($aRow['returnable_date'], true);
        $returnable_date = array_values(array_filter($returnable_date, function($value) {
            return !empty($value);
        }));
        if(!empty($returnable_date)) {
            $returnable_date = implode('<br>', $returnable_date);
        }
    }
    $row[] = $returnable_date;
    
    $output['aaData'][] = $row;
}

?>
